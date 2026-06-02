<div>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3 flex-wrap">
            <h4 class="mb-0 font-weight-bold">Posts Gallery</h4>
            <div class="d-flex align-items-center mt-2 mt-sm-0 flex-wrap">
                <div class="input-group mr-2" style="width: 250px;">
                    <input type="text" class="form-control form-control-sm shadow-sm" wire:model.live="search" placeholder="Search title or body..." style="border-radius: 20px 0 0 20px;">
                    <div class="input-group-append">
                        <button wire:click="resetFilters" class="btn btn-warning btn-sm" style="border-radius: 0 20px 20px 0;"></button>
                    </div>
                </div>
                <button wire:click="create" class="btn btn-success btn-sm font-weight-bold mr-2 px-3 shadow-sm"> Add New</button>
                <button wire:click="confirmBulkDelete" class="btn btn-danger btn-sm font-weight-bold px-3 shadow-sm"> Bulk Delete</button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-uppercase small font-weight-bold">
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" wire:model.live="selectAll">
                            </th>
                            <th style="width: 100px;">Image</th>
                            <th wire:click="toggleSort" style="cursor: pointer; min-width: 200px;">
                                Title 
                                @if($sortField == 'title')
                                    {!! $sortDirection == 'asc' ? '⬆' : '⬇' !!}
                                @else
                                    ↕
                                @endif
                            </th>
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
                                        <img src="{{ asset('storage/'.$post->image) }}" width="65" height="65" class="rounded shadow-sm border object-cover" style="object-fit: cover;">
                                    @else
                                        <div class="bg-light text-muted rounded small py-3 border">No Image</div>
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold text-dark">{{ $post->title }}</td>
                                <td class="align-middle text-muted">{!! \Illuminate\Support\Str::limit(strip_tags($post->body), 80) !!}</td>
                                <td class="align-middle text-center">
                                    <div class="btn-group shadow-sm rounded">
                                        <button wire:click="edit({{ $post->id }})" class="btn btn-info btn-sm px-3"> Edit</button>
                                        <button wire:click="confirmDelete({{ $post->id }})" class="btn btn-danger btn-sm px-3">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <h5 class="mb-0"> No records found!</h5>
                                    <small>Try changing your search or add a new post.</small>
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

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="postModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header {{ $updateMode ? 'bg-info' : 'bg-success' }} text-white">
                    <h5 class="modal-title font-weight-bold">
                        {{ $updateMode ? '✏ Edit Post' : ' Create New Post' }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark"> Post Title</label>
                            <input type="text" class="form-control" wire:model.live="title" placeholder="Enter title">
                            @error('title') 
                                <span class="text-danger small font-weight-bold">{{ $message }}</span> 
                            @enderror
                            <small class="text-muted">{{ strlen($title ?? '') }} characters</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark"> Description</label>
                            <div wire:ignore>
                                <textarea id="summernote" class="form-control"></textarea>
                            </div>
                            @error('body') 
                                <span class="text-danger small font-weight-bold">{{ $message }}</span> 
                            @enderror
                        </div>
                        
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark"> Image</label>
                            <input type="file" class="form-control-file mb-2" wire:model="image" accept="image/*">
                            
                            <div wire:loading wire:target="image" class="text-primary small mb-2">
                                <i class="fa fa-spinner fa-spin"></i> Uploading...
                            </div>
                            
                            @error('image') 
                                <span class="text-danger small font-weight-bold d-block">{{ $message }}</span> 
                            @enderror
                            
                            <div class="mt-2 text-center">
                                @if ($image)
                                    <label class="d-block small text-success"> New Preview:</label>
                                    <img src="{{ $image->temporaryUrl() }}" class="rounded shadow-sm border" style="max-height: 150px; width: auto;">
                                @elseif($old_image)
                                    <label class="d-block small text-info"> Current Image:</label>
                                    <img src="{{ asset('storage/'.$old_image) }}" class="rounded shadow-sm border" style="max-height: 150px; width: auto;">
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
                    @if($updateMode)
                        <button wire:click.prevent="update" class="btn btn-info px-5 font-weight-bold"> Update Post</button>
                    @else
                        <button wire:click.prevent="store" class="btn btn-success px-5 font-weight-bold"> Save Post</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Initialize Summernote
            if (typeof $.fn.summernote !== 'undefined') {
                $('#summernote').summernote({
                    height: 250,
                    placeholder: 'Start writing your post content here...',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onChange: function(contents) {
                            @this.set('body', contents);
                        }
                    }
                });
            }

            // Modal events
            Livewire.on('open-modal', () => {
                $('#postModal').modal('show');
            });

            Livewire.on('close-modal', () => {
                $('#postModal').modal('hide');
            });

            Livewire.on('reset-editor', () => {
                if (typeof $.fn.summernote !== 'undefined') {
                    $('#summernote').summernote('code', '');
                }
            });

            Livewire.on('set-editor', (data) => {
                if (typeof $.fn.summernote !== 'undefined') {
                    $('#summernote').summernote('code', data.body);
                }
            });

            // Toast notifications
            Livewire.on('toast', (data) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: data.type,
                    title: data.message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });

            // Single delete confirmation
            Livewire.on('swal:confirm', (data) => {
                Swal.fire({
                    title: 'Delete post?',
                    text: "You won't be able to recover this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('deletePost', data.id);
                    }
                });
            });

            // Bulk delete confirmation
            Livewire.on('swal:bulk-confirm', () => {
                Swal.fire({
                    title: 'Delete selected posts?',
                    text: "This will remove all selected items permanently!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete all!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('bulkDelete');
                    }
                });
            });
        });
    </script>
</div>