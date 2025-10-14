<div class="fi-fo-field-wrp" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="flex flex-col gap-y-1">
        <div class="fi-input-wrapper flex items-center justify-between">
            @php
                // نحصل على عدد المستخدمين المتاح من المستخدم الحالي
                $count = auth()->user()->max_users ?? 0; // افتراضياً 0 إذا لم يكن متاحاً

                if ($count <= 0) {
                    $color = 'red';
                } elseif ($count <= 5) {
                    $color = 'orange';
                } else {
                    $color = 'green';
                }
            @endphp

            <div class="flex items-center gap-x-2">
                <!-- استخدام مفتاح الترجمة بدلاً من النص المباشر -->
                <label class="fi-fo-field-label text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    {{ trans_db('app.available_users_count') }}:
                </label>

                <!-- عرض العدد باللون المناسب -->
                <span style="color: {{ $color }}; font-weight: bold; font-size: 1.25rem;">
                    {{ $count }}
                </span>
            </div>
        </div>
    </div>
</div>
