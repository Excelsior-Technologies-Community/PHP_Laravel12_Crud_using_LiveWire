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

    // Properties
    public $title, $body, $post_id, $old_image;
    public $updateMode = false;
    public $search = '';
    public $selectedPosts = [];
    public $selectAll = false;
    public $image;
    public $sortField = 'id';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['search' => ['except' => ''], 'sortField', 'sortDirection'];

    // Validation rules
    protected $rules = [
        'title' => 'required|min:3|max:255',
        'body' => 'required|min:10',
        'image' => 'nullable|image|max:2048',
    ];

    // Real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Toggle sort
    public function toggleSort()
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->resetPage();
    }

    // Reset input fields
    private function resetInput()
    {
        $this->title = '';
        $this->body = '';
        $this->image = null;
        $this->old_image = null;
        $this->post_id = null;
    }

    // Reset filters
    public function resetFilters()
    {
        $this->search = '';
        $this->selectedPosts = [];
        $this->selectAll = false;
        $this->resetPage();
        $this->dispatch('toast', type: 'info', message: 'Filters cleared!');
    }

    // Open create modal
    public function create()
    {
        $this->resetInput();
        $this->updateMode = false;
        $this->dispatch('open-modal');
        $this->dispatch('reset-editor');
    }

    // Store post
    public function store()
    {
        $validated = $this->validate();

        if ($this->image) {
            $imagePath = $this->image->store('posts', 'public');
            $validated['image'] = $imagePath;
        }

        Post::create($validated);

        $this->resetInput();
        $this->dispatch('close-modal');
        $this->dispatch('reset-editor');
        $this->dispatch('toast', type: 'success', message: 'Post created successfully!');
    }

    // Edit post
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->old_image = $post->image;
        $this->updateMode = true;

        $this->dispatch('open-modal');
        $this->dispatch('set-editor', body: $post->body);
    }

    // Cancel edit
    public function cancel()
    {
        $this->updateMode = false;
        $this->resetInput();
        $this->dispatch('close-modal');
        $this->dispatch('reset-editor');
    }

    // Update post
    public function update()
    {
        $validated = $this->validate();

        $post = Post::find($this->post_id);

        if ($this->image) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $this->image->store('posts', 'public');
            $validated['image'] = $imagePath;
        } else {
            $validated['image'] = $post->image;
        }

        $post->update($validated);

        $this->resetInput();
        $this->updateMode = false;
        $this->dispatch('close-modal');
        $this->dispatch('reset-editor');
        $this->dispatch('toast', type: 'success', message: 'Post updated successfully!');
    }

    // Confirm single delete
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', id: $id);
    }

    // Delete single post
    public function deletePost($id)
    {
        $post = Post::find($id);
        if ($post) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $post->delete();
            $this->dispatch('toast', type: 'success', message: 'Post deleted successfully!');
        }
    }

    // Confirm bulk delete
    public function confirmBulkDelete()
    {
        if (count($this->selectedPosts) > 0) {
            $this->dispatch('swal:bulk-confirm');
        } else {
            $this->dispatch('toast', type: 'warning', message: 'No posts selected!');
        }
    }

    // Bulk delete
    public function bulkDelete()
    {
        if (count($this->selectedPosts) > 0) {
            $posts = Post::whereIn('id', $this->selectedPosts)->get();
            foreach ($posts as $post) {
                if ($post->image && Storage::disk('public')->exists($post->image)) {
                    Storage::disk('public')->delete($post->image);
                }
            }
            
            Post::whereIn('id', $this->selectedPosts)->delete();
            $deletedCount = count($this->selectedPosts);
            $this->selectedPosts = [];
            $this->selectAll = false;
            $this->dispatch('toast', type: 'success', message: $deletedCount . ' post(s) deleted successfully!');
        }
    }

    // Select all functionality
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPosts = Post::query()
                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('body', 'like', '%' . $this->search . '%')
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedPosts = [];
        }
    }

    // Get posts with search, sort, pagination
    public function getPostsProperty()
    {
        return Post::where('title', 'like', '%' . $this->search . '%')
            ->orWhere('body', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);
    }

    // Render component - FIXED: properly passing posts to view
    public function render()
    {
        $posts = Post::where('title', 'like', '%' . $this->search . '%')
            ->orWhere('body', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);
            
        return view('livewire.posts', [
            'posts' => $posts
        ]);
    }
}