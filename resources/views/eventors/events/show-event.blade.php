@php use App\Models\Recipient;use App\Models\User; @endphp
    <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>عرض الاحداث</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body class="space-y-12 bg-white">

@include('partials.auth-navbar')

<section class="space max-w-xl mx-auto px-4 sm:px-0">
    <form action="{{ route('showEvent', $event->slug) }}" method="GET"
          class="relative flex flex-col sm:flex-row items-center">
        @if(
        !auth()->check() ||
        auth()->user()->hasAnyRole(['eventor', 'super admin']) ||
        auth()->user()->hasAnyPermission(['full access to events', 'search for a document', 'full access']) ||
        auth()->check()
        )
            <div class="relative w-full sm:w-[600px]">

                <input
                    name="query"
                    type="text"
                    placeholder="أدخل الأسم أو رقم الهاتف أو البريد الإلكتروني الخاص بالمشارك أو امسح رمز QR"
                    required
                    class="w-full sm:w-[600px] border border-gray-300 rounded-lg pl-12 pr-4 py-4 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 text-[9px] sm:text-base" {{-- تم تعديل هذا السطر --}}
                />

                @if(!auth()->check() || auth()->user()->hasRole('eventor') || auth()->user()->hasAnyPermission([
                'full access to events', 'search by qr code', 'full access'
                ]))
                    <i
                        id="start-qr-btn"
                        class="icon bi bi-qr-code absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600 cursor-pointer"
                    ></i>
                @endif
            </div>

        @endif

        <button
            type="submit"
            class="w-full sm:w-auto mt-3 sm:mt-0 bg-blue-600 text-white rounded-lg px-4 py-2 sm:mr-2 hover:bg-blue-700"
        >
            بحث
        </button>
    </form>


    <!-- Modal -->
    <div id="qr-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-4 max-w-md w-full relative">
            <button id="close-qr-modal" class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-xl">
                &times;
            </button>
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
                {facingMode: "environment"},
                {fps: 10, qrbox: 250},
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

<!-- بطاقة النتيجة صغيرة على اليمين -->
@if(request()->has('query') && $user)
    <!-- ======================= الإطار الجديد ======================= -->
    <!-- حاوية خارجية للإطار المتدرج الأنيق -->
    <div class="max-w-4xl mx-auto p-1 bg-blue-300 rounded-xl shadow-lg">

        <!-- الصندوق الأبيض الداخلي (الكود الأصلي الخاص بك) -->
        <div class="bg-white rounded-lg p-6">
            <!-- حاوية رئيسية باستخدام Flexbox -->
            <div class="flex justify-between items-start gap-4">

                <!-- قسم المعلومات -->
                <div id="personal-info" class="flex flex-col">
                    <h2 id="name" class="text-xl font-bold text-gray-800 mb-2">{{ $user->name }}</h2>
                    <div class="contact-item flex items-center gap-2 text-gray-600 mb-2">
                        <i class="fa-solid fa-phone-alt w-4 text-center"></i>
                        <span id="phone">{{ $user->phone }}</span>
                    </div>
                    <div class="contact-item flex items-center gap-2 text-gray-600 mb-2">
                        <i class="fa-solid fa-envelope w-4 text-center"></i>
                        <span id="email">{{ $user->email }}</span>
                    </div>
                    <div class="contact-item flex items-center gap-2 text-gray-600">
                        <i class="fa-solid fa-certificate w-4 text-center"></i>
                        <span id="certificate-label">شهادة الحضور</span>
                    </div>
                </div>

                <!-- قسم الأزرار مع السويتش الجديد -->
                <div class="flex flex-col items-end gap-4">
                    @if(optional($documents->first())->file_path != null)
                        <!-- الأزرار الأصلية -->
                        <div class="flex flex-wrap justify-end gap-3">
                            <button
                                id="print-btn"
                                class="flex items-center gap-2 bg-purple-100 text-purple-700 py-2 px-4 rounded-lg hover:bg-purple-200 transition-colors">
                                <i class="fa-solid fa-print"></i>
                                <span id="print-btn">طباعة</span>
                            </button>
                            <button
                                id="download-btn"
                                class="flex items-center gap-2 bg-green-100 text-green-700 py-2 px-4 rounded-lg hover:bg-green-200 transition-colors">
                                <i class="fa-solid fa-download"></i>
                                <span id="download-btn">تنزيل</span>
                            </button>
                            <button
                                onclick="previewFile('{{ asset('storage/' . optional($documents->first())->file_path) }}')"
                                class="flex items-center gap-2 bg-blue-100 text-blue-700 py-2 px-4 rounded-lg hover:bg-blue-200 transition-colors">
                                <i class="fa-solid fa-eye"></i>
                                <span>معاينة</span>
                            </button>
                        </div>
                    @else
                        <div class="flex items-center justify-end">
                            <div
                                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2"
                                role="alert">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span class="font-bold">لا يوجد وثيقة لهذا الشخص.</span>
                            </div>
                        </div>
                    @endif

                    @if($enable_attendance)
                        <!-- ======================= السويتش الجديد ======================= -->
                        <div class="flex items-center gap-3" data-user-id="{{ $user->id }}">
                            <span class="text-sm font-medium text-gray-700">تفعيل الحضور</span>
                            <label for="toggle-{{ $user->id }}"
                                   class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="toggle-{{ $user->id }}"
                                       class="sr-only peer attendance-toggle"
                                       @if($user->is_attendance) checked @endif>
                                <div
                                    class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-green-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>
                    @endif

                    <div id="toast"
                         class="fixed top-5 right-5 z-50 hidden
                            min-w-[200px] max-w-xs
                            bg-white border shadow-lg rounded-md p-4
                            flex items-start gap-3
                            transition transform duration-300 ease-out">

                        <i id="toast-icon" class="text-lg"></i>
                        <span id="toast-message" class="text-sm font-medium text-gray-800"></span>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endif

