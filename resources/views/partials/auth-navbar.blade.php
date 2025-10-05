@php
    use App\Models\Logo;use App\Models\Setting;
    // Direction Variables
    $isRtl = (app()->getLocale() === 'ar');
    $dirAttr = $isRtl ? 'rtl' : 'ltr';
    // Spacing between icon and text in links/dropdowns
    $iconTextSpacing = $isRtl ? 'ml-3' : 'mr-3';
    // Dropdown alignment for Notification/Profile panels
    $dropdownAlignment = $isRtl ? 'left-0 right-auto origin-top-left' : 'right-0 left-auto origin-top-right';

    $branding = Logo::first();
    $logoUrl = ($branding && $branding->path) ? asset('storage/' . $branding->path) : asset('assets/logo.jpg');
    $siteName = $branding->site_name ?? 'pepasafe';

    // Notification text uses a prefix key
    $notificationSiteName = trans_db('nav.notification_from_prefix') . ' ' . ($branding->site_name ?? 'Pepafase');

    // Fetch notifications (limit to top 5 unread for panel preview)
    $notifications = auth()->check() ? auth()->user()->unreadNotifications->take(5) : collect();

@endphp

<nav class="bg-white p-4 flex justify-between items-center shadow-md" dir="{{ $dirAttr }}">
    {{-- 1. القسم الأيمن/الأيسر: الشعار واسم الموقع (Logo) --}}
    <div class="flex items-center space-x-4 rtl:space-x-reverse flex-shrink-0">
        <a href="{{ auth()->user() && auth()->user()->hasRole('user') ? route('home.users') : route('home.eventor') }}"
           class="flex items-center space-x-2 rtl:space-x-reverse">
            <img src="{{ $logoUrl }}" alt="{{ $siteName }} Logo" class="h-8 md:h-10">

            <span class="text-gray-800 text-xl font-semibold hidden md:block">
                {{ $siteName }}
            </span>
        </a>
    </div>

    {{-- 2. القسم الأوسط: روابط الصفحات الرئيسية (Links) --}}
    <div class="hidden md:flex flex-1 justify-center space-x-6 rtl:space-x-reverse">
        <a href="{{ auth()->user() && auth()->user()->hasRole('user') ? route('home.users') : route('home.eventor') }}"
           class="text-gray-700 hover:text-blue-600 text-base font-medium flex items-center space-x-2 rtl:space-x-reverse">
            <i class="fa-solid fa-house"></i>
            <span>{{ trans_db('nav.home') }}</span>
        </a>

        @if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']))
            <a href="{{ route('wallet') }}"
               class="text-gray-700 hover:text-blue-600 text-base font-medium flex items-center space-x-2 rtl:space-x-reverse">
                <i class="fa-solid fa-wallet"></i>
                <span>{{ trans_db('nav.wallet') }}</span>
            </a>
        @endif

        <a href="{{ route('about') }}"
           class="text-gray-700 hover:text-blue-600 text-base font-medium flex items-center space-x-2 rtl:space-x-reverse">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ trans_db('nav.about') }}</span>
        </a>
    </div>

    {{-- 3. القسم الأيسر/الأيمن: إشعارات، بروفايل، لغة، وزر تسجيل الدخول (Icons & Actions) --}}
    <div class="flex items-center space-x-4 rtl:space-x-reverse flex-shrink-0">

        {{-- مُبدّل اللغة (Language Switcher) --}}
        <button id="language-switcher"
                class="flex items-center text-gray-700 hover:text-blue-600 text-sm font-medium focus:outline-none transition-colors duration-200">
            {{-- `mr-1 rtl:ml-1 rtl:mr-0` for correct icon spacing relative to text --}}
            <i class="fas fa-globe text-lg mr-1 rtl:ml-1 rtl:mr-0"></i>
            {{-- Display the language to switch TO --}}
            <span id="current-language">{{ $isRtl ? 'EN' : 'العربية' }}</span>
        </button>

        @if(auth()->check())
            {{-- أيقونة الإشعارات (Notifications) --}}
            <div class="relative">
                <div class="relative">
                    <button id="notification-bell" onclick="document.getElementById('notification-panel').classList.toggle('hidden')" class="relative text-gray-500 hover:text-red-600 transition-colors" title="{{ trans_db('nav.notifications_title') }}">
                        <i class="fas fa-bell text-2xl"></i>
                        <span id="notification-badge"
                              class="absolute -top-2 ltr:-right-2 rtl:-left-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center {{ Auth::user()->unreadNotifications->count() == 0 ? 'hidden' : '' }}">
                                {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                    </button>

                    {{-- لوحة الإشعارات (Notification Panel) --}}
                    <div id="notification-panel"
                         class="hidden absolute {{ $dropdownAlignment }}
                                mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-20">

                        <div class="p-3 bg-gray-50 border-b flex justify-between items-center">
                            <h3 class="font-bold text-gray-700">{{ trans_db('nav.notifications_title') }}</h3>
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <button onclick="window.markAllNotificationsAsRead()" title="{{ trans_db('nav.mark_all_read') }}" class="text-gray-400 hover:text-blue-500">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            </div>
                        </div>
                        <div id="notification-list" class="max-h-96 overflow-y-auto">
                            @forelse($notifications as $notification)
                                <div class="notification-card p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                                     data-id="{{ $notification->id }}"
                                     data-read="{{ $notification->read_at ? 'true' : 'false' }}">
                                    <div class="flex items-start space-x-3 rtl:space-x-reverse">
                                        <div class="flex-shrink-0">
                                            @if($logoUrl)
                                                <img src="{{ $logoUrl }}" alt="Logo" class="w-8 h-8 rounded-full">
                                            @else
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-info-circle text-sm"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $notificationSiteName }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {!! $notification->data['message'] ?? '' !!}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="p-3 text-gray-500 text-sm">{{ trans_db('nav.no_notifications') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- قائمة البروفايل المنسدلة (Avatar & Dropdown) --}}
            <div class="relative inline-block text-left">
                <button id="avatarButton" class="flex items-center focus:outline-none">
                    <img id="avatar"
                         src="{{ auth()->user()->profile_picture? asset('storage/' . auth()->user()->profile_picture) : "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAMAAACahl6sAAAAMFBMVEXU1NT////Y2Nj7+/va2trm5ubz8/Pf39/29vbe3t7j4+P8/Pzt7e3Z2dn09PTp6enlgXfuAAAEj0lEQVR4nO2dCZarOgxEMVPCkGT/u31N8+mEEIIHVUmf47sC6ghNRhZFkclkMplMJpPJZDKZTCaTyWQymUwmk8lsKLuu75sf+r7rSu2niaNrxrZyK6p2bDrt5wqibtrB7TC0Ta39fH6Uj+ueiIXrw/5r1rdHKmbaXvtJv9JUxxL+PKbRfto9yhAZsxSTb1gfKONXir0XrPb0jXdaYyHssRtujxge2s/+wu0w4H7jetN+/oU+2hz/GcWIp4xpMiZGbQ0TkV6+ptVWUZR3CR3O3ZVTSpnk5q9cVZWUEUlwj0pRiZw9JhRtIuQfC3ctHSLx6hWl2PWQ1uGcSrlykdfh3IWvQzJgPVEIXeIOMkN3kwajwzlyA1wmFrz7DNyXS6Di3YNaCXc4Hc4xDyNFS5N3rjwdPVKHc7yGEWoQokkgOf0VVn4HG4RmEmjImuEELmAOWeDkEki1uKZi6ADH3hlGBAaVvWsYRTCsXHxlwOuAJ5EZfCoBdOqfwHfv8Gw4A8+JJUeHc+j+iuQieCeB9ervoHt3Qn0yg65SKOlwAp0SCYXWDLrcYulwDquDFn3R8bfmCcGORBC6wwVsl3gaIbTEjk7tlPZwBtsknsYip/GR0wg5TR45TYlynqKR1LLjm/bT9COk0yD8edBpDh9OcxzEClv4DwukYxT8px5S/Yv/QEJyEsJECiUlMr7rUg5NGcNOlHeLMutEqFI4c3SEuEUaq4HnRMpn9oLg7qy5RtxA4wxvrBFcy/PmsTHDywvMIWaol1Anf4F1CnE2s4Ae1JGv7sPaEvZNPpS/868r1JBkMijcQYaUXCqXXQFuonTVVTwGcyPvE2mH17tS2Yk6/KC4/KWTvOKqusSmFlNSKS9/kFKiraMobiJKKgN7HySuUOteZv8jOTOaWPkwcUl6vSqFC7p7lAmHdq2N12ohdjeKlZ0oT25RnjIaiFYbuuDwdbW6ke4S5CqtISff0Hi7ymB24VlR9mNQGK7G3lbA+qVsonaL3I1tb/PdBfgJO/sB67A3aks1qpe+P1xE1tXctSPYRW6bk6aUXnYJkpazyFnjT4qGVW6Qr9QtvfaKX8z4HfLaxph1n74Q14KmtFE+sFqttMbWB07zSxmhwx9H1KxLx+CqJXVtqT/YZp42vjwBDMS0i7ozKEeRXS/pA+YkVe4Lgj+IM3oNHQglOjrklWjpkFYi+a0wWIngcaSePX6ViNkEOzDnoUQoCvPzxztC+YR2P2wfkclscl3yGYFqhbbR5TvJZ/fEW8bfSQzC2gHrSWLoMuDoC0kOb8RBZhLcBDOAGUvC4KZ6JlwTPSlI7dB9iOzibb1YE5Evl6GItRAVuYi7XPyJOOyykwpfiUiLJmrFLcHVI/pCWCzBF8mMGiTYJFYNEmwSswYJNMnNrEF+TBLy4dewQYJMYtdDJgK8xFy1uMa/djSZ1J943xInLpqLw/frtcGyd41nEUzcVxqLn7sbd/UJP3c31ql/wqt7Jy7+i8en5zV1lrWHzxmX8E8OMXj8OvF/ELMmjuOWyTOHLcenEOaz4cxxTjRd+D7Z/KDkH+MbT03dnEr6AAAAAElFTkSuQmCC"}}"
                         width="40px" alt="{{ trans_db('nav.avatar_alt') }}" class="h-8 w-8 rounded-full"/>
                </button>

                {{-- القائمة المنسدلة (Dropdown Menu) --}}
                <div id="dropdownMenu"
                     class="absolute mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-50 {{ $dropdownAlignment }}">
                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                        @if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin', 'user']))
                            @if (Setting::getValue('profile_page_enabled') === '1')
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                   role="menuitem">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor"
                                         class="w-5 h-5 {{ $iconTextSpacing }} text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ trans_db('nav.profile') }}
                                </a>
                            @endif
                        @endif
                        @if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']))
                            <a href="{{ route('filament.admin.pages.dashboard') }}"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               role="menuitem">
                                <svg class="w-5 h-5 {{ $iconTextSpacing }} text-gray-400"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924-1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ trans_db('nav.dashboard') }}
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full {{ $isRtl ? 'text-right' : 'text-left' }}"
                                    role="menuitem">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor"
                                     class="w-5 h-5 {{ $iconTextSpacing }} text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25"/>
                                </svg>
                                {{ trans_db('nav.logout') }}
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        @else
            {{-- زر تسجيل الدخول (Login Button) --}}
            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md
                                     text-base font-medium transition-colors duration-200
                                     hover:bg-blue-700">
                {{ trans_db('nav.login') }}
            </a>
        @endif

        {{-- قائمة الموبايل (Hamburger Menu) --}}
        <button id="mobile-menu-toggle" class="md:hidden text-gray-700 focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
