<x-filament::page>
    {{ $this->form }}

    <div class="mt-6">
        <x-filament::button wire:click="createUser">
            Create
        </x-filament::button>
    </div>
</x-filament::page>
