<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>محفظتي</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        /* حدود افتراضية رصاصي، وعند الاختيار أزرق فاتح */
        .card-label {
            border: 1px solid #ccc;
            background-color: #fff; /* لون البوكس أبيض افتراضيًا */
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        input[type="radio"]:checked + .card-label {
            border-color: #60a5fa;
            background-color: #ebf8ff; /* لون الخلفية أزرق فاتح عند الاختيار */
        }
        .card-label:hover {
            background-color: #e0f2fe; /* لون الخلفية أزرق أفتح عند الوقوف عليه */
            border-color: #90cdf4;
        }


        .custom-plan-style *,
        .custom-plan-style *::before,
        .custom-plan-style *::after {
            box-sizing: border-box;
        }

        .custom-plan-style body {
            all: unset; /* لو محتاج تمسح تأثير body الأساسي */
        }

        .custom-plan-style {
            background-color: #f3f9f9;
            font-family: "Segoe UI", Tahoma, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;

        }

    </style>

</head>
<body class="min-h-screen bg-[#F9FAFB]">

<!-- Navbar -->
@include('partials.auth-navbar')

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">خطأ!</strong>
        <ul class="mt-3 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<!-- المحتوى الرئيسي -->
<main class="p-6 space-y-6">

    <!-- عنوان "محفظتي" على اليمين -->
    <div class="text-right">
        <h2 class="text-2xl font-bold">محفظتي</h2>
    </div>

    <!-- صف البطاقات -->
    <div class="flex gap-6 flex-wrap">

        <!-- بطاقة ملخص الباقة -->
        <div class="flex-1 bg-white shadow rounded-lg p-6 hover:shadow-lg hover:scale-105 transition-transform transition-shadow duration-200">
            <div class="flex justify-end">
                <h3 class="text-xl font-semibold">ملخص الباقة</h3>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">اسم الباقة:</span>
                    <span>{{ ($user->subscription?->plan?->name) }}</span>
                </div>
                @if ($user->subscription?->plan?->carry_over_credit)
                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">المتبقي من الوثائق:</span>
                    <span>{{ intval(($user->subscription?->remaining)) }}</span>
                </div>
                @else
                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">الحد الأقصى للوثائق:</span>
                    <span>{{ intval(($user->subscription?->plan?->credit_amount)) }}</span>
                </div>
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-500">المتبقي من الوثائق:</span>
                        <span>{{ intval(($user->subscription?->remaining)) }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">المستخدم:</span>
                    <span> {{ ($user->name) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">تاريخ التجديد:</span>
                    <span>{{ ($user->subscription?->end_date) }}</span>
                </div>
            </div>
            <div class="mt-4 flex justify-between items-center">
                <span class="font-bold text-gray-500">التجديد التلقائي</span>


                <form method="POST" action="{{ route('subscription.autoRenew') }}" id="auto-renew-form">
                    @csrf
                    <input type="hidden" name="auto_renew" value="0">
                    <label for="auto-renew" class="inline-flex items-center cursor-pointer relative">
                        <input type="checkbox" id="auto-renew" class="sr-only peer" name="auto_renew"
                        {{ old('auto_renew', $user->subscription?->auto_renew) ? 'checked' : '' }} onchange="document.getElementById('auto-renew-form').submit();" />
                 <div class="w-12 h-6 bg-gray-300 rounded-full peer-focus:ring-2 peer-focus:ring-blue-300 peer-checked:bg-blue-600 transition-colors"></div>
                 <div class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full shadow transform peer-checked:translate-x-full transition-transform"></div>
             </label>
         </form>


     </div>
            @if($user->subscription?->auto_renew)
                 <div class="mt-4 border border-green-600 rounded-lg p-4 bg-green-50 text-right">
                     <p class="text-green-600">سيتم التجديد التلقائي بتاريخ <strong>{{ ($user->subscription?->end_date) }}</strong></p>
                        </div>
            @endif
            <form action="{{ route('subscription.renewNow') }}" method="POST">
                @csrf
                <button type="submit" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 transition-colors transition-transform hover:scale-105 text-white font-bold py-2 px-4 rounded duration-200">تجديد الباقة الآن</button>
            </form>
        </div>

        <!-- بطاقة الرصيد المتاح -->
        <div id="recharge" class="flex-1 bg-white shadow rounded-lg p-6 hover:shadow-lg hover:scale-105 transition-transform transition-shadow duration-200">
            <div class="flex justify-end">
                <h3 class="text-xl font-semibold">الرصيد المتاح</h3>
            </div>
            <!-- السعر على اليمين -->
            <div class="mt-4 text-right">
                <span id="balance-amount" class="text-4xl font-bold text-green-600">{{ intval(($user->subscription?->balance)) }}</span>
                <span class="text-xl font-semibold text-green-600 mr-1">نقطة</span>
                <input type="hidden" id="balance-data" value="1500" data-unit-price="" />
            </div>
            <p class="text-gray-500 mt-1 text-right">يمكن إصدار {{ intval(($user->subscription?->balance)) }} وثيقة أخرى</p>


            <div class="text-right">
                <h4 class="text-lg font-semibold">شحن المحفظة</h4>
            </div>
            @foreach($paymentMethods as $paymentMethod)
                <div class="bg-white border shadow rounded-lg p-4 flex justify-between items-center mt-2">
                    <span id="account-number" class="text-gray-700">{{ $paymentMethod->value }}</span>
                    <button id="copy-account" title="انسخ رقم الحساب"><i class="bi bi-clipboard text-lg text-gray-600"></i></button>
                </div>
            @endforeach

                <input type="file" id="file-attachment" class="hidden" />
                <button type="button" id="charge-wallet-btn" class="mt-4 w-full bg-green-600 hover:bg-green-700 transition-colors transition-transform hover:scale-105 text-white font-bold py-2 px-4 rounded duration-200">شحن المحفظة</button>
        </div>

        <div id="wallet-charge-popup" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-auto relative">
                <button id="close-popup-btn" class="absolute top-2 left-2 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none" title="إغلاق">
                    &times;
                </button>

                <h3 class="text-xl font-semibold mb-4 text-gray-800">تأكيد شحن المحفظة</h3>

                <form id="charge-wallet-form" action="{{ route('wallet-recharge-request') }}" method="POST" enctype="multipart/form-data">
                    @csrf <div class="mb-4">
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="plan_id" value="{{ $user->subscription?->plan?->id }}">
                        <input type="hidden" name="subscription_id" value="{{ $user->subscription?->id }}">
                        <label for="charge-amount" class="block text-gray-700 text-sm font-bold mb-2">قيمة الشحن:</label>
                        <input type="number" id="charge-amount" name="amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="أدخل قيمة الشحن" required>
                    </div>
                    <div class="mb-4">
                        <label for="receipt-upload" class="block text-gray-700 text-sm font-bold mb-2">وصل الدفع:</label>
                        <input type="file" id="receipt-upload" name="receipt_path" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer" required>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" id="confirm-charge-popup" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">تأكيد الشحن</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <hr class="border-gray-300 my-4 fade-in" />
    <!-- Section: ترقية الباقة -->
    <div class="text-right fade-in mb-2">
        <h2 class="text-2xl font-bold">ترقية الباقة</h2>
    </div>

    <div class="text-lg font-semibold text-right mb-4">أختيار الباقة </div>

    <div id="upgrade" class="custom-plan-style">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($plans as $plan)
            <input type="radio" name="plan" id="plan-{{ $plan->id }}" value="{{ $plan->id }}" class="hidden" >
            <label for="plan-{{ $plan->id }}" class="card-label bg-white rounded-lg p-4 hover:shadow-lg transition cursor-pointer">
                <div class="text-lg font-semibold text-right mb-3">{{ $plan->name }}</div>
                <div class="text-right text-xl font-bold mb-2">
                    @if ($plan->compare_price)
                        <span class="text-gray-500 line-through text-base ml-2">{{ $plan->compare_price }} ج.م</span>
                    @endif
                    <span>{{ $plan->price }} ج.م</span>
                </div>
                <ul class="space-y-2 text-right mb-4">
                    @foreach ( $plan->features_list as $feature)
                        <li><i class="fas fa-check-circle text-green-500 ml-2"></i>{{ $feature }}</li>
                    @endforeach
                </ul>

                @if ($plan->price > 0)
                    <div class="bg-gray-100 rounded p-3 mb-4 text-right">
                        @foreach($paymentMethods as $payment)
                            <div class="mb-2">
                                <div class="mb-1 font-semibold">{{ $payment->key }}:</div>
                                <div class="flex items-center justify-between bg-white p-2 rounded border font-mono text-sm">
                                    <span id="value-{{ $loop->index }}">{{ $payment->value }}</span>
                                    <button type="button" onclick="copyToClipboard('value-{{ $loop->index }}')" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <label for="payment_receipt_{{ $plan->id }}" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition cursor-pointer flex items-center justify-center">
                        <i class="fas fa-cloud-upload-alt ml-2"></i>إرفاق وصل الدفع
                        <input
                            type="file"
                            id="payment_receipt_{{ $plan->id }}"
                            name="payment_receipt[{{ $plan->id }}]"
                            class="hidden payment-upload"
                            data-plan="{{ $plan->id }}"
                        >
                    </label>
                @endif
            </label>
        @endforeach
    </div>
    </div>

    <hr class="border-gray-300 my-4 fade-in" />
    <section class="text-right fade-in mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">سجل المعاملات</h2>

            <button class="flex items-center bg-gray-100 hover:bg-gray-200 hover:shadow-md transition-all rounded-lg px-4 py-2 font-semibold">
                <span>جميع العمليات</span>
                <i class="bi bi-arrow-up ms-2"></i>
                <i class="bi bi-arrow-down ms-1"></i>
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="overflow-hidden rounded-lg shadow">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-gray-700">نوع العملية</th>
                        <th class="px-4 py-2 text-gray-700">نوع الباقة</th>
                        <th class="px-4 py-2 text-gray-700">السعر</th>
                        <th class="px-4 py-2 text-gray-700">التاريخ</th>
                        <th class="px-4 py-2 text-gray-700">الحالة</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{-- التحقق إذا كان هناك اشتراك وسجل تاريخي --}}
{{--                    @dd($user->subscription->histories)--}}
                    @if($user->subscription && $user->subscription->histories->count() > 0)
                        @foreach($user->subscription->histories as $history)
                            <tr class="border-t hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-4 py-3">{{ $history->type }}</td> {{-- نوع العملية: initial, upgrade, renewal --}}
                                <td class="px-4 py-3">
                                     {{ $history->subscription->plan->name ?? 'غير معروف' }}
                                </td>
                                <td class="px-4 py-3">
                                        {{ intval ($history->subscription->plan->price ?? 'غير معروف')  }}
                                </td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($history->start_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch($history->status) {
                                            case 'active':
                                                $statusClass = 'text-green-700 bg-green-50 border border-green-600';
                                                $statusText = 'مكتملة';
                                                break;
                                            case 'pending': // ممكن تستخدمها لـ 'قيد المراجعة' لو عندك حالة كده
                                                $statusClass = 'text-yellow-700 bg-yellow-50 border border-yellow-600';
                                                $statusText = 'قيد المراجعة';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'text-red-700 bg-red-50 border border-red-600';
                                                $statusText = 'مرفوضة'; // أو 'ملغية' أو 'منتهية' حسب المعنى
                                                break;
                                                case 'expired':
                                                $statusClass = 'text-red-700 bg-red-50 border border-red-600';
                                                $statusText = 'منتهية ';
                                                break;
                                            case 'paused':
                                                $statusClass = 'text-blue-700 bg-blue-50 border border-blue-600';
                                                $statusText = 'متوقفة مؤقتاً';
                                                break;
                                            default:
                                                $statusClass = 'text-gray-700 bg-gray-50 border border-gray-600';
                                                $statusText = $history->status; // يعرض القيمة الأصلية لو مفيش مطابقة
                                        }
                                    @endphp
                                    <span class="inline-block px-2 py-1 text-sm font-semibold {{ $statusClass }} rounded">
                                    {{ $statusText }}
                                </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- لو مفيش سجلات تاريخية --}}
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                لا يوجد سجل معاملات متاحة حتى الآن.
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>



        <hr class="border-gray-300 my-4 fade-in" />
        <section class="text-right fade-in mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">سجل عمليات الشحن  </h2>

                <button class="flex items-center bg-gray-100 hover:bg-gray-200 hover:shadow-md transition-all rounded-lg px-4 py-2 font-semibold">
                    <span>جميع العمليات</span>
                    <i class="bi bi-arrow-up ms-2"></i>
                    <i class="bi bi-arrow-down ms-1"></i>
                </button>
            </div>


        <div class="overflow-x-auto">
            <div class="overflow-hidden rounded-lg shadow">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-gray-700">نوع العملية</th>
                        <th class="px-4 py-2 text-gray-700">نوع الباقة</th>
                        <th class="px-4 py-2 text-gray-700">قيمة الشحن</th>
                        <th class="px-4 py-2 text-gray-700">التاريخ</th>
                        <th class="px-4 py-2 text-gray-700">الحالة</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{-- التحقق إذا كان هناك اشتراك وسجل تاريخي --}}
                    {{--                    @dd($user->subscription->histories)--}}
                    @if($user->subscription && $user->subscription->histories->count() > 0)
                        @foreach($user->subscription->histories as $history)
                            <tr class="border-t hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-4 py-3">{{ $history->type }}</td> {{-- نوع العملية: initial, upgrade, renewal --}}
                                <td class="px-4 py-3">
                                    {{ $history->subscription->plan->name ?? 'غير معروف' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ intval ($history->subscription->plan->price ?? 'غير معروف')  }}
                                </td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($history->start_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch($history->status) {
                                            case 'active':
                                                $statusClass = 'text-green-700 bg-green-50 border border-green-600';
                                                $statusText = 'مكتملة';
                                                break;
                                            case 'pending': // ممكن تستخدمها لـ 'قيد المراجعة' لو عندك حالة كده
                                                $statusClass = 'text-yellow-700 bg-yellow-50 border border-yellow-600';
                                                $statusText = 'قيد المراجعة';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'text-red-700 bg-red-50 border border-red-600';
                                                $statusText = 'مرفوضة'; // أو 'ملغية' أو 'منتهية' حسب المعنى
                                                break;
                                                case 'expired':
                                                $statusClass = 'text-red-700 bg-red-50 border border-red-600';
                                                $statusText = 'منتهية ';
                                                break;
                                            case 'paused':
                                                $statusClass = 'text-blue-700 bg-blue-50 border border-blue-600';
                                                $statusText = 'متوقفة مؤقتاً';
                                                break;
                                            default:
                                                $statusClass = 'text-gray-700 bg-gray-50 border border-gray-600';
                                                $statusText = $history->status; // يعرض القيمة الأصلية لو مفيش مطابقة
                                        }
                                    @endphp
                                    <span class="inline-block px-2 py-1 text-sm font-semibold {{ $statusClass }} rounded">
                                    {{ $statusText }}
                                </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- لو مفيش سجلات تاريخية --}}
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                لا يوجد سجل معاملات متاحة حتى الآن.
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

