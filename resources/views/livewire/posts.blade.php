<div>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 font-weight-bold">Posts Gallery</h4>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control form-control-sm mr-3 shadow-sm" wire:model.live="search" placeholder="Search title or body..." style="width: 250px; border-radius: 20px;">
                <button wire:click="create" class="btn btn-success btn-sm font-weight-bold mr-2 px-3 shadow-sm">Add New Post</button>
                <button wire:click="confirmBulkDelete" class="btn btn-danger btn-sm font-weight-bold px-3 shadow-sm">Bulk Delete</button>
            </div>
        </div>
        <div class="card-body p-0 text-center">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-uppercase small font-weight-bold">
                        <tr>
                            <th style="width: 50px;"></th>
                            <th style="width: 120px;">Image</th>
                            <th wire:click="toggleSort" style="cursor: pointer; width: 200px;">Title ↕</th>
                            <th>Body Preview</th>
                            <th style="width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td class="align-middle text-center">
                                    <input type="checkbox" wire:model.live="selectedPosts" value="{{ $post->id }}">
                                </td>
                                <td class="align-middle text-center">
                                    @if($post->image)
                                        <img src="{{ asset('storage/'.$post->image) }}" width="65" height="65" class="rounded shadow-sm border object-cover">
                                    @else
                                        <div class="bg-light text-muted rounded small py-3 border">No Image</div>
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold text-dark">{{ $post->title }}</td>
                                <td class="align-middle text-muted">{!! \Illuminate\Support\Str::limit(strip_tags($post->body), 80) !!}</td>
                                <td class="align-middle text-center">
                                    <div class="btn-group shadow-sm rounded">
                                        <button wire:click="edit({{ $post->id }})" class="btn btn-info btn-sm px-3">Edit</button>
                                        <button wire:click="confirmDelete({{ $post->id }})" class="btn btn-danger btn-sm px-3">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <h5 class="mb-0">No records found!</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($posts->hasPages())
            <div class="card-footer bg-white border-0 pt-3">
                <div class="d-flex justify-content-center">
                    {{ $posts->links() }}
                </div>
            </div>
        @endif
    </div>

    <div wire:ignore.self class="modal fade" id="postModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header {{ $updateMode ? 'bg-info' : 'bg-success' }} text-white">
                    <h5 class="modal-title font-weight-bold">{{ $updateMode ? 'Edit Post' : 'Create New Post' }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Post Title</label>
                            <input type="text" class="form-control" wire:model.live="title" placeholder="Enter title">
                            @error('title') <span class="text-danger small font-weight-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Description</label>
                            <div wire:ignore>
                                <textarea id="summernote" class="form-control"></textarea>
                            </div>
                            @error('body') <span class="text-danger small font-weight-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark">Image</label>
                            <input type="file" class="form-control-file mb-2" wire:model="image">
                            <div wire:loading wire:target="image" class="text-primary small mb-2"><i class="fa fa-spinner fa-spin"></i> Uploading...</div>
                            @error('image') <span class="text-danger small font-weight-bold d-block">{{ $message }}</span> @enderror
                            
                            <div class="mt-2 text-center">
                                @if ($image)
                                    <label class="d-block small text-success">New Preview:</label>
                                    <img src="{{ $image->temporaryUrl() }}" class="rounded shadow-sm border" style="max-height: 150px; width: auto;">
                                @elseif($old_image)
                                    <label class="d-block small text-info">Current Image:</label>
                                    <img src="{{ asset('storage/'.$old_image) }}" class="rounded shadow-sm border" style="max-height: 150px; width: auto;">
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
                    @if($updateMode)
                        <button wire:click.prevent="update" class="btn btn-info px-5 font-weight-bold">Update Post</button>
                    @else
                        <button wire:click.prevent="store" class="btn btn-success px-5 font-weight-bold">Save Post</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            
            $('#summernote').summernote({
                height: 250,
                placeholder: 'Start writing...',
                callbacks: {
                    onChange: function(contents) {
                        @this.set('body', contents);
                    }
                }
            });

            Livewire.on('open-modal', () => {
                $('#postModal').modal('show');
            });

            Livewire.on('close-modal', () => {
                $('#postModal').modal('hide');
            });

            Livewire.on('reset-editor', () => {
                $('#summernote').summernote('code', '');
            });

            Livewire.on('set-editor', (data) => {
                $('#summernote').summernote('code', data.body);
            });

            Livewire.on('toast', (data) => {
                Swal.fire({
                    toast: true, position: 'top-end', icon: data.type, title: data.message, showConfirmButton: false, timer: 3000, timerProgressBar: true
                });
            });

            Livewire.on('swal:confirm', (data) => {
                Swal.fire({
                    title: 'Delete post?', text: "You won't be able to recover this!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) { @this.call('deletePost', data.id); }
                });
            });

            Livewire.on('swal:bulk-confirm', () => {
                Swal.fire({
                    title: 'Delete selected?', text: "This will remove all selected items!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete all!'
                }).then((result) => {
                    if (result.isConfirmed) { @this.call('bulkDelete'); }
                });
            });
        });
    </script>
</div>