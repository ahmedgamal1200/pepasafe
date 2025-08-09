@if(isset($showCookieConsent) && $showCookieConsent)
    <div id="cookie-consent" class="fixed bottom-4 left-4 z-50">
        <div class="bg-white p-4 rounded-lg shadow-lg max-w-sm border border-gray-200">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <i class="fas fa-cookie-bite text-blue-500 text-2xl mr-2"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Cookies Consent</h3>
                </div>
                {{-- تم تعديل الكلاس ليتحول للأحمر عند الوقوف عليه --}}
                <button onclick="document.getElementById('cookie-consent').remove()" class="text-gray-400 hover:text-red-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                We use cookies to ensure you get the best experience on **pepsafe** website.
            </p>
            <div class="flex space-x-2 justify-end">
                {{-- تم تعديل الكلاس ليتحول للأحمر عند الوقوف عليه --}}
                <button onclick="declineCookies()" class="text-gray-500 hover:text-red-500 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm">
                    Decline
                </button>
                <button onclick="acceptCookies()" class="bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded-lg text-sm">
                    Accept
                </button>
            </div>
        </div>
    </div>

    <script>
        function acceptCookies() {
            fetch("{{ route('cookie.accept') }}")
                .then(() => document.getElementById('cookie-consent').remove());
        }

        function declineCookies() {
            document.getElementById('cookie-consent').remove();
        }
    </script>
@endif