@if(request()->has('query') && !$user)
    <div class="max-w-md mx-auto bg-red-50 border-l-4 border-red-500 rounded-lg p-5 shadow-sm">
        <div class="flex items-center gap-4">
            <!-- الأيقونة -->
            <div>
                <i class="fa-solid fa-circle-exclamation text-3xl text-red-500"></i>
            </div>
            <!-- النصوص -->
            <div>
                <h3 class="font-bold text-red-800 text-lg">لم يتم العثور على المستخدم</h3>
                <p class="text-red-700 mt-1">لا يوجد شخص بهذا الاسم. يرجى المحاولة مرة أخرى.</p>
            </div>
        </div>
    </div>
@endif

<!-- كارد التحكم بالأحداث -->
<section
    class="w-full mx-auto bg-white rounded-lg p-4 sm:p-6 shadow-md mb-6 hover:shadow-lg transition-shadow duration-300">

    @if(session('success') || session('error'))
        <div id="flash-message-container" class="fixed top-5 right-5 z-50">
            <div id="flash-message-card"
                 class="min-w-[300px] max-w-sm rounded-lg shadow-lg p-4 transition-all duration-500 ease-in-out transform scale-0 origin-top-right"
                 role="alert">
                <div class="flex items-center gap-3">
                    <div id="flash-icon">
                        @if(session('success'))
                            <i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>
                        @endif
                        @if(session('error'))
                            <i class="fa-solid fa-circle-xmark text-red-500 text-2xl"></i>
                        @endif
                    </div>
                    <div id="flash-message" class="text-sm font-semibold">
                        @if(session('success'))
                            {{ session('success') }}
                        @endif
                        @if(session('error'))
                            {{ session('error') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const flashMessageCard = document.getElementById('flash-message-card');
                if (flashMessageCard) {
                    // إضافة كلاسات التنسيق بناءً على نوع الرسالة
                    const isSuccess = "{{ session('success') }}" !== "";
                    if (isSuccess) {
                        flashMessageCard.classList.add('bg-white', 'text-green-800', 'border', 'border-green-300');
                    } else {
                        flashMessageCard.classList.add('bg-white', 'text-red-800', 'border', 'border-red-300');
                    }

                    // إظهار الرسالة
                    setTimeout(() => {
                        flashMessageCard.classList.remove('scale-0');
                        flashMessageCard.classList.add('scale-100');
                    }, 100);

                    // إخفاء الرسالة بعد 5 ثواني
                    setTimeout(() => {
                        flashMessageCard.classList.remove('scale-100');
                        flashMessageCard.classList.add('scale-0');
                    }, 5000);
                }
            });
        </script>
    @endif

    <!-- رأس الكارد -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3">
        <h2 class="text-lg sm:text-xl font-semibold">اسم الحدث: {{ $event->title }}</h2>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto">

            @if(@auth()->check() && @auth()->user()->hasAnyPermission([
                'full access to events', 'full access', 'edit events'
            ]))
                <a href="{{ route('editEvent', $event->slug) }}">
                    <button
                        class="w-full sm:w-auto flex items-center justify-center gap-2 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-edit text-sm sm:text-base"></i>
                        <span class="text-sm sm:text-base">تعديل الحدث</span>
                    </button>
                </a>
            @endif

            @if(@auth()->check() && @auth()->user()->hasAnyPermission([
                'full access to events', 'full access', 'delete event'
            ]))
                <!-- زر حذف الحدث -->
                <button
                    onclick="openDeleteModal()"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 bg-red-600 text-white px-3 py-2 rounded-md hover:bg-red-700 transition">
                    <i class="fas fa-trash text-sm sm:text-base"></i>
                    <span class="text-sm sm:text-base">حذف الحدث</span>
                </button>
            @endif

        </div>
    </div>

    <!-- البطاقات الإحصائية -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <!-- بطاقة تاريخ الحدث -->
        <div class="bg-blue-100 p-4 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="font-semibold mb-1 text-sm sm:text-base">تاريخ الحدث</h3>
            {{--            <p class="text-gray-600 text-xs sm:text-sm mb-2">الوصف هنا</p>--}}
            <div class="flex items-center justify-between">
                <span class="text-base sm:text-lg font-bold">{{ $event->start_date }}</span>
                <i class="fas fa-calendar-alt text-2xl sm:text-3xl text-blue-600"></i>
            </div>
        </div>
        <!-- بطاقة عدد النماذج -->
        <div class="bg-green-100 p-4 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="font-semibold mb-1 text-sm sm:text-base">عدد النماذج</h3>
            {{--            <p class="text-gray-600 text-xs sm:text-sm mb-2">الوصف هنا</p>--}}
            <div class="flex items-center justify-between">
                <span class="text-2xl sm:text-3xl font-bold">{{ $templateCount }}</span>
                <i class="fas fa-file-alt text-2xl sm:text-3xl text-green-600"></i>
            </div>
        </div>
        <!-- بطاقة عدد الأعضاء -->
        <div class="bg-purple-100 p-4 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="font-semibold mb-1 text-sm sm:text-base">عدد المشاركين</h3>
            {{--            <p class="text-gray-600 text-xs sm:text-sm mb-2">الوصف هنا</p>--}}
            <div class="flex items-center justify-between">
                <span class="text-2xl sm:text-3xl font-bold">{{ $recipientCount }}</span>
                <i class="fas fa-users text-2xl sm:text-3xl text-purple-600"></i>
            </div>
        </div>
        <!-- بطاقة الحالة -->
        @php
            $isExpired = \Carbon\Carbon::parse($event->end_date)->isPast();
        @endphp

        <div class="bg-orange-100 p-4 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="font-semibold mb-1 text-sm sm:text-base">الحالة</h3>
            <div class="flex items-center justify-between">
                <span class="text-2xl sm:text-3xl font-bold">
                    {{ $isExpired ? 'غير نشط' : 'نشط' }}
                </span>
                <i class="{{ $isExpired ? 'fas fa-ban text-red-600' : 'fas fa-running text-green-600' }} text-2xl sm:text-3xl"></i>
            </div>
        </div>
    </div>
