<nav class="bg-white p-4 flex justify-between items-center shadow-md">
    <!-- الجزء الأيسر (تسجيل الدخول ومبدل اللغة) - تم نقله إلى اليسار -->
    <div class="flex items-center space-x-4 rtl:space-x-reverse">
        <!-- رابط تسجيل الدخول -->
        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md
                          text-base font-medium transition-colors duration-200
                          hover:bg-blue-700">
            تسجيل الدخول
        </a>

        <!-- مبدل اللغة -->
        <button id="language-switcher" class="flex items-center text-gray-700 hover:text-blue-600 text-sm font-medium focus:outline-none transition-colors duration-200">
            <i class="fas fa-globe text-lg mr-1 rtl:ml-1 rtl:mr-0"></i> <!-- استخدام Font Awesome لأيقونة الكرة الأرضية -->
            <span id="current-language">EN</span>
        </button>
    </div>

    <!-- الجزء الأيمن (الشعار واسم الموقع) - تم نقله إلى اليمين -->
    @php
        $branding = \App\Models\Logo::first();
    @endphp

    <a href="{{ route('homeForGuests') }}" class="flex items-center space-x-2">
        <!-- شعار الموقع -->
        @if($branding && $branding->path)
            <img src="{{ asset('storage/' . $branding->path) }}" alt="Logo" class="h-8 md:h-10">
        @else
            <img src="{{ asset('assets/logo.jpg') }}" alt="Default Logo" class="h-8 md:h-10">
        @endif

        <!-- اسم الموقع -->
        <span class="text-gray-800 text-xl font-semibold hidden md:block">
        {{ $branding->site_name ?? 'pepasafe' }}
    </span>
    </a>


</nav>






<script>
    // Get references to the language switcher button and the span that displays the language
    const languageSwitcher = document.getElementById('language-switcher');
    const currentLanguageSpan = document.getElementById('current-language');

    // Add a click event listener to the language switcher button
    languageSwitcher.addEventListener('click', () => {
        // Check the current text content of the span
        if (currentLanguageSpan.textContent === 'EN') {
            // If it's 'EN', change it to 'العربية'
            currentLanguageSpan.textContent = 'العربية';
        } else {
            // If it's 'العربية' (or anything else), change it back to 'EN'
            currentLanguageSpan.textContent = 'EN';
        }
    });


</script>
