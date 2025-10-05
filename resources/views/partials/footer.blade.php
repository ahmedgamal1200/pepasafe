@php
    // تحديد ما إذا كانت اللغة الحالية هي العربية (RTL)
    $isRtl = (app()->getLocale() === 'ar');
    $dirClass = $isRtl ? 'rtl' : 'ltr';
    // تحديد فئة المحاذاة المناسبة
    $textAlignStart = $isRtl ? 'text-right' : 'text-left';
    $textAlignCenterOrStart = $isRtl ? 'text-right' : 'text-center md:text-left';
    // تحديد محاذاة عناصر القائمة للروابط السريعة
    $listItemAlignment = $isRtl ? 'items-end w-full' : 'items-center md:items-start';
    // تحديد عرض القائمة للغة العربية
    $listWidth = $isRtl ? 'w-full' : '';
    // تحديد ترتيب العناصر داخل الروابط
    $flexDirection = 'flex-row';

    $branding = \App\Models\Logo::first();
    $siteName = $branding->site_name ?? 'Pepasafe';
@endphp

<footer id="footer" class="bg-[#13162c] text-white py-12" dir="{{ $dirClass }}">
    <div class="container mx-auto px-6 lg:px-12">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-8 pb-10 border-b border-gray-700/50">

            {{-- العمود الأول: الشعار ووصف المنصة --}}
            <div class="flex flex-col items-center md:items-start text-center md:{{ $textAlignStart }}">
                <div class="flex-shrink-0 mb-4">
                    @if($branding && $branding->path)
                        <img src="{{ asset('storage/' . $branding->path) }}"
                             alt="{{ trans_db('logo.alt') }}"
                             class="h-10 object-contain bg-transparent [clip-path:inset(8%)]" />
                    @else
                        <img src="{{ asset('assets/logo.jpg') }}" alt="{{ trans_db('logo.default_alt') }}" class="h-8 md:h-10">
                    @endif
                </div>
                <h3 class="text-lg font-semibold mb-2">{{ trans_db('footer.about_pepasafe_title') }}</h3>
                <p class="text-sm text-gray-400 max-w-xs {{ $textAlignStart }}">
                    {{ trans_db('footer.about_pepasafe_desc') }}
                </p>
            </div>

            {{-- العمود الثاني: روابط سريعة --}}
            <div class="{{ $isRtl ? 'w-full' : '' }}">
                <h3 class="text-lg font-semibold mb-4 {{ $textAlignCenterOrStart }}">{{ trans_db('footer.quick_links_title') }}</h3>
                <ul class="space-y-3 text-sm flex flex-col {{ $listItemAlignment }}">
                    <li class="w-full">
                        <a href="{{ route('privacy') }}" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2 {{ $flexDirection }} {{ $isRtl ? 'justify-end' : '' }}">
                            <i class="fas fa-shield-alt text-blue-300"></i>
                            {{ trans_db('footer.privacy_policy') }}
                        </a>
                    </li>
                    <li class="w-full">
                        <a href="{{ route('terms') }}" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2 {{ $flexDirection }} {{ $isRtl ? 'justify-end' : '' }}">
                            <i class="fas fa-file-contract text-blue-300"></i>
                            {{ trans_db('footer.terms_and_conditions') }}
                        </a>
                    </li>
                    <li class="w-full">
                        <a href="{{ route('about') }}#contact" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2 {{ $flexDirection }} {{ $isRtl ? 'justify-end' : '' }}">
                            <i class="fas fa-headset text-blue-300"></i>
                            {{ trans_db('footer.contact_us') }}
                        </a>
                    </li>
                    <li class="w-full">
                        <a href="{{ route('about') }}#about-us" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2 {{ $flexDirection }} {{ $isRtl ? 'justify-end' : '' }}">
                            <i class="fas fa-info-circle text-blue-300"></i>
                            {{ trans_db('footer.about_us') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- العمود الثالث: تواصل معنا (بيانات الاتصال) --}}
            <div>
                <h3 class="text-lg font-semibold mb-4 {{ $textAlignCenterOrStart }}">{{ trans_db('footer.contact_title') }}</h3>
                <ul class="space-y-3 text-sm flex flex-col {{ $listItemAlignment }}">
                    <li>
                        <a href="mailto:info@example.com" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2 {{ $flexDirection }}">
                            <i class="fas fa-envelope text-blue-300"></i>
                            info@example.com
                        </a>
                    </li>
                    <li>
                        <a href="tel:+201234567890" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2 {{ $flexDirection }}">
                            <i class="fas fa-phone-alt text-blue-300"></i>
                            +20 123 456 7890
                        </a>
                    </li>
                </ul>
            </div>

            {{-- العمود الرابع: تابعنا على (السوشيال ميديا) --}}
            <div class="flex flex-col items-center md:items-start">
                <h3 class="text-lg font-semibold mb-4 {{ $textAlignStart }}">{{ trans_db('footer.follow_us_title') }}</h3>
                {{-- استخدام space-x-6 و rtl:space-x-reverse يحل مشكلة التباعد في كلا الاتجاهين --}}
                <div class="flex justify-center md:justify-start text-2xl {{ $isRtl ? 'space-x-4 rtl:space-x-reverse' : 'space-x-6' }}">
                    <a href="{{ trans_db('footer.facebook_link') }}" class="transition-colors duration-200 hover:text-blue-400" title="Facebook">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a href="{{ trans_db('footer.twitter_link') }}" class="transition-colors duration-200 hover:text-blue-400" title="Twitter">
                        <i class="fab fa-twitter-square"></i>
                    </a>
                    <a href="{{ trans_db('footer.instagram_link') }}" class="transition-colors duration-200 hover:text-blue-400" title="Instagram">
                        <i class="fab fa-instagram-square"></i>
                    </a>
                </div>
            </div>

        </div>

        {{-- حقوق النشر --}}
        <div class="mt-8 text-center text-xs text-gray-500 space-y-2">
            <p class="text-gray-600 text-xs">
                © 2025 {{ $siteName }}. {{ trans_db('footer.all_rights_reserved') }}
            </p>
            <p class="text-gray-600 text-xs">
                {{ trans_db('footer.developed_by') }} <span class="font-semibold">Ahmed Gamal</span>.
            </p>
        </div>

    </div>
</footer>
