<div class="container">

    <!--  SUCCESS MESSAGE -->
    @if (session()->has('message'))
        <div class="alert alert-success shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <!--  HEADER CARD -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <!--  SEARCH -->
                <input 
                    type="text" 
                    class="form-control w-50 shadow-sm" 
                    placeholder="🔍 Search posts..." 
                    wire:model.live="search"
                >

                <!--  SORT BUTTON -->
                <button wire:click="toggleSort" 
                    class="btn btn-{{ $sortDirection === 'ASC' ? 'primary' : 'dark' }} ml-2 shadow-sm">
                    
                    <i class="fa fa-sort"></i>
                    {{ $sortDirection === 'ASC' ? 'Oldest First' : 'Latest First' }}
                </button>

            </div>
        </div>
    </div>

    <!--  FORM CARD -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">

            <h5 class="mb-3 text-primary">
                {{ $updateMode ? '✏️ Edit Post' : '➕ Create New Post' }}
            </h5>

            @if($updateMode)
                @include('livewire.update')
            @else
                @include('livewire.create')
            @endif

        </div>
    </div>

    <!--  TABLE CARD -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table table-hover mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Body</th>
                        <th width="180px">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td class="align-middle">{{ $post->id }}</td>

                        <td class="align-middle">
                            <strong>{{ $post->title }}</strong>
                        </td>

                        <td class="align-middle text-muted">
                            {{ Str::limit($post->body, 50) }}
                        </td>

                        <td class="align-middle">

                            <button 
                                wire:click="edit({{ $post->id }})" 
                                class="btn btn-sm btn-outline-primary mr-1">
                                ✏️ Edit
                            </button>

                            <button
                                class="btn btn-sm btn-outline-danger"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $post->id }})"
                            >
                                🗑 Delete
                            </button>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted p-4">
                            🚫 No posts found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <!-- PAGINATION -->
    <div class="mt-3 d-flex justify-content-center">
        {{ $posts->links() }}
    </div>

</div>