<div>
    <form wire:submit="save">
        <h4>Create</h4>
        <div class="mb-3">
            <label for="" class="form-label">ID</label>
            <input type="text" id="authorityId" wire:model="authorityId" class="form-control">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Name</label>
            <input type="text" id="name" wire:model="name" class="form-control">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Prefix</label>
            <input type="text" id="prefix" wire:model="prefix" class="form-control">
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" wire:model="autoAcknowledgeParticipant" id="autoAcknowledgeParticipant">
                <label class="form-check-label" for="">
                    Auto acknowledge participant
                </label>
            </div>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" wire:model="validRclTarget" id="validRclTarget">
                <label class="form-check-label" for="">
                    Valid RCL target
                </label>
            </div>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" wire:model="system" id="system">
                <label class="form-check-label" for="">
                    System authority
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
