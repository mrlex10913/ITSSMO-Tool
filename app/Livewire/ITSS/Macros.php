<?php

namespace App\Livewire\ITSS;

use App\Models\Helpdesk\TicketMacro;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class Macros extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public array $actions = [];

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
            'actions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getItemsProperty()
    {
        return TicketMacro::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $m = TicketMacro::findOrFail($id);
        $this->editingId = $m->id;
        $this->name = (string) $m->name;
        $this->actions = (array) $m->actions;
        $this->is_active = (bool) $m->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'actions' => $this->actions,
            'is_active' => $this->is_active,
            'updated_by' => Auth::id(),
        ];
        if ($this->editingId) {
            TicketMacro::whereKey($this->editingId)->update($data);
        } else {
            $data['created_by'] = Auth::id();
            TicketMacro::create($data);
        }
        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', 'Macro saved.');
    }

    public function delete(int $id): void
    {
        TicketMacro::whereKey($id)->delete();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->actions = [];
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.i-t-s-s.macros', [
            'items' => $this->items,
        ]);
    }
}
