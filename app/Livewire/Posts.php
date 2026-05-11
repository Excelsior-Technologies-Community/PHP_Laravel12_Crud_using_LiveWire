<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class Posts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $title, $body, $image, $post_id, $old_image;
    public $updateMode = false;
    public $search = '';
    public $sortDirection = 'ASC';
    public $selectedPosts = [];

    protected $rules = [
        'title' => 'required|min:3',
        'body' => 'required|min:4',
        'image' => 'nullable|image|max:2048',
    ];

    public function render()
    {
        $posts = Post::where('title', 'like', '%' . $this->search . '%')
            ->orWhere('body', 'like', '%' . $this->search . '%')
            ->orderBy('id', $this->sortDirection)
            ->paginate(5);

        return view('livewire.posts', compact('posts'));
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleSort()
    {
        $this->sortDirection = $this->sortDirection === 'ASC' ? 'DESC' : 'ASC';
    }

    private function resetInput()
    {
        $this->title = '';
        $this->body = '';
        $this->image = null;
        $this->old_image = null;
        $this->post_id = null;
        $this->dispatch('reset-editor');
    }

    public function create()
    {
        $this->resetInput();
        $this->updateMode = false;
        $this->dispatch('open-modal');
    }

    public function store()
    {
        $validated = $this->validate();

        if ($this->image) {
            $validated['image'] = $this->image->store('posts', 'public');
        }

        Post::create($validated);

        $this->dispatch('close-modal');
        $this->dispatch('toast', type: 'success', message: 'Post Created Successfully.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->old_image = $post->image;
        $this->updateMode = true;
        
        $this->dispatch('open-modal');
        $this->dispatch('set-editor', body: $this->body);
    }

    public function update()
    {
        $validated = $this->validate();
        $post = Post::find($this->post_id);

        if ($this->image) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $this->image->store('posts', 'public');
        }

        $post->update($validated);

        $this->dispatch('close-modal');
        $this->dispatch('toast', type: 'success', message: 'Post Updated Successfully.');
        $this->resetInput();
        $this->updateMode = false;
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', id: $id);
    }

    public function deletePost($id)
    {
        $post = Post::find($id);
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();
        $this->dispatch('toast', type: 'success', message: 'Post Deleted Successfully.');
    }

    public function confirmBulkDelete()
    {
        if (count($this->selectedPosts) > 0) {
            $this->dispatch('swal:bulk-confirm');
        } else {
            $this->dispatch('toast', type: 'error', message: 'Select at least one post!');
        }
    }

    public function bulkDelete()
    {
        $posts = Post::whereIn('id', $this->selectedPosts)->get();
        foreach ($posts as $post) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $post->delete();
        }
        $this->selectedPosts = [];
        $this->dispatch('toast', type: 'success', message: 'Selected Posts Deleted.');
    }
}