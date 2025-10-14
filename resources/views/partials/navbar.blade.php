<nav id="navbar" class="bg-white p-4 flex justify-between items-center shadow-md">

    {{-- 1. مجموعة تسجيل الدخول ومبدل اللغة (أصبحت العنصر الأول) --}}
    <div class="flex items-center
    space-x-4
    rtl:space-x-0 rtl:gap-4">

        {{-- مُبدّل اللغة (Language Switcher) --}}
        <button id="language-switcher"
                class="flex items-center text-gray-700 hover:text-blue-600 text-sm font-medium focus:outline-none transition-colors duration-200">
            <i class="fas fa-globe text-lg mr-1 rtl:ml-1 rtl:mr-0"></i>
            <span id="current-language">{{ app()->getLocale() === 'ar' ? 'EN' : 'العربية' }}</span>
        </button>

        <a href="{{ route('login') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-md text-base font-medium transition-colors duration-200 hover:bg-blue-700">
            {{ trans_db('login') }}
        </a>
    </div>


    {{-- 2. الشعار واسم الموقع (أصبح العنصر الثاني) --}}
    @php
        $branding = \App\Models\Logo::first();
    @endphp

    <a href="{{ route('homeForGuests') }}" class="flex items-center space-x-2 rtl:space-x-reverse">
        @if($branding && $branding->path)
            <img src="{{ asset('storage/' . $branding->path) }}" alt="Logo" class="h-8 md:h-10">
        @else
            <img src="{{ asset('assets/logo.jpg') }}" alt="Default Logo" class="h-8 md:h-10">
        @endif

        <span class="text-gray-800 text-xl font-semibold hidden md:block">
            {{ $branding->site_name ?? 'pepasafe' }}
        </span>
    </a>


</nav>

{{-- الكود الخاص بـ JavaScript لم يتم تغييره لأنه يعتمد على الـ ID وليس الترتيب --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

// ** إضافة قيمة اللغة الحالية واللغة المستهدفة من Blade **
// $currentLocale هي اللغة الحالية للموقع (مثل 'ar' أو 'en')
        const currentLocale = '{{ app()->getLocale() }}'; // 👈 نستخدم دالة app()->getLocale()
// targetLocale هو رمز اللغة التي سيتم التبديل إليها ('en' إذا كانت الحالية 'ar', والعكس)
        const targetLocale = (currentLocale === 'ar') ? 'en' : 'ar';
// targetLangDisplay: اللغة التي سيتم التبديل إليها (النص الظاهر على الزر)
        const targetLangDisplay = (currentLocale === 'ar') ? 'EN' : 'العربية';

// ** alertPrefix: نص التنبيه المترجم للغة الحالية **
        const alertPrefix = '{{ trans_db("nav.language_switch_alert_prefix") }}';

// --- الجزء الخاص باللغة ---
        const languageSwitcher = document.getElementById('language-switcher');
        const currentLanguageSpan = document.getElementById('current-language');

        if (languageSwitcher && currentLanguageSpan) {
            languageSwitcher.addEventListener('click', () => {
                // إنشاء رابط تغيير اللغة باستخدام المسار المسمى (Route Name)
                const switchUrl = '{{ route('language.switch', ['locale' => 'REPLACEME']) }}'; // استخدام Placeholder
                const finalUrl = switchUrl.replace('REPLACEME', targetLocale); // استبدال الـ Placeholder بالرمز الصحيح

                window.location.href = finalUrl;
            });
        }
// --- نهاية الجزء الخاص باللغة ---

    });
</script>
