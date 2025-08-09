<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Share Your Profile With Others') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Copy the link below or share it directly.') }}
        </p>
    </header>

    <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input id="profile-link" type="text" readonly
               value="{{ route('showProfileToGuest', $user->slug) }}"
               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-500 text-sm px-3 py-2">

        {{-- زر النسخ --}}
        <button onclick="copyProfileLink()"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition text-sm flex items-center justify-center gap-1">
            <i class="bi bi-clipboard"></i> {{ __('Copy') }}
        </button>

        {{-- زر الشير --}}
        <button onclick="shareProfileLink()"
                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition text-sm flex items-center justify-center gap-1">
            <i class="bi bi-share-fill"></i> {{ __('Share') }}
        </button>
    </div>

    <p id="copy-success" class="text-green-600 text-sm mt-2 hidden">
        {{ __('Link copied to clipboard!') }}
    </p>

    <script>
        function copyProfileLink() {
            const input = document.getElementById('profile-link');
            input.select();
            input.setSelectionRange(0, 99999); // For mobile

            navigator.clipboard.writeText(input.value).then(function () {
                document.getElementById('copy-success').classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('copy-success').classList.add('hidden');
                }, 2000);
            });
        }

        function shareProfileLink() {
            const link = document.getElementById('profile-link').value;

            if (navigator.share) {
                navigator.share({
                    title: 'Check my profile',
                    text: 'View my public profile on PepaSafe:',
                    url: link,
                }).catch((err) => {
                    console.error('Share failed:', err);
                });
            } else {
                // fallback: copy link
                copyProfileLink();
                alert('تم نسخ الرابط لأنه لا يمكن مشاركة الرابط مباشرة في هذا المتصفح.');
            }
        }
    </script>
</section>

