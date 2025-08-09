<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>صفحة الكروت الجديدة</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body class="space-y-12 bg-white">

@include('partials.auth-navbar')

<!-- كارد التحكم بالأحداث -->
<section class="w-full mx-auto bg-white rounded-lg p-4 sm:p-6 shadow-md mb-6 hover:shadow-lg transition-shadow duration-300">

    @if(session('success') || session('error'))
        <div id="flash-message-container" class="fixed top-5 right-5 z-50">
            <div id="flash-message-card" class="min-w-[300px] max-w-sm rounded-lg shadow-lg p-4 transition-all duration-500 ease-in-out transform scale-0 origin-top-right"
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
            document.addEventListener('DOMContentLoaded', function() {
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
                <button class="w-full sm:w-auto flex items-center justify-center gap-2 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 transition">
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
            <div id="attendance-status-{{ $template->id }}" class="inline-flex items-center gap-1 border border-gray-300 rounded-md px-2 py-1">
                @if($template->is_attendance_enabled)
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


        </div>
    </div>
    <!-- تفعيل الكل يدوياً -->
    <div class="bg-gray-200 rounded-md p-4 mb-6">
        <div class="flex justify-between items-center">
            <span class="text-sm sm:text-base font-medium">تفعيل حضور الكل يدوياً</span>

            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox"
                       class="sr-only peer"
                       id="attendance-switch"
                       data-template-id="{{ $template->id }}"
                    {{ $template->is_attendance_enabled ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-blue-600 transition"></div>
                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full peer-checked:translate-x-5 transition"></div>
            </label>
        </div>
    </div>

    <!-- تفاصيل الاتصال -->
    <div class="flex flex-col lg:flex-row justify-between items-start gap-6 mb-6">
        <!-- جهة الاتصال 1 -->
        <div class="flex items-start gap-3">
            <div class="w-1 bg-blue-600 rounded h-20"></div>
            <div class="flex flex-col">
                <h4 class="font-semibold mb-1 text-sm sm:text-base">المعلومات الخاصة بالوثيقة</h4>
                <span class="text-gray-500 text-xs sm:text-sm mb-2">توقيت إرسال الوثيقة: {{ $template->send_at }}</span>
                <div class="flex items-center gap-4">
                    <div class="text-gray-500 text-xs sm:text-sm mb-2">طريقة إرسال الوثيقة:</div>
                    @php
                        $sendVia = $template->send_via; // هنفك JSON إلى array
                    @endphp


                    <div class="flex flex-wrap items-center gap-4">
                        @if(in_array('whatsapp', $sendVia))
                            <div class="flex items-center gap-1">
                                <i class="fab fa-whatsapp text-2xl text-green-500"></i>
                                <span class="text-sm">واتساب</span>
                            </div>
                        @endif

                        @if(in_array('email', $sendVia))
                            <div class="flex items-center gap-1">
                                <i class="fas fa-envelope text-2xl text-red-500"></i>
                                <span class="text-sm">إيميل</span>
                            </div>
                        @endif

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
                <span class="text-gray-500 text-xs sm:text-sm mb-2">توقيت إرسال بادج الحضور: {{ $attendance->send_at }}</span>
                <div class="flex items-center gap-4">
                    @if(in_array('whatsapp', $sendViaAtt))
                    <i class="fab fa-whatsapp text-2xl text-green-500"></i><span class="text-sm">واتساب</span>
                    @endif
                    @if(in_array('email', $sendViaAtt))
                    <i class="fas fa-envelope text-2xl text-red-500"></i><span class="text-sm">ايميل</span>
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
        <button class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
            <i class="fas fa-download text-base sm:text-lg text-gray-700"></i>
            <span class="text-xs sm:text-sm">ملف بيانات المشاركين الخاص بالوثيقة</span>
        </button>
        <button class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
            <i class="fas fa-download text-base sm:text-lg text-gray-700"></i>
            <span class="text-xs sm:text-sm">ملف بيانات التواصل</span>
        </button>
        <button class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
            <i class="fas fa-file-alt text-base sm:text-lg text-gray-700"></i>
            <span class="text-xs sm:text-sm">ملف بيانات المشاركين الخاص بالبادج</span>
        </button>
    </div>
    <!-- أزرار المعاينة -->
    <div class="flex flex-col sm:flex-row justify-start items-center gap-3 sm:gap-4 mt-4">
        <button class="w-full sm:w-auto flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
            <i class="fas fa-eye text-base sm:text-lg"></i>
            <span class="text-sm sm:text-base">معاينة قالب الشهادة</span>
        </button>
        <button class="w-full sm:w-auto flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
            <i class="fas fa-eye text-base sm:text-lg"></i>
            <span class="text-sm sm:text-base">معاينة قالب البادج</span>
        </button>
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

<div id="toast" class="fixed top-5 right-5 z-50 hidden min-w-[200px] max-w-xs bg-white border shadow-lg rounded-md p-4 flex items-start gap-3 transition transform duration-300 ease-out">
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
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md">إلغاء</button>
            <button type="button" onclick="confirmAndDelete()" class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-md">تأكيد الحذف</button>
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
                <button type="button" onclick="closePasswordModal()" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md">إلغاء</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md">تأكيد</button>
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

            // إخفاء التوست بعد 3 ثواني
            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-x-0');
                toast.classList.add('opacity-0', 'translate-x-5');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }

        // لكل سويتش في الصفحة
        const switches = document.querySelectorAll('input[type="checkbox"][data-template-id]');

        switches.forEach(switchInput => {
            switchInput.addEventListener('change', function () {
                const templateId = this.dataset.templateId;
                const isChecked = this.checked ? 1 : 0;

                fetch("{{ route('toggleAttendance') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        template_id: templateId,
                        status: isChecked
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('تم التحديث بنجاح');

                            // ✅ تحديث العنصر الخاص بالحالة في نفس الصفحة
                            const statusDiv = document.getElementById(`attendance-status-${templateId}`);
                            if (statusDiv) {
                                statusDiv.innerHTML = isChecked
                                    ? `<i class="fas fa-check-circle text-base sm:text-lg text-green-600"></i>
                                   <span class="text-sm sm:text-base font-medium text-green-700">تسجيل الحضور: مفعل</span>`
                                    : `<i class="fas fa-times-circle text-base sm:text-lg text-red-600"></i>
                                   <span class="text-sm sm:text-base font-medium text-red-700">تسجيل الحضور: غير مفعل</span>`;
                            }
                        } else {
                            showToast('لم يتم التحديث: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        showToast('حصل خطأ في الاتصال بالسيرفر', 'error');
                        console.error(error);
                    });
            });
        });
    });


    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function confirmAndDelete() {
        closeDeleteModal();
        openPasswordModal();
    }

    function openPasswordModal() {
        document.getElementById('passwordModal').classList.remove('hidden');
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').classList.add('hidden');
    }

</script>





</body>
</html>
