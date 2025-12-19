<?php

namespace App\Livewire\ITSS;

use App\Models\Helpdesk\CannedResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class CannedResponses extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $body = '';

    public bool $is_global = true;

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
            'title' => 'required|string|min:3|max:255',
            'body' => 'required|string|min:1',
            'is_global' => 'boolean',
        ];
    }

    public function getItemsProperty()
    {
        return CannedResponse::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('title')
            ->paginate(10);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $cr = CannedResponse::findOrFail($id);
        $this->editingId = $cr->id;
        $this->title = (string) $cr->title;
        $this->body = (string) $cr->body;
        $this->is_global = (bool) $cr->is_global;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'title' => $this->title,
            'body' => $this->body,
            'is_global' => $this->is_global,
            'updated_by' => Auth::id(),
        ];
        if ($this->editingId) {
            CannedResponse::whereKey($this->editingId)->update($data);
        } else {
            $data['created_by'] = Auth::id();
            CannedResponse::create($data);
        }
        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', 'Canned response saved.');
    }

    public function delete(int $id): void
    {
        CannedResponse::whereKey($id)->delete();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->body = '';
        $this->is_global = true;
    }

    public function render()
    {
        return view('livewire.i-t-s-s.canned-responses', [
            'items' => $this->items,
        ]);
    }
}
