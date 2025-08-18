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
        <a id="profile-link" href="{{ route('showProfileToGuest', $user->slug) }}"
           class="flex-1 text-blue-600 font-semibold text-sm px-4 py-2 break-all underline">
            {{ route('showProfileToGuest', $user->slug) }}
        </a>

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

        {{-- QR Code --}}
        @if($user->qr_code)
            <div class="flex flex-col items-center transform -translate-y-15 translate-x-8">

            <!-- أضفنا id لهذا الـ div ليتم تحويله إلى صورة -->
                <div id="qr-card-to-download" class="p-4 bg-white rounded-lg shadow-lg">
                    <h3 class="text-sm font-semibold text-blue-600 mb-2 text-center">
                        {{ __('Profile QR Code') }}
                    </h3>
                    <div class="flex flex-col items-center">
                        <img src="{{ asset('storage/' . $user->qr_code) }}" alt="QR Code"
                             class="w-32 h-32 object-contain">
                        <p class="mt-2 text-sm font-medium text-blue-600">{{ '@' . $user->slug }}</p>
                    </div>
                </div>

                <div class="flex mt-4 space-x-4">
                    <!-- استبدلنا زر التحميل الحالي بـ button مع id -->
                    <button id="download-button" class="p-2 rounded-full text-blue-500 hover:text-blue-700 hover:bg-blue-50 transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>
                    <button id="share-qr-button" type="button" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </button>
                    <button id="scan-qr-button" type="button" class="p-2 rounded-full text-green-500 hover:text-green-700 hover:bg-green-50 transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v-1a2 2 0 0 1 2 -2h2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 17v1a2 2 0 0 0 2 2h2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 4h2a2 2 0 0 1 2 2v1" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div id="scanner-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center p-4">
            <div class="bg-white rounded-lg p-6 shadow-xl w-full max-w-sm">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-bold">QR Code Scanner</h4>
                    <button id="close-scanner-modal" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>
                <div id="reader" style="width: 100%;"></div>
            </div>
        </div>

        <!-- أضفنا مكتبة html2canvas هنا -->
        <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const shareButton = document.getElementById('share-qr-button');
                const qrCodeUrl = "{{ asset('storage/' . $user->qr_code) }}";
                const username = "{{ $user->username }}";

                if (navigator.share) {
                    shareButton.addEventListener('click', async () => {
                        try {
                            await navigator.share({
                                title: `QR Code from ${username}`,
                                text: `Check out my profile QR Code on Pepasafe!`,
                                url: qrCodeUrl
                            });
                            console.log('Shared successfully');
                        } catch (error) {
                            console.error('Sharing failed:', error);
                        }
                    });
                } else {
                    shareButton.style.display = 'none';
                }

                const scannerModal = document.getElementById('scanner-modal');
                const scanButton = document.getElementById('scan-qr-button');
                const closeButton = document.getElementById('close-scanner-modal');
                let html5QrCode;

                if (scanButton) {
                    scanButton.addEventListener('click', () => {
                        scannerModal.classList.remove('hidden');
                        scannerModal.classList.add('flex');

                        html5QrCode = new Html5Qrcode("reader");
                        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                            window.location.href = decodedText;
                        };

                        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                        html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                            .catch(err => {
                                console.error("Failed to start QR code scanner:", err);
                            });
                    });

                    closeButton.addEventListener('click', () => {
                        if (html5QrCode) {
                            html5QrCode.stop().then(() => {
                                scannerModal.classList.add('hidden');
                                scannerModal.classList.remove('flex');
                            }).catch(err => {
                                console.error("Error stopping scanner:", err);
                                scannerModal.classList.add('hidden');
                                scannerModal.classList.remove('flex');
                            });
                        } else {
                            scannerModal.classList.add('hidden');
                            scannerModal.classList.remove('flex');
                        }
                    });
                }

                // ⭐⭐ الكود الجديد الخاص بالتحميل ⭐⭐
                const downloadButton = document.getElementById('download-button');
                const qrCard = document.getElementById('qr-card-to-download');

                if (downloadButton && qrCard) {
                    downloadButton.addEventListener('click', () => {
                        html2canvas(qrCard, {
                            // يمكنك إضافة خيارات هنا لتحسين جودة الصورة
                            scale: 2 // يزيد دقة الصورة النهائية
                        }).then(canvas => {
                            const imageData = canvas.toDataURL('image/png');
                            const link = document.createElement('a');
                            link.href = imageData;
                            link.download = 'pepasafe_profile.png';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }).catch(err => {
                            console.error('Failed to capture and download image:', err);
                        });
                    });
                }
                // ⭐⭐ نهاية الكود الجديد ⭐⭐
            });
        </script>

    </div>

    <p id="copy-success" class="text-green-600 text-sm mt-2 hidden">
        {{ __('Link copied to clipboard!') }}
    </p>

    <script>
        function copyProfileLink() {
            const link = document.getElementById('profile-link').href; // خد الـ href
            navigator.clipboard.writeText(link).then(function () {
                document.getElementById('copy-success').classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('copy-success').classList.add('hidden');
                }, 2000);
            });
        }


        function shareProfileLink() {
            const link = document.getElementById('profile-link').href;

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