</section>

<!-- عنوان تحت الكارد الرئيسي -->
<div class="w-full mx-auto text-right mb-4 sm:mb-6 px-4 sm:px-6">
    <h2 class="text-lg sm:text-xl font-semibold">النماذج المتاحة لهذا الحدث: </h2>
</div>

@foreach($templates as $template)
    <!-- الكارد الموحد الجديد -->
    <div class="w-full mx-auto bg-white rounded-lg p-4 sm:p-6 shadow-md mb-6 hover:shadow-lg transition">
        <!-- صلاحية & تسجيل الحضور -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
            <h3 class="text-lg font-semibold">اسم النموذج: {{ $template->title }}</h3>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <div class="inline-flex items-center gap-1 border border-gray-300 rounded-md px-2 py-1">
                    <i class="fas fa-key text-base sm:text-lg text-gray-600"></i>
                    <span class="text-sm sm:text-base font-medium">{{ $template->validity }}</span>
                </div>
                @if($enable_attendance)
                    <div id="attendance-status"
                         class="inline-flex items-center gap-1 border border-gray-300 rounded-md px-2 py-1">
                        @if(User::where('is_attendance', 1)->exists())
                            <i class="fas fa-check-circle text-base sm:text-lg text-green-600"></i>
                            <span class="text-sm sm:text-base font-medium text-green-700">
            تسجيل الحضور: مفعل
        </span>
                        @else
                            <i class="fas fa-times-circle text-base sm:text-lg text-red-600"></i>
                            <span class="text-sm sm:text-base font-medium text-red-700">
            تسجيل الحضور: غير مفعل
        </span>
                        @endif
                    </div>

                @endif

            </div>
        </div>
        <!-- تفعيل الكل يدوياً -->
        @if($enable_attendance)
            <div class="bg-gray-200 rounded-md p-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-sm sm:text-base font-medium">تفعيل حضور الكل يدوياً</span>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox"
                               class="sr-only peer"
                               id="attendance-switch"
                            {{ User::where('is_attendance', 1)
                               ->whereIn('id', Recipient::where('event_id', $event->id)->pluck('user_id'))
                               ->exists() ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-green-600 transition"></div>
                        <div
                            class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full peer-checked:translate-x-5 transition"></div>
                    </label>
                </div>
            </div>

        @endif
        <!-- تفاصيل الاتصال -->
        <div class="flex flex-col lg:flex-row justify-between items-start gap-6 mb-6">
            <!-- جهة الاتصال 1 -->
            <div class="flex items-start gap-3">
                <div class="w-1 bg-blue-600 rounded h-20"></div>
                <div class="flex flex-col">
                    <h4 class="font-semibold mb-1 text-sm sm:text-base">المعلومات الخاصة بالوثيقة</h4>
                    <span
                        class="text-gray-500 text-xs sm:text-sm mb-2">توقيت إرسال الوثيقة: {{ $template->send_at }}</span>
                    <div class="flex items-center gap-4">
                        <div class="text-gray-500 text-xs sm:text-sm mb-2">طريقة إرسال الوثيقة:</div>
                        @php
                            $sendVia = $template->send_via; // هنفك JSON إلى array
                        @endphp


                        <div class="flex flex-wrap items-center gap-4">
                            {{--                            @php--}}
                            {{--                                $sendVia = json_decode($template->send_via, true) ?? [];--}}
                            {{--                            @endphp--}}

                            @if(in_array('whatsapp', $sendVia))
                                <div class="flex items-center gap-1">
                                    <i class="fab fa-whatsapp text-2xl text-green-500"></i>
                                    <span class="text-sm">واتساب</span>
                                </div>
                            @endif

                            {{--                            @php--}}
                            {{--                                $sendVia = json_decode($template->send_via, true) ?? [];--}}
                            {{--                            @endphp--}}

                            @if(in_array('email', $sendVia))
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-envelope text-2xl text-red-500"></i>
                                    <span class="text-sm">إيميل</span>
                                </div>
                            @endif

                            {{--                            @php--}}
                            {{--                                $sendVia = json_decode($template->send_via, true) ?? [];--}}
                            {{--                            @endphp--}}

                            @if(in_array('sms', $sendVia))
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-sms text-2xl text-blue-500"></i>
                                    <span class="text-sm">SMS</span>
                                </div>
                            @endif

                            @if(empty($sendVia))
                                <span class="text-sm text-gray-400">لا توجد وسيلة إرسال مفعّلة</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($attendances)
                <!-- جهة الاتصال 2 -->
                @foreach($attendances as $attendance)
                    @php
                        $sendViaAtt = $attendance->send_via;
                    @endphp
                    <div class="flex items-start gap-3">
                        <div class="w-1 bg-blue-600 rounded h-20"></div>
                        <div class="flex flex-col">
                            <h4 class="font-semibold mb-1 text-sm sm:text-base">المعلومات الخاصة ببادج الحضور</h4>
                            <span
                                class="text-gray-500 text-xs sm:text-sm mb-2">توقيت إرسال بادج الحضور: {{ $attendance->send_at }}</span>
                            <div class="flex items-center gap-4">
                                @if(in_array('whatsapp', $sendViaAtt))
                                    <i class="fab fa-whatsapp text-2xl text-green-500"></i><span
                                        class="text-sm">واتساب</span>
                                @endif
                                @if(in_array('email', $sendViaAtt))
                                    <i class="fas fa-envelope text-2xl text-red-500"></i><span
                                        class="text-sm">ايميل</span>
                                @endif
                                @if(in_array('sms', $sendViaAtt))
                                    <i class="fas fa-sms text-2xl text-blue-500"></i><span class="text-sm">SMS</span>
                                @endif
                                @if(empty($sendViaAtt))
                                    <span class="text-sm text-gray-400">لا توجد وسيلة إرسال مفعّلة</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <!-- أزرار أسفل الاتصال -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
            {{--            @if($templateDataFile)--}}
            {{--                <button--}}
            {{--                    onclick="downloadFile('{{ asset('storage/' . $templateDataFile->file_path) }}')"--}}
            {{--                    class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">--}}
            {{--                    <i class="fas fa-download text-base sm:text-lg text-gray-700"></i>--}}
            {{--                    <span class="text-xs sm:text-sm">ملف بيانات المشاركين الخاص بالوثيقة</span>--}}
            {{--                </button>--}}
            {{--            @endif--}}

            {{--            <script>--}}
            {{--                function downloadFile(url) {--}}
            {{--                    const link = document.createElement('a');--}}
            {{--                    link.href = url;--}}
            {{--                    link.setAttribute('download', 'participants.xlsx'); // تقدر تغير الاسم--}}
            {{--                    document.body.appendChild(link);--}}
            {{--                    link.click();--}}
            {{--                    document.body.removeChild(link);--}}
            {{--                }--}}
            {{--            </script>--}}


            <button
                class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                <i class="fas fa-download text-base sm:text-lg text-gray-700"></i>
                <span class="text-xs sm:text-sm">ملف بيانات التواصل</span>
            </button>
            <a href="{{ route('documents.download', ['template' => $template->id]) }}"
               class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                <i class="fas fa-file-alt text-base sm:text-lg text-gray-700"></i>
                <span class="text-xs sm:text-sm">جميع الوثائق</span>
            </a>
        </div>
        <!-- أزرار المعاينة -->
        <div class="flex flex-col sm:flex-row justify-start items-center gap-3 sm:gap-4 mt-4">
            <button
                onclick="previewFile('{{ asset('storage/' . optional($template->templateFiles->first())->file_path) }}')"
                class="w-full sm:w-auto flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                <i class="fas fa-eye text-base sm:text-lg"></i>
                <span class="text-sm sm:text-base">معاينة قالب الوثيقة</span>
            </button>

            <div id="image-popup" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
                <div class="relative max-w-3xl max-h-screen">
                    <img id="popup-image" src="" alt="Popup Image" class="max-w-full max-h-full rounded-md shadow-lg">
                    <button onclick="closePopup()"
                            class="absolute top-2 right-2 text-white text-3xl font-bold bg-gray-800 rounded-full w-8 h-8 flex items-center justify-center transition hover:bg-gray-700">
                        &times;
                    </button>
                </div>
            </div>

            @if($event->is_attendance_enabled)
                <button
                    class="w-full sm:w-auto flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    <i class="fas fa-eye text-base sm:text-lg"></i>
                    <span class="text-sm sm:text-base">معاينة قالب البادج</span>
                </button>
            @endif
        </div>
    </div>
@endforeach

@if($templates->isEmpty())
    <div class="text-center text-gray-500 text-sm py-12">
        <i class="fas fa-file-alt text-4xl text-blue-400 mb-4"></i>
        <h2 class="text-lg font-semibold mb-2">No documents available yet</h2>
        <p class="text-sm">Start creating your first document to see it here!</p>
    </div>
@endif

<div id="toast"
     class="fixed top-5 right-5 z-50 hidden min-w-[200px] max-w-xs bg-white border shadow-lg rounded-md p-4 flex items-start gap-3 transition transform duration-300 ease-out">
    <i id="toast-icon" class="text-lg"></i>
    <span id="toast-message" class="text-sm font-medium text-gray-800"></span>
</div>

<!-- Modal تأكيد الحذف -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-[90%] sm:w-[400px]">
        <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            <h2 class="text-lg font-semibold text-gray-800">هل أنت متأكد من حذف هذا الحدث؟</h2>
        </div>
        <p class="text-sm text-gray-600 mb-6">سيتم حذف الحدث وكل النماذج المرتبطة به بشكل دائم ولا يمكن استرجاعهم.</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md">إلغاء
            </button>
            <button type="button" onclick="confirmAndDelete()"
                    class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-md">تأكيد الحذف
            </button>
        </div>
    </div>
</div>

<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-[90%] sm:w-[400px]">
        <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-lock text-blue-600 text-2xl"></i>
            <h2 class="text-lg font-semibold text-gray-800">تأكيد كلمة المرور</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">
            للمتابعة، يرجى إدخال كلمة المرور الخاصة <span class="font-bold">بـمنشئ الحدث</span>.
        </p>
        <form id="passwordForm" method="POST" action="{{ route('events.destroy', $event->slug) }}">
            @csrf
            @method('DELETE')
            <div class="mb-4">
                <input
                    type="password"
                    id="passwordInput"
                    name="password"
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="أدخل كلمة المرور"
                    required
                />
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closePasswordModal()"
                        class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md">إلغاء
                </button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    تأكيد
                </button>
            </div>
        </form>
    </div>
</div>

@include('partials.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        const toastIcon = document.getElementById('toast-icon');

        function showToast(message, type = 'success') {
            toast.classList.remove('hidden', 'opacity-0', 'translate-x-5', 'border-green-200', 'border-red-200');
            toast.classList.add('opacity-100', 'translate-x-0');

            toastMessage.textContent = message;

            if (type === 'success') {
                toast.classList.add('border-green-200');
                toastIcon.className = 'fas fa-check-circle text-green-600';
            } else {
                toast.classList.add('border-red-200');
                toastIcon.className = 'fas fa-times-circle text-red-600';
            }

            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-x-0');
                toast.classList.add('opacity-0', 'translate-x-5');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }

        // السويتش
        const switchInput = document.querySelector('input[type="checkbox"][id="attendance-switch"]');

        if (switchInput) {
            switchInput.addEventListener('change', function () {
                const isChecked = this.checked ? 1 : 0;

                fetch("{{ route('toggleAttendance') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        status: isChecked,
                        event_id: {{ $event->id }} // أو أي ID للـ event اللي إنت فيه
                    })
                })

                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('تم التحديث بنجاح');
                        } else {
                            showToast('لم يتم التحديث: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        showToast('حصل خطأ في الاتصال بالسيرفر', 'error');
                        console.error(error);
                    });
            });
        }
    });

    function previewFile(filePath) {
        const popup = document.getElementById('image-popup');
        const popupImage = document.getElementById('popup-image');

        // Set the image source
        popupImage.src = filePath;

        // Show the popup
        popup.classList.remove('hidden');
        popup.classList.add('flex');
    }

    function closePopup() {
        const popup = document.getElementById('image-popup');

        // Hide the popup
        popup.classList.remove('flex');
        popup.classList.add('hidden');
    }
</script>


<script>
    var csrfToken = '{{ csrf_token() }}';
</script>


<script src="{{ asset('js/update-attendance.js') }}"></script>

<script>
    function downloadAllDocuments(documents) {
        if (!documents || documents.length === 0) {
            alert('لا توجد وثائق متاحة للتنزيل.');
            return;
        }

        documents.forEach(filePath => {
            // المسار الكامل للملف في مجلد 'public/storage'
            const fullPath = `/storage/${filePath}`;

            const link = document.createElement('a');
            link.href = fullPath;

            // استخراج اسم الملف من المسار لتسميته عند التنزيل
            const fileName = filePath.substring(filePath.lastIndexOf('/') + 1);
            link.setAttribute('download', fileName);

            link.style.display = 'none';
            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const printBtn = document.getElementById('print-btn');
        const imagePath = "{{ asset('storage/' .  optional($documents->first())->file_path) }}";
        const logoPath = "{{ asset('/assets/logo.jpg') }}";

        if (printBtn) {
            printBtn.addEventListener('click', () => {
                const tempImg = new Image();
                const tempLogo = new Image();
                tempImg.src = imagePath;
                tempLogo.src = logoPath;

                Promise.all([
                    new Promise((resolve) => tempImg.onload = resolve),
                    new Promise((resolve) => tempLogo.onload = resolve)
                ]).then(() => {
                    const imgCanvas = document.createElement('canvas');
                    const imgContext = imgCanvas.getContext('2d');
                    imgCanvas.width = tempImg.width;
                    imgCanvas.height = tempImg.height;
                    imgContext.drawImage(tempImg, 0, 0);
                    const base64Image = imgCanvas.toDataURL('image/jpeg');

                    const logoCanvas = document.createElement('canvas');
                    const logoContext = logoCanvas.getContext('2d');
                    logoCanvas.width = tempLogo.width;
                    logoCanvas.height = tempLogo.height;
                    logoContext.drawImage(tempLogo, 0, 0);
                    const base64Logo = logoCanvas.toDataURL('image/jpeg');

                    const footerHtml = `
                    <div style="
                        background-color: white;
                        border: 1px solid #ccc;
                        border-top: none;
                        border-radius: 0 0 4px 4px;
                        padding: 8px 16px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        width: 100%;
                        box-sizing: border-box;
                        font-family: Arial, sans-serif;
                        direction: ltr;  /* تم التغيير هنا */
                    ">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <img src="${base64Logo}" alt="Pepasafe Logo" style="height: 32px; width: auto;">
                            <span style="font-size: 14px; color: #6b7280; font-weight: 500; margin-left: 8px;">
                                Verified by Pepasafe
                            </span>
                        </div>
                        <span style="font-size: 12px; color: #9ca3af;">
                            <i class="fas fa-copyright" style="margin-right: 4px;"></i>
                        </span>
                    </div>
                `;

                    const printContent = `
                    <div style="display: flex; flex-direction: column; align-items: center; padding: 20px;">
                        <img src="${base64Image}" style="width: 100%; height: auto; max-width: 800px; border-radius: 8px 8px 0 0; border: 2px solid #e5e7eb; border-bottom: none;">
                        ${footerHtml}
                    </div>
                `;

                    const printWindow = window.open('', '_blank');
                    if (printWindow) {
                        printWindow.document.write('<html><head><title>طباعة الشهادة</title>');
                        printWindow.document.write('<style>');
                        printWindow.document.write('body { margin: 0; padding: 0; }');
                        printWindow.document.write('img { max-width: 100%; height: auto; }');
                        printWindow.document.write('</style>');
                        printWindow.document.write('</head><body>');
                        printWindow.document.write(printContent);
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();

                        setTimeout(() => {
                            printWindow.print();
                        }, 500);
                    }
                }).catch(error => {
                    console.error('فشل في تحميل الصور للطباعة.', error);
                    alert('عذراً، فشل تحميل الصورة للطباعة.');
                });
            });
        }

        const downloadBtn = document.getElementById('download-btn');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', () => {
                const tempImg = new Image();
                const tempLogo = new Image();

                tempImg.src = imagePath;
                tempLogo.src = logoPath;

                Promise.all([
                    new Promise((resolve) => tempImg.onload = resolve),
                    new Promise((resolve) => tempLogo.onload = resolve)
                ]).then(() => {
                    const finalWidth = tempImg.width;
                    const footerHeight = 50;
                    const finalHeight = tempImg.height + footerHeight;

                    const finalCanvas = document.createElement('canvas');
                    const finalContext = finalCanvas.getContext('2d');
                    finalCanvas.width = finalWidth;
                    finalCanvas.height = finalHeight;

                    finalContext.drawImage(tempImg, 0, 0, finalWidth, tempImg.height);
                    finalContext.fillStyle = '#fff';
                    finalContext.fillRect(0, tempImg.height, finalWidth, footerHeight);

                    // حساب أبعاد اللوجو
                    const logoHeight = 32;
                    const logoWidth = (tempLogo.width / tempLogo.height) * logoHeight;

                    // حساب أبعاد النص
                    const text = 'Verified by Pepasafe';
                    finalContext.font = `500 14px Arial, sans-serif`;

                    // تحديد المسافات من اليسار
                    const padding = 16;
                    const spaceBetween = 8;

                    // رسم اللوجو
                    const logoX = padding;
                    const logoY = tempImg.height + (footerHeight - logoHeight) / 2;
                    finalContext.drawImage(tempLogo, logoX, logoY, logoWidth, logoHeight);

                    // رسم النص
                    finalContext.fillStyle = '#6b7280';
                    finalContext.textAlign = 'left';
                    const textX = logoX + logoWidth + spaceBetween;
                    const textY = tempImg.height + (footerHeight / 2) + 5;
                    finalContext.fillText(text, textX, textY);

                    // تحويل الـ canvas إلى رابط تحميل
                    const downloadLink = document.createElement('a');
                    downloadLink.href = finalCanvas.toDataURL('image/jpeg', 1.0);
                    downloadLink.download = 'document-with-footer.jpg';
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }).catch(error => {
                    console.error('فشل في تحميل الصور للتحميل.', error);
                    alert('عذراً، فشل تحميل المستند للتحميل.');
                });
            });
        }
    });
</script>


</body>
</html>
