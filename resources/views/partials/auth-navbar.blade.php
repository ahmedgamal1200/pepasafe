@php
    use App\Models\Setting;
@endphp

<nav class="bg-white p-4 flex justify-between items-center shadow-md">
    <!-- الجزء الأيمن (الشعار واسم الموقع، الرئيسية، محفظتي) - تم نقله ليكون الأول -->
    <div class="flex items-center space-x-4 rtl:space-x-reverse">
        <!-- شعار الموقع واسم الموقع -->
        <a href="{{ auth()->user()->hasRole('user') ? route('home.users') : route('home.eventor') }}" class="flex items-center space-x-2">
{{--        <div class="flex items-center space-x-2">--}}
            <img src=" {{ asset('assets/logo.jpg') }} " alt="Logo" class="h-8 md:h-10">
            <span class="text-gray-800 text-xl font-semibold hidden md:block">pepasafe</span>
        </a>
        <!-- روابط "الرئيسية" و "محفظتي" - تظهر على الشاشات الكبيرة فقط -->
{{--        <div class="hidden md:flex space-x-6 rtl:space-x-reverse">--}}
{{--            <a href="{{ auth()->user()->hasRole('user') ? route('home.users') : route('home.eventor') }}"--}}
{{--               class="text-gray-700 hover:text-blue-600 text-base font-medium">الرئيسية</a>--}}
{{--            @if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']))--}}
{{--                <a href="{{ route('wallet') }}" class="text-gray-700 hover:text-blue-600 text-base font-medium">محفظتي</a>--}}
{{--            @endif--}}
{{--        </div>--}}

        <div class="hidden md:flex space-x-6 rtl:space-x-reverse">
            <a href="{{ auth()->user()->hasRole('user') ? route('home.users') : route('home.eventor') }}"
               class="text-gray-700 hover:text-blue-600 text-base font-medium flex items-center space-x-2 rtl:space-x-reverse">
                <i class="fa-solid fa-house"></i>
                <span>الرئيسية</span>
            </a>
            @if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']))
                <a href="{{ route('wallet') }}" class="text-gray-700 hover:text-blue-600 text-base font-medium flex items-center space-x-2 rtl:space-x-reverse">
                    <i class="fa-solid fa-wallet"></i>
                    <span>محفظتي</span>
                </a>
            @endif
        </div>
    </div>

    <!-- الجزء الأيسر (تسجيل الدخول/البروفايل، عن المشروع، مبدل اللغة، زر قائمة الموبايل) - تم نقله ليكون الثاني -->
    <div class="flex items-center space-x-4 rtl:space-x-reverse">
        <!-- تسجيل الدخول أو الأفاتار والإشعارات -->
        @if(auth()->check())
            <!-- Avatar and Dropdown Container -->
            <div class="relative inline-block text-left">
                <button id="avatarButton" class="flex items-center focus:outline-none">
                    <img id="avatar" src="{{ auth()->user()->profile_picture? asset('storage/' . auth()->user()->profile_picture) : "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAMAAACahl6sAAAAMFBMVEXU1NT////Y2Nj7+/va2trm5ubz8/Pf39/29vbe3t7j4+P8/Pzt7e3Z2dn09PTp6enlgXfuAAAEj0lEQVR4nO2dCZarOgxEMVPCkGT/u31N8+mEEIIHVUmf47sC6ghNRhZFkclkMplMJpPJZDKZTCaTyWQymUwmk8lsKLuu75sf+r7rSu2niaNrxrZyK6p2bDrt5wqibtrB7TC0Ta39fH6Uj+ueiIXrw/5r1rdHKmbaXvtJv9JUxxL+PKbRfto9yhAZsxSTb1gfKONXir0XrPb0jXdaYyHssRtujxge2s/+wu0w4H7jetN+/oU+2hz/GcWIp4xpMiZGbQ0TkV6+ptVWUZR3CR3O3ZVTSpnk5q9cVZWUEUlwj0pRiZw9JhRtIuQfC3ctHSLx6hWl2PWQ1uGcSrlykdfh3IWvQzJgPVEIXeIOMkN3kwajwzlyA1wmFrz7DNyXS6Di3YNaCXc4Hc4xDyNFS5N3rjwdPVKHc7yGEWoQokkgOf0VVn4HG4RmEmjImuEELmAOWeDkEki1uKZi6ADH3hlGBAaVvWsYRTCsXHxlwOuAJ5EZfCoBdOqfwHfv8Gw4A8+JJUeHc+j+iuQieCeB9ervoHt3Qn0yg65SKOlwAp0SCYXWDLrcYulwDquDFn3R8bfmCcGORBC6wwVsl3gaIbTEjk7tlPZwBtsknsYip/GR0wg5TR45TYlynqKR1LLjm/bT9COk0yD8edBpDh9OcxzEClv4DwukYxT8px5S/Yv/QEJyEsJECiUlMr7rUg5NGcNOlHeLMutEqFI4c3SEuEUaq4HnRMpn9oLg7qy5RtxA4wxvrBFcy/PmsTHDywvMIWaol1Anf4F1CnE2s4Ae1JGv7sPaEvZNPpS/868r1JBkMijcQYaUXCqXXQFuonTVVTwGcyPvE2mH17tS2Yk6/KC4/KWTvOKqusSmFlNSKS9/kFKiraMobiJKKgN7HySuUOteZv8jOTOaWPkwcUl6vSqFC7p7lAmHdq2N12ohdjeKlZ0oT25RnjIaiFYbuuDwdbW6ke4S5CqtISff0Hi7ymB24VlR9mNQGK7G3lbA+qVsonaL3I1tb/PdBfgJO/sB67A3aks1qpe+P1xE1tXctSPYRW6bk6aUXnYJkpazyFnjT4qGVW6Qr9QtvfaKX8z4HfLaxph1n74Q14KmtFE+sFqttMbWB07zSxmhwx9H1KxLx+CqJXVtqT/YZp42vjwBDMS0i7ozKEeRXS/pA+YkVe4Lgj+IM3oNHQglOjrklWjpkFYi+a0wWIngcaSePX6ViNkEOzDnoUQoCvPzxztC+YR2P2wfkclscl3yGYFqhbbR5TvJZ/fEW8bfSQzC2gHrSWLoMuDoC0kOb8RBZhLcBDOAGUvC4KZ6JlwTPSlI7dB9iOzibb1YE5Evl6GItRAVuYi7XPyJOOyykwpfiUiLJmrFLcHVI/pCWCzBF8mMGiTYJFYNEmwSswYJNMnNrEF+TBLy4dewQYJMYtdDJgK8xFy1uMa/djSZ1J943xInLpqLw/frtcGyd41nEUzcVxqLn7sbd/UJP3c31ql/wqt7Jy7+i8en5zV1lrWHzxmX8E8OMXj8OvF/ELMmjuOWyTOHLcenEOaz4cxxTjRd+D7Z/KDkH+MbT03dnEr6AAAAAElFTkSuQmCC"}}"  width="40px"  alt="Avatar" class="h-8 w-8 rounded-full mr-6" />
                </button>

                <!-- تم تعديل موضع القائمة المنسدلة للبروفايل -->
                <div id="dropdownMenu" class="origin-top-left absolute left-0 right-auto mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-50">
                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                        @if (Setting::getValue('profile_page_enabled') === '1')
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }} text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            الملف الشخصي
                        </a>
                        @endif
                        @if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']))
                            <a href="{{ route('filament.admin.pages.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }} text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                لوحة التحكم
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }} text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25" />
                                </svg>
                                تسجيل الخروج
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            <div class="relative">
                <i class="bi bi-bell-fill text-2xl text-gray-700 cursor-pointer" id="notification-bell"></i>
                <span id="notif-count" class="absolute -top-1 -right-2 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold text-white bg-red-600 rounded-full">0</span>

                <!-- تم تعديل موضع القائمة المنسدلة للإشعارات -->
                <div id="notification-dropdown"
                     class="absolute mt-2 w-72 bg-white rounded-md shadow-lg py-1 z-50 hidden ltr:right-0 rtl:left-0">
                    <div id="notifications-list"></div>
                    <div class="text-center py-2">
                        <a href="#" id="mark-all-read" class="text-blue-600 hover:underline text-sm">قرأت كل الاشعارات</a>
                    </div>
                </div>
            </div>


        @else
            <!-- رابط تسجيل الدخول -->
            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-md
                              text-base font-medium transition-colors duration-200
                              hover:bg-blue-700">
                تسجيل الدخول
            </a>
        @endif

        <!-- رابط "عن المشروع" - يظهر على الشاشات الكبيرة فقط -->
        <a href="{{ route('about') }}"
           class="hidden md:block text-gray-700 hover:text-blue-600 text-base font-medium flex items-center space-x-2 rtl:space-x-reverse">
            <i class="fa-solid fa-circle-info"></i>
            <span>عن المشروع</span>
        </a>

        <!-- مبدل اللغة -->
        <button id="language-switcher" class="flex items-center text-gray-700 hover:text-blue-600 text-sm font-medium focus:outline-none transition-colors duration-200">
            <i class="fas fa-globe text-lg mr-1 rtl:ml-1 rtl:mr-0"></i> <!-- استخدام Font Awesome لأيقونة الكرة الأرضية -->
            <span id="current-language">EN</span>
        </button>

        <!-- زر قائمة الموبايل (يظهر على الشاشات الصغيرة فقط) -->
        <button id="mobile-menu-toggle" class="md:hidden text-gray-700 focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
