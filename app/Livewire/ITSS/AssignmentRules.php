<?php

namespace App\Livewire\ITSS;

use App\Models\Helpdesk\AssignmentRule;
use App\Models\Helpdesk\TicketCategory;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class AssignmentRules extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public array $criteria = [
        'type' => '',
        'priority' => '',
        'category_id' => null,
        'keywords' => '',
    ];

    public ?int $assignee_id = null;

    public bool $is_active = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updating($name, $value): void
    {
        if ($name === 'search') {
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'criteria' => 'array',
            'criteria.type' => 'nullable|in:incident,request',
            'criteria.priority' => 'nullable|in:low,medium,high,urgent',
            'criteria.category_id' => 'nullable|exists:ticket_categories,id',
            'criteria.keywords' => 'nullable|string|max:255',
            'assignee_id' => 'required|exists:users,id',
            'is_active' => 'boolean',
        ];
    }

    public function getItemsProperty()
    {
        return AssignmentRule::with('assignee:id,name')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);
    }

    public function getAgentsProperty()
    {
        return User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['itss', 'administrator', 'developer']);
        })->orderBy('name')->get(['id', 'name']);
    }

    public function getCategoriesProperty()
    {
        return TicketCategory::orderBy('name')->get(['id', 'name']);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $r = AssignmentRule::findOrFail($id);
        $this->editingId = $r->id;
        $this->name = (string) $r->name;
        $this->criteria = array_merge([
            'type' => '', 'priority' => '', 'category_id' => null, 'keywords' => '',
        ], (array) $r->criteria);
        $this->assignee_id = $r->assignee_id;
        $this->is_active = (bool) $r->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        $criteria = [
            'type' => $this->criteria['type'] ?? '',
            'priority' => $this->criteria['priority'] ?? '',
            'category_id' => $this->criteria['category_id'] ?? null,
            'keywords' => trim((string) ($this->criteria['keywords'] ?? '')),
        ];
        if ($criteria['category_id'] === '') {
            $criteria['category_id'] = null;
        }
        $data = [
            'name' => $this->name,
            'criteria' => $criteria,
            'assignee_id' => $this->assignee_id,
            'is_active' => $this->is_active,
        ];
        if ($this->editingId) {
            AssignmentRule::whereKey($this->editingId)->update($data);
        } else {
            AssignmentRule::create($data);
        }
        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', 'Assignment rule saved.');
    }

    public function delete(int $id): void
    {
        AssignmentRule::whereKey($id)->delete();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->criteria = ['type' => '', 'priority' => '', 'category_id' => null, 'keywords' => ''];
        $this->assignee_id = null;
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.i-t-s-s.assignment-rules', [
            'items' => $this->items,
            'agents' => $this->agents,
            'categories' => $this->categories,
        ]);
    }
}