</nav>

{{-- قائمة الموبايل (Mobile Menu) --}}
<div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg py-2" dir="{{ $dirAttr }}">
    <a href="{{ auth()->user() && auth()->user()->hasRole('user') ? route('home.users') : route('home.eventor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">{{ trans_db('nav.home') }}</a>
    @if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']))
        <a href="{{ route('wallet') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">{{ trans_db('nav.wallet') }}</a>
    @endif
    <a href="{{ route('about') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">{{ trans_db('nav.about') }}</a>
</div>

{{-- الأكواد البرمجية (Scripts) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ** إضافة قيمة اللغة الحالية من Blade **
        // ** إضافة قيمة اللغة الحالية من Blade **
        const isRtl = {{ $isRtl ? 'true' : 'false' }};
        // targetLangDisplay: اللغة التي سيتم التبديل إليها (النص الظاهر على الزر)
        const targetLangDisplay = isRtl ? 'EN' : 'العربية';

        // ** alertPrefix: نص التنبيه المترجم للغة الحالية **
        const alertPrefix = '{{ trans_db("nav.language_switch_alert_prefix") }}';

        // --- الجزء الخاص باللغة ---
        const languageSwitcher = document.getElementById('language-switcher');
        const currentLanguageSpan = document.getElementById('current-language');

        if (languageSwitcher && currentLanguageSpan) {
            languageSwitcher.addEventListener('click', () => {
                if (currentLanguageSpan.textContent === 'EN') {
                    currentLanguageSpan.textContent = 'العربية';
                } else {
                    currentLanguageSpan.textContent = 'EN';
                }
            });
        }
        // --- نهاية الجزء الخاص باللغة ---

        // --- جزء قائمة الموبايل ---
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
        // --- نهاية جزء قائمة الموبايل ---

        // --- القائمة المنسدلة للبروفايل ---
        const avatarButton = document.getElementById('avatarButton');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (avatarButton && dropdownMenu) {
            const toggleDropdown = () => {
                dropdownMenu.classList.toggle('hidden');
            };

            avatarButton.addEventListener('click', toggleDropdown);

            document.addEventListener('click', (event) => {
                if (!avatarButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    if (!dropdownMenu.classList.contains('hidden')) {
                        dropdownMenu.classList.add('hidden');
                    }
                }
            });

            dropdownMenu.querySelectorAll('a, button[type="submit"]').forEach(item => {
                item.addEventListener('click', () => {
                    if (item.tagName === 'BUTTON' && item.type === 'submit') {
                        // Allow logout form submission to proceed
                    } else {
                        if (!dropdownMenu.classList.contains('hidden')) {
                            dropdownMenu.classList.add('hidden');
                        }
                    }
                });
            });
        }
        // --- نهاية القائمة المنسدلة للبروفايل ---
    });
</script>

@auth
    <script src=" {{ asset('js/notifications.js') }}"></script>
@endauth