</nav>

<!-- قائمة الموبايل (تظهر عند النقر على أيقونة الهمبرغر) -->
<div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg py-2">
    <a href="{{ route('home.eventor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">الرئيسية</a>
    @if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']))
        <a href="{{ route('wallet') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">محفظتي</a>
    @endif
    <a href="{{ route('about') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">عن المشروع</a>
</div>

{{--@vite('resources/js/app.js')--}}


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- كود الإشعارات الحالي ---
        const bellIcon = document.getElementById('notification-bell');
        const dropdown = document.getElementById('notification-dropdown');
        const notifCountSpan = document.getElementById('notif-count');
        const notificationsList = document.getElementById('notifications-list');
        const markAllReadBtn = document.getElementById('mark-all-read');

        // Check if elements exist before proceeding with notification logic
        if (bellIcon && dropdown && notifCountSpan && notificationsList && markAllReadBtn) {
            const userNotifications = @json($notifications ?? []); // Provide a default empty array if $notifications is not set

            // دالة لتحديث عرض الإشعارات في الـ UI
            function updateNotificationsUI() {
                const unreadCount = userNotifications.filter(n => !n.read_at).length;
                notifCountSpan.textContent = unreadCount;
                if (unreadCount === 0) {
                    notifCountSpan.classList.add('hidden');
                } else {
                    notifCountSpan.classList.remove('hidden');
                }

                notificationsList.innerHTML = '';
                if (userNotifications.length > 0) {
                    userNotifications.forEach((notification, index) => {
                        const notificationData = notification.data;
                        const notificationId = notification.id;

                        const createdAt = new Date(notification.created_at).toLocaleString('ar-EG', {
                            year: 'numeric',
                            month: 'numeric',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric',
                            hour12: true
                        });

                        const notificationItem = document.createElement('a');
                        notificationItem.href = "#";
                        notificationItem.classList.add('block', 'px-4', 'py-2', 'text-sm', 'text-gray-700', 'hover:bg-gray-100');

                        if (!notification.read_at) {
                            notificationItem.classList.add('font-semibold', 'bg-blue-50');
                        }

                        notificationItem.innerHTML = `
                            <p class="text-xs text-gray-800 break-words">${notificationData.message}</p>
                            <span class="text-xs text-gray-400">${createdAt}</span>
                        `;

                        notificationItem.addEventListener('click', function(event) {
                            event.preventDefault();
                            if (!notification.read_at) {
                                // هنستخدم jQuery AJAX هنا
                                // Make sure jQuery is loaded for this to work
                                if (typeof jQuery !== 'undefined') {
                                    markSingleNotificationAsRead(notificationId, notificationItem);
                                } else {
                                    console.warn("jQuery is not loaded. Cannot mark notification as read.");
                                }
                            }
                        });

                        notificationsList.appendChild(notificationItem);

                        if (index < userNotifications.length - 1) {
                            const hr = document.createElement('hr');
                            hr.classList.add('border-gray-200', 'my-1');
                            notificationsList.appendChild(hr);
                        }
                    });
                } else {
                    const noNotificationsDiv = document.createElement('div');
                    noNotificationsDiv.classList.add('px-4', 'py-2', 'text-sm', 'text-gray-500', 'text-center');
                    noNotificationsDiv.textContent = 'لا يوجد إشعارات جديدة.';
                    notificationsList.appendChild(noNotificationsDiv);
                    markAllReadBtn.classList.add('hidden');
                }
            }

            // دالة لتعليم إشعار واحد كمقروء باستخدام jQuery AJAX
            function markSingleNotificationAsRead(notificationId, notificationElement) {
                $.ajax({
                    url: `/notifications/${notificationId}/mark-as-read`, // اسم الراوت اللي عرفناه في web.php
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // لازم تبعت الـ CSRF token
                    },
                    success: function(response) {
                        if (response.message) {
                            notificationElement.classList.remove('font-semibold', 'bg-blue-50');
                            const notificationIndex = userNotifications.findIndex(n => n.id === notificationId);
                            if (notificationIndex !== -1) {
                                userNotifications[notificationIndex].read_at = new Date().toISOString();
                            }
                            updateNotificationsUI();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to mark notification as read:', xhr.responseText);
                    }
                });
            }

            // دالة لتعليم كل الإشعارات كمقروءة باستخدام jQuery AJAX
            markAllReadBtn.addEventListener('click', function(event) {
                event.preventDefault();
                if (typeof jQuery !== 'undefined') {
                    $.ajax({
                        url: `/notifications/mark-all-as-read`, // اسم الراوت اللي عرفناه في web.php
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.message) {
                                userNotifications.forEach(n => {
                                    n.read_at = new Date().toISOString();
                                });
                                updateNotificationsUI();

                            }
                        },
                        error: function(xhr) {
                            console.error('Failed to mark all notifications as read:', xhr.responseText);
                        }
                    });
                } else {
                    console.warn("jQuery is not loaded. Cannot mark all notifications as read.");
                }
            });

            updateNotificationsUI();

            bellIcon.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(event) {
                if (!dropdown.contains(event.target) && !bellIcon.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        const userId = @json(auth()->user()->id);

        if (typeof window.Echo !== 'undefined') {
            window.Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    userNotifications.unshift({
                        id: crypto.randomUUID(),
                        data: notification,
                        read_at: null,
                        created_at: new Date().toISOString()
                    });

                    updateNotificationsUI();

                    console.log('🔔 Real-Time Notification Received:', notification);
                });
        } else {
            console.warn("❌ Echo is not loaded. Real-time notifications will not work.");
        }

        // --- نهاية كود الإشعارات ---

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

        // --- القائمة المنسدلة للبروفايل (تم دمجها هنا) ---
        const avatarButton = document.getElementById('avatarButton');
        const dropdownMenu = document.getElementById('dropdownMenu');

        // تحقق من وجود الأزرار والقائمة قبل إضافة المستمعين لتجنب الأخطاء إذا لم يكن المستخدم مسجلاً الدخول
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

            dropdownMenu.querySelectorAll('a, button[type="submit"]').forEach(item => { // Added button[type="submit"] for logout form
                item.addEventListener('click', () => {
                    // Allow logout form submission to proceed
                    if (item.tagName === 'BUTTON' && item.type === 'submit') {
                        // Do nothing, let the form submit
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

