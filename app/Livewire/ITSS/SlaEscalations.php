<?php

namespace App\Livewire\ITSS;

use App\Models\Helpdesk\SlaEscalation;
use App\Models\Helpdesk\SlaPolicy;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class SlaEscalations extends Component
{
    use WithPagination;

    public ?int $policyId = null;

    public bool $showModal = false;

    public ?int $editingId = null;

    public $threshold_mins_before_breach = null; // int|null

    public ?int $escalate_to_user_id = null;

    public bool $is_active = true;

    protected $queryString = [
        'policyId' => ['except' => null, 'as' => 'policy'],
        'page' => ['except' => 1],
    ];

    public function mount(): void
    {
        if (! $this->policyId) {
            $first = SlaPolicy::orderBy('type')->orderBy('priority')->first();
            $this->policyId = $first?->id;
        }
    }

    protected function rules(): array
    {
        return [
            'policyId' => 'required|exists:sla_policies,id',
            'threshold_mins_before_breach' => 'required|integer|min:1',
            'escalate_to_user_id' => 'required|exists:users,id',
            'is_active' => 'boolean',
        ];
    }

    public function getPoliciesProperty()
    {
        return SlaPolicy::orderBy('type')->orderBy('priority')->get(['id', 'name', 'type', 'priority']);
    }

    public function getAgentsProperty()
    {
        return User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['itss', 'administrator', 'developer']);
        })->orderBy('name')->get(['id', 'name']);
    }

    public function getItemsProperty()
    {
        return SlaEscalation::with('escalateTo:id,name')
            ->where('sla_policy_id', $this->policyId)
            ->orderBy('threshold_mins_before_breach')
            ->paginate(10);
    }

    public function updatedPolicyId(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $e = SlaEscalation::findOrFail($id);
        $this->editingId = $e->id;
        $this->threshold_mins_before_breach = $e->threshold_mins_before_breach;
        $this->escalate_to_user_id = $e->escalate_to_user_id;
        $this->is_active = (bool) $e->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'sla_policy_id' => $this->policyId,
            'threshold_mins_before_breach' => (int) $this->threshold_mins_before_breach,
            'escalate_to_user_id' => $this->escalate_to_user_id,
            'is_active' => $this->is_active,
        ];
        if ($this->editingId) {
            SlaEscalation::whereKey($this->editingId)->update($data);
        } else {
            SlaEscalation::create($data);
        }
        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', 'Escalation saved.');
    }

    public function delete(int $id): void
    {
        SlaEscalation::whereKey($id)->delete();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->threshold_mins_before_breach = null;
        $this->escalate_to_user_id = null;
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.i-t-s-s.sla-escalations', [
            'policies' => $this->policies,
            'agents' => $this->agents,
            'items' => $this->items,
        ]);
    }
}
