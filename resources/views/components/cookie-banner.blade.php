@if(isset($showCookieConsent) && $showCookieConsent)
    <div id="cookie-consent" class="fixed bottom-4 left-4 z-50">
        {{-- البانر الأصلي --}}
        <div id="initial-consent-banner" class="bg-white p-4 rounded-lg shadow-lg max-w-sm border border-gray-200">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <i class="fas fa-cookie-bite text-blue-500 text-2xl mr-2"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Cookies Consent</h3>
                </div>
                <button onclick="document.getElementById('cookie-consent').remove()" class="text-gray-400 hover:text-red-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                We use cookies to ensure you get the best experience on <strong>Pepasafe</strong> website.
            </p>
            <div class="flex space-x-2 justify-end">
                <button onclick="declineCookies()" class="text-gray-500 hover:text-red-500 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm">
                    Decline
                </button>
                <button onclick="showCustomizationOptions()" class="text-gray-500 hover:text-blue-500 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm">
                    Customize
                </button>
                <button onclick="acceptCookies()" class="bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded-lg text-sm">
                    Accept
                </button>
            </div>
        </div>

        {{-- بانر الخيارات المخصصة (مخفي مبدئياً) --}}
        <div id="custom-consent-banner" class="hidden bg-white p-4 rounded-lg shadow-lg max-w-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Customize your cookies</h3>
            <p class="text-sm text-gray-600 mb-4">
                You can choose which types of cookies to accept.
            </p>

            {{-- خيارات الكوكيز --}}
            <div class="space-y-4">
                <label class="flex items-center">
                    <input type="checkbox" id="essential-cookies" checked disabled class="form-checkbox text-blue-500">
                    <span class="ml-2 text-gray-800">Essential Cookies <span class="text-xs text-gray-500">(Required)</span></span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" id="analytics-cookies" class="form-checkbox text-blue-500">
                    <span class="ml-2 text-gray-800">Analytics Cookies</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" id="marketing-cookies" class="form-checkbox text-blue-500">
                    <span class="ml-2 text-gray-800">Marketing Cookies</span>
                </label>
            </div>

            <div class="flex justify-end mt-4">
                <button onclick="saveCustomCookies()" class="bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded-lg text-sm">
                    Save Preferences
                </button>
            </div>
        </div>
    </div>

    <script>
        function acceptCookies() {
            // ... (نفس الكود اللي بيعمل fetch وبيضيف الكوكي 'user_consent=accepted')
            fetch("{{ route('cookie.accept') }}")
                .then(() => {
                    const date = new Date();
                    date.setFullYear(date.getFullYear() + 1);
                    const expires = "expires=" + date.toUTCString();
                    document.cookie = "user_consent=accepted;" + expires + ";path=/";
                    document.getElementById('cookie-consent').remove();
                })
                .catch(error => {
                    console.error('Error accepting cookies:', error);
                    document.getElementById('cookie-consent').remove();
                });
        }

        function declineCookies() {
            // ... (نفس الكود اللي بيعمل fetch وبيضيف الكوكي 'user_consent=declined')
            // ممكن هنا تبعت طلب للـ backend عشان يحذف كل الكوكيز اللي مش ضرورية
            document.getElementById('cookie-consent').remove();
        }

        function showCustomizationOptions() {
            // إخفاء البانر الأول
            document.getElementById('initial-consent-banner').classList.add('hidden');
            // إظهار بانر الخيارات المخصصة
            document.getElementById('custom-consent-banner').classList.remove('hidden');
        }

        function saveCustomCookies() {
            const analytics = document.getElementById('analytics-cookies').checked;
            const marketing = document.getElementById('marketing-cookies').checked;

            fetch("{{ route('cookie.custom') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    analytics: analytics,
                    marketing: marketing
                })
            })
                .then(() => {
                    // هنا بيتم إخفاء البانر فقط بعد نجاح الطلب
                    document.getElementById('cookie-consent').remove();
                })
                .catch(error => {
                    // في حالة وجود خطأ، بنظهره في الـ console
                    console.error('Error saving custom cookies:', error);
                    // وممكن برضه تخفي البانر في حالة الخطأ عشان مايفضلش ظاهر
                    document.getElementById('cookie-consent').remove();
                });
        }
    </script>
@endif
