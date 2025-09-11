<div class="fi-fo-field-wrp">
    <div class="flex flex-col gap-y-1">
        <div class="fi-input-wrapper flex items-center justify-between">
            @php
                $count = auth()->user()->max_users;

                if ($count <= 0) {
                    $color = 'red';
                } elseif ($count <= 5) {
                    $color = 'orange';
                } else {
                    $color = 'green';
                }
            @endphp

            <div class="flex items-center gap-x-2">
                <label class="fi-fo-field-label text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Available Users Count:
                </label>
                <span style="color: {{ $color }}; font-weight: bold; font-size: 1.25rem;">
                    {{ $count }}
                </span>
            </div>
        </div>
    </div>
</div>
