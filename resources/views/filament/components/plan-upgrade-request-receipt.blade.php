<img
    src="{{ Storage::url($getRecord()->receipt_path) }}"
    alt="Receipt"
    class="h-11 cursor-pointer rounded"
    x-data
    x-on:click="$dispatch('open-modal-{{ $getRecord()->id }}')"
/>

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $getRecord()->id }}.window="open = true"
    x-show="open"
    x-transition
    class="fixed inset-0 bg-black/70 flex items-center justify-center z-50"
>
    <div @click.outside="open = false" class="bg-white p-4 rounded shadow max-w-4xl w-full">
        <img src="{{ Storage::url($getRecord()->receipt_path) }}" class="w-full h-auto" />
        <div class="text-right mt-2">
            <button @click="open = false" class="px-4 py-1 bg-red-600 text-white rounded">Close</button>
        </div>
    </div>
</div>
