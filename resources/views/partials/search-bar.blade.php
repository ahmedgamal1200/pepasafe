<section class="space max-w-xl mx-auto px-4 sm:px-0">
    <form action="{{ auth()->check() ? route('home.users') : route('homeForGuests') }}" method="GET" class="relative flex flex-col sm:flex-row items-stretch sm:items-center w-full">
        {{-- جزء حقل الإدخال (Input) --}}
        @if(
            !auth()->check() ||
            auth()->user()->hasAnyRole(['eventor', 'super admin', 'user', 'employee']) ||
            auth()->user()->hasAnyPermission(['full access to events', 'search for a document', 'full access'])
        )
            @php
                // هذا الجزء يجب أن يكون معرّفًا مرة واحدة في بداية ملف الـ Blade
                $isRTL = app()->getLocale() === 'ar';
                $direction = $isRTL ? 'rtl' : 'ltr';
                $paddingStart = $isRTL ? 'pr-4' : 'pl-4'; // المسافة في بداية النص
                $paddingEnd = $isRTL ? 'pl-3' : 'pr-3';   // المسافة في نهاية النص
                $textAlignment = $isRTL ? 'text-right' : 'text-left'; // محاذاة النص داخل الحقل
            @endphp

            <div class="relative flex-grow">
                <input
                    name="query"
                    type="text"
                    placeholder="{{ trans_db('placeholder.search') }}"
                    required
                    class="w-full border border-gray-300 rounded-lg
               {{ $paddingStart }} {{ $paddingEnd }} py-2
               focus:outline-none focus:ring-2 focus:ring-blue-400
               text-xs sm:text-base {{ $textAlignment }}"
                    dir="{{ $direction }}"
                />
            </div>
        @endif

        @if(!auth()->check() ||  auth()->user()->hasRole(['user', 'eventor']) || auth()->user()->hasAnyPermission([
            'full access to events', 'search by qr code', 'full access'
        ]))
            <i
                id="start-qr-btn"
                class="icon bi bi-qr-code text-3xl sm:text-2xl text-gray-500 hover:text-blue-600 cursor-pointer p-2 sm:ml-2 mt-3 sm:mt-0 order-first sm:order-none"
            ></i>
        @endif

        <button
            type="submit"
            class="w-full sm:w-auto mt-3 sm:mt-0 bg-blue-600 text-white rounded-lg px-4 py-2 sm:mr-2 hover:bg-blue-700"
        >
            {{ trans_db('buttons.search') }}
        </button>
    </form>
    <p class="mt-3 text-gray-700 text-center text-sm sm:text-base">
       {{ trans_db('search.title') }}
    </p>


    <!-- Modal -->
    <div id="qr-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-4 max-w-md w-full relative">
            <button id="close-qr-modal" class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-xl">&times;</button>
            <h2 class="text-lg font-semibold mb-4">امسح رمز QR</h2>
            <div id="qr-reader" style="width: 100%"></div>
        </div>
    </div>

</section>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const qrBtn = document.getElementById("start-qr-btn");
        const modal = document.getElementById("qr-modal");
        const closeBtn = document.getElementById("close-qr-modal");
        const input = document.querySelector("input[name='query']");
        const form = input.closest("form");
        let html5QrCode = null;
        let isScanning = false;

        async function stopScanning() {
            if (html5QrCode && isScanning) {
                try {
                    await html5QrCode.stop();
                    await html5QrCode.clear();
                } catch (err) {
                    console.error("❌ Error stopping scanner:", err);
                }
                isScanning = false;
            }
            modal.classList.add("hidden");
        }

        qrBtn?.addEventListener("click", async () => {
            if (typeof Html5Qrcode === "undefined") {
                console.error("❌ مكتبة Html5Qrcode مش موجودة");
                return;
            }

            modal.classList.remove("hidden");

            await new Promise(resolve => setTimeout(resolve, 500));

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }

            if (isScanning) return;
            isScanning = true;

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                async (decodedText) => {
                    if (!isScanning) return;

                    let uuid = decodedText.trim();

                    // حاول تستخرج uuid من لينك لو موجود
                    try {
                        const url = new URL(uuid);
                        uuid = url.pathname.split("/").pop();
                    } catch (e) {
                        // مش لينك، تمام
                    }

                    // تأكد إنه شكل UUID صحيح
                    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
                    if (!uuidRegex.test(uuid)) {
                        console.warn("⚠️ الكود المقروء مش UUID:", decodedText);
                        return;
                    }

                    input.value = uuid;
                    console.log("✅ UUID جاهز للإرسال:", uuid);

                    await stopScanning();
                    form.submit(); // ⬅️ يبعته على route('home.users')
                },
                (error) => {
                    // مش لازم أي حاجة هنا
                }
            );
        });

        closeBtn?.addEventListener("click", async () => {
            await stopScanning();
        });
    });
</script>