@include('partials.footer')

</main>

<script>
    window.copyToClipboard = function(elementId) {
        const el = document.getElementById(elementId);
        if (!el) return;

        const text = el.innerText || el.textContent;

        navigator.clipboard.writeText(text).then(() => {
            Toastify({
                text: "تم نسخ القيمة!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10B981",
                stopOnFocus: true
            }).showToast();
        }).catch(() => {
            Toastify({
                text: "فشل النسخ!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#EF4444",
                stopOnFocus: true
            }).showToast();
        });
    }


    // جزء شحن المحفظة
    document.addEventListener('DOMContentLoaded', function() {
        const chargeWalletBtn = document.getElementById('charge-wallet-btn');
        const walletChargePopup = document.getElementById('wallet-charge-popup');
        const closePopupBtn = document.getElementById('close-popup-btn');
        const confirmChargePopupBtn = document.getElementById('confirm-charge-popup');

        // **تمت الإضافة: عنصر الرسالة (notification)**
        const notificationContainer = document.createElement('div');
        notificationContainer.id = 'custom-notification';
        notificationContainer.className = 'fixed top-4 left-1/2 -translate-x-1/2 bg-gray-800 text-white px-6 py-3 rounded-md shadow-lg opacity-0 transition-opacity duration-300 z-[9999]';
        document.body.appendChild(notificationContainer);

        // دالة مساعدة لإخفاء البوب أب ومسح البيانات
        function hidePopup() {
            walletChargePopup.classList.add('hidden');
            document.getElementById('charge-amount').value = '';
            document.getElementById('receipt-upload').value = '';
        }

        // **تمت الإضافة: دالة لعرض الرسالة المخصصة**
        function showNotification(message, type = 'success') {
            notificationContainer.textContent = message;
            if (type === 'success') {
                notificationContainer.style.backgroundColor = '#4CAF50'; // لون أخضر للنجاح
            } else if (type === 'error') {
                notificationContainer.style.backgroundColor = '#F44336'; // لون أحمر للخطأ
            } else {
                notificationContainer.style.backgroundColor = '#333'; // لون افتراضي
            }

            notificationContainer.classList.remove('opacity-0');
            notificationContainer.classList.add('opacity-100');

            setTimeout(() => {
                notificationContainer.classList.remove('opacity-100');
                notificationContainer.classList.add('opacity-0');
            }, 3000); // إخفاء الرسالة بعد 3 ثواني
        }


        // لما المستخدم يضغط على زر "شحن المحفظة"
        chargeWalletBtn.addEventListener('click', function(event) {
            event.preventDefault();
            walletChargePopup.classList.remove('hidden');
        });

        // لما المستخدم يضغط على زر الـ X
        closePopupBtn.addEventListener('click', hidePopup);

        // لما المستخدم يضغط في أي مكان خارج البوب أب (الخلفية الشفافة)
        walletChargePopup.addEventListener('click', function(event) {
            if (event.target === walletChargePopup) {
                hidePopup();
            }
        });

        // لما المستخدم يضغط على زر "تأكيد الشحن" داخل البوب أب
        confirmChargePopupBtn.addEventListener('click', function() {
            const chargeAmount = document.getElementById('charge-amount').value;
            const receiptFile = document.getElementById('receipt-upload').files[0];

            if (chargeAmount && receiptFile) {
                // هنا هتحط الكود الخاص بإرسال البيانات (قيمة الشحن والوصل) للسيرفر
                console.log('قيمة الشحن:', chargeAmount);
                console.log('وصل الدفع:', receiptFile.name);

                // الحصول على الفورم من زر التأكيد والقيام بالإرسال
                const form = confirmChargePopupBtn.closest('form'); // ده بيجيب أقرب فورم للزرار
                if (form) {
                    form.submit(); // بيتم إرسال الفورم
                    // بعد ما تعمل submit، الصفحة هتعمل reload، فمش هتظهر الـ notification
                    // أو الـ hidePopup() إلا لو الـ server side رجع نفس الصفحة بـ flash message
                }

                // showNotification('تم إرسال طلب شحن المحفظة بنجاح!', 'success'); // هذا السطر لن يظهر إلا لو لم يحدث reload
                // hidePopup(); // هذا السطر لن يتم تنفيذه قبل الـ reload

            } else {
                showNotification('الرجاء إدخال قيمة الشحن ورفع الوصل.', 'error');
            }
        });
    });


</script>

<script src=" {{ asset('js/cash.js') }}"></script>
</body>
</html>
