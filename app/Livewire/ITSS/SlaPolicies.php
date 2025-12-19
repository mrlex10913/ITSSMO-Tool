<?php

namespace App\Livewire\ITSS;

use App\Models\Helpdesk\SlaPolicy;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// #[Layout('layouts.enduser')]
class SlaPolicies extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';

    public string $type = '';

    public string $priority = '';

    // Form state
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $form_type = 'incident';

    public string $form_priority = 'medium';

    public $respond_mins = null; // int|null

    public $resolve_mins = null; // int|null

    public bool $is_active = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'priority' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updating($name, $value): void
    {
        if (in_array($name, ['search', 'type', 'priority'])) {
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'form_type' => 'required|in:incident,request',
            'form_priority' => 'required|in:low,medium,high,critical',
            'respond_mins' => 'nullable|integer|min:1',
            'resolve_mins' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ];
    }

    public function getPoliciesProperty()
    {
        return SlaPolicy::query()
            ->when($this->search, fn ($q) => $q->where(function ($qq) {
                $qq->where('name', 'like', "%{$this->search}%");
            }))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
            ->orderBy('type')
            ->orderBy('priority')
            ->paginate(10);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $p = SlaPolicy::findOrFail($id);
        $this->editingId = $p->id;
        $this->name = (string) $p->name;
        $this->form_type = (string) $p->type;
        $this->form_priority = (string) $p->priority;
        $this->respond_mins = $p->respond_mins;
        $this->resolve_mins = $p->resolve_mins;
        $this->is_active = (bool) $p->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'type' => $this->form_type,
            'priority' => $this->form_priority,
            'respond_mins' => $this->respond_mins ? (int) $this->respond_mins : null,
            'resolve_mins' => $this->resolve_mins ? (int) $this->resolve_mins : null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            SlaPolicy::whereKey($this->editingId)->update($data);
        } else {
            SlaPolicy::create($data);
        }

        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', 'SLA policy saved.');
    }

    public function toggleActive(int $id): void
    {
        $p = SlaPolicy::findOrFail($id);
        $p->is_active = ! (bool) $p->is_active;
        $p->save();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->form_type = 'incident';
        $this->form_priority = 'medium';
        $this->respond_mins = null;
        $this->resolve_mins = null;
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.i-t-s-s.sla-policies', [
            'policies' => $this->policies,
        ]);
    }
}
