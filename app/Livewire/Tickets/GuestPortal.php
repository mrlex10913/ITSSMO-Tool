<?php

namespace App\Livewire\Tickets;

use App\Events\TicketChanged;
use App\Models\Department;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketAttachment;
use App\Models\Helpdesk\TicketCategory;
use App\Services\Helpdesk\SlaResolver;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')]
class GuestPortal extends Component
{
    use WithFileUploads;

    public string $full_name = '';

    public string $id_number = '';

    public string $school_email = '';

    public string $subject = '';

    public string $description = '';

    public ?int $category_id = null; // actual DB category id (auto-filled)

    public string $category_choice = ''; // ui select: account_access|others

    public string $priority_new = 'medium';

    public ?string $verification_option = null; // id_card, cor

    public $id_front;

    public $id_back;

    public $cor_file;

    public string $captcha = '';

    // Department chosen by guest (slug from departments table)
    public ?string $department = null;

    // Post-submit receipt/redirect state
    public bool $showReceiptModal = false;

    public ?string $lastTicketNo = null;

    public ?string $lastTicketEmail = null;

    public bool $downloadedReceipt = false;

    public function markDownloaded(): void
    {
        $this->downloadedReceipt = true;
    }

    public function proceedToTrack()
    {
        if (! $this->downloadedReceipt) {
            // guard: require download first
            session()->flash('error', 'Please download your ticket info before continuing.');
            $this->showReceiptModal = true;

            return null;
        }

        $hint = $this->lastTicketNo
            ? "Enter your Ticket Number ($this->lastTicketNo) in the 'Ticket Number' field, then enter the same email you used and click Check Status."
            : 'Enter your Ticket Number in the Ticket Number field, then your email, and click Check Status.';

        // reset modal state
        $this->showReceiptModal = false;
        $this->downloadedReceipt = false;
        $no = $this->lastTicketNo;
        $this->lastTicketNo = null;
        $this->lastTicketEmail = null;

        return redirect()->route('helpdesk.track')->with('track_hint', $hint);
    }

    protected function rules(): array
    {
        $base = [
            'full_name' => 'required|string|min:3|max:255',
            'id_number' => 'required|string|min:3|max:50',
            'school_email' => 'required|email:rfc,dns|max:255',
            'subject' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:5',
            'priority_new' => 'required|in:low,medium,high,critical',
            // category_id is auto-mapped from category_choice
            'category_id' => 'nullable|exists:ticket_categories,id',
            'department' => 'required|exists:departments,slug',
            'captcha' => 'required|string|in:ITSS',
        ];

        if ($this->requiresVerification()) {
            $base['verification_option'] = 'required|in:id_card,cor';
            if ($this->verification_option === 'id_card') {
                $base['id_front'] = 'required|image|max:4096';
                $base['id_back'] = 'required|image|max:4096';
            } elseif ($this->verification_option === 'cor') {
                $base['cor_file'] = 'required|image|max:6144';
            }
        }

        return $base;
    }

    public function requiresVerification(): bool
    {
        // Drive by UI choice; falls back to category_id name if present
        if ($this->category_choice === 'account_access') {
            return true;
        }
        if (! $this->category_id) {
            return false;
        }
        $cat = TicketCategory::find($this->category_id);

        return $cat && Str::lower($cat->name) === 'account access';
    }

    public function updatedCategoryChoice(string $value): void
    {
        // Map UI selection to actual category_id (if Account Access exists)
        if ($value === 'account_access') {
            $this->category_id = TicketCategory::whereRaw('LOWER(name) = ?', ['account access'])->value('id');
        } else {
            $this->category_id = null; // Others: no specific category enforced
            // Clear verification-related state when switching away
            $this->verification_option = null;
            $this->id_front = null;
            $this->id_back = null;
            $this->cor_file = null;
        }
    }

    public function submit(): void
    {
        // Ensure category_id is synced with UI before validation
        if ($this->category_choice === 'account_access' && ! $this->category_id) {
            $this->category_id = TicketCategory::whereRaw('LOWER(name) = ?', ['account access'])->value('id');
        }
        $this->validate();

        $ticketNo = 'HD-'.now()->format('Y').'-'.str_pad((string) (Ticket::max('id') + 1), 5, '0', STR_PAD_LEFT);
        $verificationStatus = $this->requiresVerification() ? 'pending' : 'verified';
        $verificationMethod = null;

        // Default type for guest submissions: request
        $type = 'request';
        $policy = SlaResolver::pickPolicy($type, $this->priority_new);
        $dueAt = SlaResolver::computeDueAt($policy?->resolve_mins);

        $ticket = Ticket::create([
            'ticket_no' => $ticketNo,
            'subject' => $this->subject,
            'description' => $this->description,
            'type' => $type,
            'status' => 'open',
            'priority' => $this->priority_new,
            'category_id' => $this->category_id,
            'requester_id' => null,
            'requester_name' => $this->full_name,
            'requester_email' => $this->school_email,
            'requester_idno' => $this->id_number,
            'department' => $this->department,
            'verification_status' => $verificationStatus,
            'verification_method' => $verificationMethod,
            'sla_policy_id' => $policy?->id,
            'sla_due_at' => $dueAt,
        ]);

        if ($this->requiresVerification()) {
            if ($this->verification_option === 'id_card') {
                $verificationMethod = 'id_card';
                $frontPath = $this->id_front->store("tickets/{$ticket->id}", 'private');
                $backPath = $this->id_back->store("tickets/{$ticket->id}", 'private');
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => null,
                    'type' => 'id_front',
                    'disk' => 'private',
                    'path' => $frontPath,
                    'filename' => $this->id_front->getClientOriginalName(),
                    'mime' => $this->id_front->getMimeType(),
                    'size' => $this->id_front->getSize(),
                ]);
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => null,
                    'type' => 'id_back',
                    'disk' => 'private',
                    'path' => $backPath,
                    'filename' => $this->id_back->getClientOriginalName(),
                    'mime' => $this->id_back->getMimeType(),
                    'size' => $this->id_back->getSize(),
                ]);
            } elseif ($this->verification_option === 'cor') {
                $verificationMethod = 'cor';
                $corPath = $this->cor_file->store("tickets/{$ticket->id}", 'private');
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => null,
                    'type' => 'cor',
                    'disk' => 'private',
                    'path' => $corPath,
                    'filename' => $this->cor_file->getClientOriginalName(),
                    'mime' => $this->cor_file->getMimeType(),
                    'size' => $this->cor_file->getSize(),
                ]);
            }

            $ticket->verification_method = $verificationMethod;
            $ticket->save();
        }

        // Prepare receipt/redirect modal
        $this->lastTicketNo = $ticket->ticket_no;
        $this->lastTicketEmail = $this->school_email;
        $this->showReceiptModal = true;

        // Clear inputs for a fresh form (keep lastTicket* for the modal)
        $this->reset(['full_name', 'id_number', 'school_email', 'subject', 'description', 'category_id', 'priority_new', 'verification_option', 'id_front', 'id_back', 'cor_file', 'captcha', 'category_choice', 'department']);
        $this->priority_new = 'medium';

        // Notify agents that a new ticket has been created
        try {
            event(new TicketChanged($ticket->id, 'created'));
        } catch (\Throwable $e) { /* noop */
        }
    }

    public function render()
    {
        return view('livewire.tickets.guest-portal', [
            'categories' => TicketCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'departments' => Department::active()->guestVisible()->orderBy('sort_order')->orderBy('name')->get(['slug', 'name']),
        ]);
    }
}
