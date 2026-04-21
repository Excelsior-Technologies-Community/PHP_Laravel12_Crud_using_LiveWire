<form>
    <div class="form-group">
        <label>Title:</label>
        <input type="text" class="form-control" wire:model.live="title">

        <small class="text-muted">
            {{ strlen($title ?? '') }} characters
        </small>

        @error('title') 
            <span class="text-danger d-block">{{ $message }}</span> 
        @enderror
    </div>

    <div class="form-group">
        <label>Body:</label>
        <textarea class="form-control" wire:model.live="body"></textarea>

        <small class="text-muted">
            {{ strlen($body ?? '') }} characters
        </small>

        @error('body') 
            <span class="text-danger d-block">{{ $message }}</span> 
        @enderror
    </div>

    <button wire:click.prevent="update()" class="btn btn-dark">Update</button>
    <button wire:click.prevent="cancel()" class="btn btn-danger">Cancel</button>
</form>