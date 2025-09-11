<footer id="footer" class="bg-[#13162c] text-white py-12">
    <div class="container mx-auto px-6 lg:px-12">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-8 pb-10 border-b border-gray-700/50">

            <div class="flex flex-col items-center md:items-start text-center md:text-left">
                <div class="flex-shrink-0 mb-4">
                    @php
                        $branding = \App\Models\Logo::first();
                    @endphp

                    @if($branding && $branding->path)
                        <img src="{{ asset('storage/' . $branding->path) }}"
                             alt="Logo"
                             class="h-10 object-contain bg-transparent [clip-path:inset(8%)]" />
                    @else
                        <img src="{{ asset('assets/logo.jpg') }}" alt="Default Logo" class="h-8 md:h-10">
                    @endif
                </div>
                <h3 class="text-lg font-semibold mb-2">عن Pepasafe</h3>
                <p class="text-sm text-gray-400 max-w-xs">
                    منصّة تُصدر تصميماتك بهويتك البصرية مع ختم تحقق رقمي (QR + كود فريد) يثبت الملكية والأصالة عند الفحص، مع تنزيل جاهز للطباعة/المشاركة ومشاركة تشغيلية عبر القنوات المعتمدة عند التفعيل.
                </p>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4 text-center md:text-left">روابط سريعة</h3>
                <ul class="space-y-3 text-sm flex flex-col items-center md:items-start">
                    <li>
                        <a href="{{ route('privacy') }}" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-blue-300"></i>
                            سياسة الخصوصية
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms') }}" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2">
                            <i class="fas fa-file-contract text-blue-300"></i>
                            الشروط والأحكام
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}#contact" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2">
                            <i class="fas fa-headset text-blue-300"></i>
                            تواصل معنا
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}#about-us" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-300"></i>
                            من نحن
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4 text-center md:text-left">تواصل معنا</h3>
                <ul class="space-y-3 text-sm flex flex-col items-center md:items-start">
                    <li>
                        <a href="mailto:info@example.com" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2">
                            <i class="fas fa-envelope text-blue-300"></i>
                            info@example.com
                        </a>
                    </li>
                    <li>
                        <a href="tel:+201234567890" class="transition-colors duration-200 hover:text-blue-400 flex items-center gap-2">
                            <i class="fas fa-phone-alt text-blue-300"></i>
                            +20 123 456 7890
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex flex-col items-center md:items-start">
                <h3 class="text-lg font-semibold mb-4">تابعنا على</h3>
                <div class="flex justify-center md:justify-start text-2xl space-x-6 rtl:space-x-reverse">
                    <a href="#" class="transition-colors duration-200 hover:text-blue-400">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a href="#" class="transition-colors duration-200 hover:text-blue-400">
                        <i class="fab fa-twitter-square"></i>
                    </a>
                    <a href="#" class="transition-colors duration-200 hover:text-blue-400">
                        <i class="fab fa-instagram-square"></i>
                    </a>
                </div>
            </div>

        </div>

        <div class="mt-8 text-center text-xs text-gray-500 space-y-2">
            <p class="text-gray-600 text-xs">
                © 2025 {{ $branding->site_name ?? 'Pepasafe' }}. All rights reserved.
            </p>
            <p class="text-gray-600 text-xs">
                Developed by <span class="font-semibold">Ahmed Gamal</span>.
            </p>
        </div>

    </div>
</footer>
