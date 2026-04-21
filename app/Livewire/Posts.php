<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class Posts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $title, $body, $post_id;
    public $updateMode = false;
    public $search = '';
    public $sortDirection = 'ASC';

    //  Validation rules
    protected $rules = [
        'title' => 'required|min:3',
        'body' => 'required|min:4',
    ];

    //  Real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    //  Reset pagination when search
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Toggle sorting
    public function toggleSort()
    {
        $this->sortDirection = $this->sortDirection === 'ASC' ? 'DESC' : 'ASC';
    }

    public function render()
    {
        $posts = Post::where('title', 'like', '%' . $this->search . '%')
            ->orWhere('body', 'like', '%' . $this->search . '%')
            ->orderBy('id', $this->sortDirection)
            ->paginate(5);

        return view('livewire.posts', compact('posts'));
    }

    private function resetInput()
    {
        $this->title = '';
        $this->body = '';
    }

    public function store()
    {
        $validated = $this->validate();

        Post::create($validated);

        session()->flash('message', 'Post Created Successfully.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->updateMode = true;
    }

    public function cancel()
    {
        $this->updateMode = false;
        $this->resetInput();
    }

    public function update()
    {
        $validated = $this->validate();

        $post = Post::find($this->post_id);
        $post->update($validated);

        session()->flash('message', 'Post Updated Successfully.');
        $this->updateMode = false;
        $this->resetInput();
    }

    public function delete($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
    }
}