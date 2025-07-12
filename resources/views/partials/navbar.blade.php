<!-- ملف: partials/navbar.html -->
<nav id="navbar" class="w-full bg-white shadow p-4 relative z-50">
    <div class="max-w-6xl mx-auto flex items-center justify-center relative">

        <div class="absolute right-0 flex items-center space-x-8 md:space-x-4" dir="rtl">
            <img src=" {{ asset('assets/logo.jpg') }} " alt="Logo" class="h-8 md:h-10">
{{--            <div id="langSel" class="relative inline-block border border-gray-300 rounded-xl bg-white">--}}
{{--                <button id="selBtn"--}}
{{--                        class="flex items-center gap-1 px-2 py-1 md:gap-2 md:px-4 md:py-2 focus:outline-none">--}}
{{--                    <i class="bi bi-globe2 text-blue-600 text-lg md:text-xl"></i> <span class="flag text-lg md:text-xl">🇸🇦</span> --}}
{{--                    <span class="lang-name text-sm md:text-base">العربية</span> --}}
{{--                    <i id="caret" class="bi bi-caret-down-fill text-gray-500 transition-transform duration-300 text-sm md:text-base"></i> --}}
{{--                </button>--}}
{{--                <ul id="langDropdown"--}}
{{--                    class="hidden absolute right-0 mt-1 w-40 bg-white border border-gray-300 rounded-xl shadow-lg overflow-hidden">--}}
{{--                    <li data-lang="ar"--}}
{{--                        class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 cursor-pointer">--}}
{{--                        <span class="flag text-xl">🇸🇦</span>--}}
{{--                        <span class="lang-name">العربية</span>--}}
{{--                    </li>--}}
{{--                    <li data-lang="en"--}}
{{--                        class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 cursor-pointer">--}}
{{--                        <span class="flag text-xl">🇬🇧</span>--}}
{{--                        <span class="lang-name">English</span>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </div>--}}
        <div class="hidden sm:flex space-x-6">
            <button id="language-switcher" class="flex items-center text-gray-700 hover:text-blue-600 text-sm font-medium focus:outline-none">
                <i class="bi bi-globe text-lg mr-1"></i>
                <span id="current-language">EN</span>
            </button>
        </div>


        </div>

        <div class="hidden md:flex gap-x-8">
            <a href="{{ route('home.eventor') }}" class="text-gray-800 hover:text-blue-500 font-medium">الرئيسية</a>
            <a href="{{ route('about') }}#about" class="text-gray-800 hover:text-blue-500 font-medium">عن المشروع </a>
            <a href="{{ route('about') }}#contact" class="text-gray-800 hover:text-blue-500 font-medium">تواصل معنا</a>
        </div>

        <button id="mobile-menu-btn" class="absolute left-0 md:hidden focus:outline-none z-50" dir="ltr">
            <svg class="w-6 h-6 text-gray-800 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div id="mobile-menu" class="fixed inset-y-0 right-0 w-1/2 bg-white transform translate-x-full transition-transform duration-300 md:hidden z-50">
        <button id="mobile-menu-close" class="absolute top-4 right-4 focus:outline-none z-50">
            <svg class="w-6 h-6 text-gray-800 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <ul class="flex flex-col justify-start items-center h-full space-y-6 pt-20 text-xl">
            <li><a href="#" class="text-gray-800 hover:text-blue-500 font-medium">الرئيسية</a></li>
            <li><a href="#" class="text-gray-800 hover:text-blue-500 font-medium">خدماتنا</a></li>
            <li><a href="#" class="text-gray-800 hover:text-blue-500 font-medium">تواصل معنا</a></li>
        </ul>
    </div>
</nav>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnOpen  = document.getElementById('mobile-menu-btn');
        const btnClose = document.getElementById('mobile-menu-close');
        const menu     = document.getElementById('mobile-menu');

        btnOpen.addEventListener('click', function() {
            menu.classList.replace('translate-x-full', 'translate-x-0');
        });
        btnClose.addEventListener('click', function() {
            menu.classList.replace('translate-x-0', 'translate-x-full');
        });
    });

    const langSel = document.getElementById('langSel');
    const selBtn = document.getElementById('selBtn');
    const dropdown = document.getElementById('langDropdown');
    const caret = document.getElementById('caret');
    const selectedFlag = selBtn.querySelector('.flag');
    const selectedName = selBtn.querySelector('.lang-name');

    // فتح/إغلاق القائمة عند الضغط على الزر فقط
    selBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
        caret.classList.toggle('rotate-180');
    });

    // اختيار لغة وإغلاق القائمة
    dropdown.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            selectedFlag.textContent = item.querySelector('.flag').textContent;
            selectedName.textContent = item.querySelector('.lang-name').textContent;
            dropdown.classList.add('hidden');
            caret.classList.remove('rotate-180');
            // هنا تضيف منطق تغيير اللغة فعليًا
        });
    });

    // إغلاق القائمة عند النقر في أي مكان آخر
    document.addEventListener('click', () => {
        dropdown.classList.add('hidden');
        caret.classList.remove('rotate-180');
    });

    // الجزء الخاص باللغة
    document.addEventListener('DOMContentLoaded', function() {
        const languageSwitcher = document.getElementById('language-switcher');
        const currentLanguageSpan = document.getElementById('current-language');

        // Language Switcher Logic
        languageSwitcher.addEventListener('click', function() {
            const currentLang = currentLanguageSpan.textContent.trim().toLowerCase();
            let newLang = '';
            if (currentLang === 'en') {
                newLang = 'ar';
                currentLanguageSpan.textContent = 'العربية';
            } else {
                newLang = 'en';
                currentLanguageSpan.textContent = 'EN';
            }

            // هنا يمكنك إضافة منطق لتغيير اللغة الفعلي في التطبيق (مثل إعادة التوجيه أو AJAX)
            // window.location.href = `/lang/${newLang}`; // مثال: إذا كان لديك راوت لتغيير اللغة

            console.log('Language switched to:', newLang);
            document.documentElement.setAttribute('lang', newLang); // تحديث خاصية lang في وسم <html>
        });

        // Initialize language display based on current HTML lang attribute
        const initialLang = document.documentElement.getAttribute('lang') || 'en';
        if (initialLang === 'ar') {
            currentLanguageSpan.textContent = 'العربية';
        } else {
            currentLanguageSpan.textContent = 'EN';
        }
    });

    document.getElementById('mobile-menu-toggle').addEventListener('click', () => {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
