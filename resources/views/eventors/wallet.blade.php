<!DOCTYPE html>
{{--<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">--}}
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.jpg') }}">
    <title>The Wallet</title>
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
<body>

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
    @php
        $isRtl = (app()->getLocale() === 'ar');
        $textAlignClass = $isRtl ? 'text-right' : 'text-left';
    @endphp

    <div class="{{ $textAlignClass }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <h2 class="text-2xl font-bold">{{ trans_db('the.wallet') }}</h2>
    </div>

    <!-- صف البطاقات -->
    <div class="flex gap-6 flex-wrap">

        <!-- بطاقة ملخص الباقة -->
        @php
            // يمكنك تعديل هذا السطر ليناسب طريقة جلبك للغة النشطة في تطبيقك
            $isRtl = (app()->getLocale() === 'ar');
        @endphp

        {{--
            تحديد الاتجاه العام (dir="rtl" أو dir="ltr")
            واستبدال الفئات الثابتة (مثل justify-end) بأخرى تعتمد على الاتجاه (مثل flex-row-reverse)
        --}}
        <div class="flex-1 bg-white shadow rounded-lg p-6 hover:shadow-lg hover:scale-105 transition-transform transition-shadow duration-200" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

            {{--  1. عنوان ملخص الباقة  --}}
            {{--  استبدلنا justify-end بـ text-start/text-end أو استخدمنا flex justify-end / flex justify-start بناءً على $isRtl  --}}
            <div class="flex {{ $isRtl ? 'justify-start' : 'justify-end' }}">
                <h3 class="text-xl font-semibold">{{ trans_db('subscription.summary_title') }}</h3>
            </div>

            <div class="mt-4 space-y-2">
                {{--  2. الصفوف الأساسية (تعتمد على justify-between وهو يعمل بشكل جيد مع أي اتجاه)  --}}
                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">{{ trans_db('subscription.package_name_label') }}:</span>
                    <span>{{ ($user->subscription?->plan?->name) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">{{ trans_db('subscription.remaining_balance_label') }}:</span>
                    <span>{{ intval(($user->subscription?->remaining)) }}</span>
                </div>

                @if ($user->subscription?->plan)
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-500">{{ trans_db('subscription.carry_over_credit_label') }}:</span>

                        @if ($user->subscription->plan->carry_over_credit)
                            <span class="px-3 py-1 rounded-lg bg-green-100 text-green-700 font-semibold border border-green-300 shadow-sm">
                        {{ trans_db('general.yes_carry_over') }}
                    </span>
                        @else
                            <span class="px-3 py-1 rounded-lg bg-red-100 text-red-700 font-semibold border border-red-300 shadow-sm">
                        {{ trans_db('general.no_dont_carry_over') }}
                    </span>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">{{ trans_db('general.user_label') }}:</span>
                    <span> {{ ($user->name) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-bold text-gray-500">{{ trans_db('general.expiry_date_label') }}:</span>
                    <span>{{ ($user->subscription?->end_date) }}</span>
                </div>
            </div>

            {{--  3. قسم التبديل التلقائي  --}}
            <div class="mt-4 flex justify-between items-center">
                <span class="font-bold text-gray-500">{{ trans_db('subscription.auto_renew_label') }}</span>

                <form method="POST" action="{{ route('subscription.autoRenew') }}" id="auto-renew-form">
                    @csrf
                    <input type="hidden" name="auto_renew" value="0">
                    <label for="auto-renew" class="inline-flex items-center cursor-pointer relative">
                        <input type="checkbox" id="auto-renew" class="sr-only peer" name="auto_renew"
                               {{ old('auto_renew', $user->subscription?->auto_renew) ? 'checked' : '' }} onchange="document.getElementById('auto-renew-form').submit();" />
                        <div class="w-12 h-6 bg-gray-300 rounded-full peer-focus:ring-2 peer-focus:ring-blue-300 peer-checked:bg-blue-600 transition-colors"></div>

                        {{--  تعديل مكان المقبض في التوغل (Toggle)  --}}
                        <div class="absolute {{ $isRtl ? 'right-0.5' : 'left-0.5' }} top-0.5 bg-white w-5 h-5 rounded-full shadow transform peer-checked:{{ $isRtl ? '-translate-x-full' : 'translate-x-full' }} transition-transform"></div>
                    </label>
                </form>
            </div>

            {{--  4. رسالة التجديد التلقائي  --}}
            @if($user->subscription?->auto_renew)
                {{--  استبدال text-right بـ text-start أو text-end حسب الحاجة  --}}
                <div class="mt-4 border border-green-600 rounded-lg p-4 bg-green-50 {{ $isRtl ? 'text-right' : 'text-left' }}">
                    <p class="text-green-600">{{ trans_db('subscription.auto_renew_message_prefix') }} <strong>{{ ($user->subscription?->end_date) }}</strong></p>
                </div>
            @endif

            {{--  5. زر تجديد الباقة (Full-width button needs no direction change)  --}}
            <form action="{{ route('subscription.renewNow') }}" method="POST">
                @csrf
                <button type="submit" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 transition-colors transition-transform hover:scale-105 text-white font-bold py-2 px-4 rounded duration-200">{{ trans_db('subscription.renew_now_button') }}</button>
            </form>
        </div>
        <!-- بطاقة الرصيد المتاح -->
        @php
            // تحديد ما إذا كانت اللغة الحالية هي العربية (RTL)
            $isRtl = (app()->getLocale() === 'ar');
        @endphp

        <div id="recharge" class="flex-1 bg-white shadow rounded-lg p-6 hover:shadow-lg hover:scale-105 transition-transform transition-shadow duration-200" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

            {{--  1. عنوان الرصيد المتاح  --}}
            <div class="flex {{ $isRtl ? 'justify-start' : 'justify-end' }}">
                <h3 class="text-xl font-semibold">{{ trans_db('wallet.available_balance_title') }}</h3>
            </div>

            {{--  2. عرض الرصيد والوحدة  --}}
            {{--  يتم عكس ترتيب العناصر في RTL، مع محاذاة النص بناءً على الاتجاه  --}}
            <div class="mt-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                <span id="balance-amount" class="text-4xl font-bold text-green-600">{{ intval(($user->subscription?->balance)) }}</span>

                {{--  نستخدم flexbox لضمان بقاء "جنية" بجوار الرقم في جميع الاتجاهات  --}}
                <div class="inline-flex items-baseline {{ $isRtl ? 'flex-row-reverse' : 'flex-row' }}">
                    <span class="text-xl font-semibold text-green-600 {{ $isRtl ? 'mr-1' : 'ml-1' }}">{{ trans_db('wallet.currency_unit') }}</span>
                </div>

                <input type="hidden" id="balance-data" value="1500" data-unit-price="" />
            </div>

            {{--  3. رسالة الوثائق الممكن إصدارها  --}}
            {{--  استخدام text-start/text-end لضمان المحاذاة الصحيحة في كل لغة  --}}
            <p class="text-gray-500 mt-1 {{ $isRtl ? 'text-right' : 'text-left' }}">
                {{ trans_db('wallet.can_issue_prefix') }} {{ intval(($user->subscription?->balance)) }} {{ trans_db('wallet.can_issue_suffix') }}
            </p>


            {{--  4. عنوان شحن المحفظة  --}}
            <div class="{{ $isRtl ? 'text-right' : 'text-left' }} mt-6">
                <h4 class="text-lg font-semibold">{{ trans_db('wallet.recharge_wallet_title') }}</h4>
            </div>

            {{--  5. طرق الدفع  --}}
            @foreach($paymentMethods as $paymentMethod)
                <div class="bg-white border shadow rounded-lg p-4 mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1 {{ $isRtl ? 'text-right' : 'text-left' }}">
                        {{ $paymentMethod->key ?? trans_db('wallet.payment_method_default_name') }}
                    </label>

                    {{--  justify-between يعمل بشكل جيد  --}}
                    <div class="flex justify-between items-center">
                        <span id="account-number" class="text-gray-700">{{ $paymentMethod->value }}</span>
                        <button  onclick="copyToClipboard('value-{{ $loop->index }}')" title="{{ trans_db('general.copy_button_title') }}">
                            <i class="bi bi-clipboard text-lg text-gray-600"></i>
                        </button>
                    </div>
                </div>
            @endforeach


            <input type="file" id="file-attachment" class="hidden" />
            <button type="button" id="charge-wallet-btn" class="mt-4 w-full bg-green-600 hover:bg-green-700 transition-colors transition-transform hover:scale-105 text-white font-bold py-2 px-4 rounded duration-200">
                {{ trans_db('wallet.recharge_wallet_button') }}
            </button>
        </div>

        {{--  النافذة المنبثقة (Popup)  --}}
        <div id="wallet-charge-popup" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-auto relative" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

                {{--  زر الإغلاق - يجب عكس موقعه  --}}
                <button id="close-popup-btn" class="absolute top-2 {{ $isRtl ? 'left-2' : 'right-2' }} text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none" title="{{ trans_db('general.close_button_title') }}">
                    &times;
                </button>

                <h3 class="text-xl font-semibold mb-4 text-gray-800 {{ $isRtl ? 'text-right' : 'text-left' }}">{{ trans_db('wallet.confirm_recharge_title') }}</h3>

                <form id="charge-wallet-form" action="{{ route('wallet-recharge-request') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="plan_id" value="{{ $user->subscription?->plan?->id }}">
                        <input type="hidden" name="subscription_id" value="{{ $user->subscription?->id }}">

                        <label for="charge-amount" class="block text-gray-700 text-sm font-bold mb-2 {{ $isRtl ? 'text-right' : 'text-left' }}">{{ trans_db('wallet.charge_amount_label') }}:</label>
                        <input type="number" id="charge-amount" name="amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="{{ trans_db('wallet.charge_amount_placeholder') }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="receipt-upload" class="block text-gray-700 text-sm font-bold mb-2 {{ $isRtl ? 'text-right' : 'text-left' }}">{{ trans_db('wallet.receipt_upload_label') }}:</label>
                        <input type="file" id="receipt-upload" name="receipt_path" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer" required>
                    </div>

                    {{--  زر التأكيد يجب أن يكون في النهاية (يمين في RTL ويسار في LTR)  --}}
                    <div class="flex {{ $isRtl ? 'justify-start' : 'justify-end' }}">
                        <button type="submit" id="confirm-charge-popup" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">{{ trans_db('wallet.confirm_charge_button') }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <hr class="border-gray-300 my-4 fade-in" />
    <!-- Section: ترقية الباقة -->
    <div class="bg-gray-50 rounded-lg p-6 mb-8 shadow-sm">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 leading-tight mb-1">
               {{ trans_db('upgrade.plan') }}
            </h2>
            <p class="text-xl font-medium text-blue-600 mb-4">
                {{ trans_db('chose.your.plan') }}
            </p>
        </div>
    </div>

    @php
        // تحديد ما إذا كانت اللغة الحالية هي العربية (RTL)
        $isRtl = (app()->getLocale() === 'ar');
        $dirClass = $isRtl ? 'rtl' : 'ltr';
    @endphp

    <div id="upgrade" class="custom-plan-style flex justify-center" dir="{{ $dirClass }}">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-screen-xl mx-auto p-4">
            @foreach ($plans as $plan)
                @if($plan->is_public || ($plan->user_id == auth()->id()))
                    @php $isCurrentPlan = (auth()->check() && auth()->user()->subscription?->plan_id == $plan->id);
                    @endphp


                    <input type="radio" name="plan" id="plan-{{ $plan->id }}" value="{{ $plan->id }}" class="hidden peer"
                        {{ $isCurrentPlan ? 'checked disabled' : '' }}>

                    <label for="plan-{{ $plan->id }}"
                           class="card-label relative flex-grow flex-shrink-0 min-h-[24rem] bg-white rounded-xl shadow-md p-6 border-2
                    {{ $isCurrentPlan ? 'border-green-500' : 'border-transparent peer-checked:border-blue-600' }}
                    hover:shadow-lg transition-all duration-300 ease-in-out cursor-pointer flex flex-col justify-between">

                        @if($isCurrentPlan)
                            {{--  إصلاح: استخدام {!! ... !!} لعرض وسم الأيقونة والنص  --}}
                            <div class="absolute top-4 left-1/2 transform -translate-x-1/2">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-sm font-semibold text-green-800">
                                {!! $isRtl ? '<i class="fas fa-star ml-2"></i>' : '<i class="fas fa-star mr-2"></i>' !!}
                                {{ trans_db('plan.current_plan_label') }}
                            </span>
                            </div><br>
                        @endif


                        <div class="flex flex-col h-full">
                            <div>
                                {{--  محاذاة العنوان  --}}
                                <h3 class="text-2xl font-bold text-gray-800 mb-2 {{ $isRtl ? 'text-right' : 'text-left' }}">{{ $plan->name }}</h3>

                                {{--  محاذاة السعر  --}}
                                <div class="{{ $isRtl ? 'text-right' : 'text-left' }} text-3xl font-extrabold text-gray-900 mb-4">
                                    @if ($plan->compare_price)
                                        <span class="text-gray-500 line-through text-lg {{ $isRtl ? 'ml-2' : 'mr-2' }}">{{ $plan->compare_price }} {{ trans_db('wallet.currency_unit_short') }}</span>
                                    @endif
                                    <span>{{ $plan->price }} {{ trans_db('wallet.currency_unit_short') }}</span>
                                </div>
                            </div>

                            {{--  قائمة الميزات  --}}
                            <ul class="space-y-3 {{ $isRtl ? 'text-right' : 'text-left' }} flex-1 mb-4">
                                @foreach ( $plan->features_list as $feature)
                                    <li class="flex items-start">
                                        {{--  عكس الهامش وموقع الأيقونة  --}}
                                        <i class="fas fa-check-circle text-green-500 mt-1 {{ $isRtl ? 'ml-2' : 'mr-2' }} flex-shrink-0"></i>
                                        <span class="text-gray-600 text-base">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-auto">
                                @if ($plan->price > 0 && !$isCurrentPlan)
                                    {{--  تفاصيل الدفع  --}}
                                    <div class="bg-blue-50 rounded-lg p-3 mb-4 {{ $isRtl ? 'text-right' : 'text-left' }}">
                                        <h4 class="font-bold text-blue-800 mb-2">{{ trans_db('plan.payment_details_title') }}</h4>
                                        @foreach($paymentMethods as $payment)
                                            <div class="mb-2 last:mb-0">
                                                <p class="mb-1 font-semibold text-gray-700 {{ $isRtl ? 'text-right' : 'text-left' }}">{{ $payment->key }}:</p>

                                                {{--  عكس ترتيب العناصر في flex (القيمة وزر النسخ)  --}}
                                                <div class="flex items-center {{ $isRtl ? 'flex-row-reverse' : 'flex-row' }} justify-between bg-white p-2 rounded-lg border border-gray-200 font-mono text-sm text-gray-900">
                                                    <span id="value-{{ $loop->index }}" class="truncate">{{ $payment->value }}</span>
                                                    <button type="button" onclick="copyToClipboard('value-{{ $loop->index }}')" class="text-blue-500 hover:text-blue-700 transition-colors duration-200" title="{{ trans_db('general.copy_button_title') }}">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <form action="{{ route('plan.upgrade.request') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                        <input type="hidden" name="subscription_id" value="{{ $user->subscription?->id }}">

                                        <div id="filename-{{ $plan->id }}" class="mt-2 text-sm text-gray-500 text-center mb-2"></div>

                                        <label for="payment_receipt_{{ $plan->id }}" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 cursor-pointer flex items-center justify-center font-semibold">
                                            {{--  إصلاح: استخدام {!! ... !!} لعرض وسم الأيقونة  --}}
                                            {!! $isRtl ? '<i class="fas fa-cloud-upload-alt ml-2"></i>' : '<i class="fas fa-cloud-upload-alt mr-2"></i>' !!}
                                            {{ trans_db('plan.attach_receipt_button') }}

                                            <input
                                                type="file"
                                                id="payment_receipt_{{ $plan->id }}"
                                                name="receipt_path"
                                                class="hidden payment-upload"
                                                data-plan="{{ $plan->id }}"
                                            >
                                        </label>

                                        <button type="submit"
                                                class="block text-center w-full bg-green-500 text-white py-3 rounded-lg mt-2 hover:bg-green-600 transition-colors duration-200 font-semibold">
                                            {{ trans_db('plan.upgrade_plan_button') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </label>
                @endif
            @endforeach

            {{--  باقة مخصصة (Custom Plan Card)  --}}
            <div class="flex-grow flex-shrink-0 min-h-[24rem] bg-gray-100 rounded-xl shadow-md p-6 flex flex-col justify-between items-center {{ $isRtl ? 'text-right' : 'text-left' }}">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ trans_db('plan.custom_plan_title') }}</h3>
                    <div class="font-base text-xl mb-2">
                        <p class="text-gray-600 text-base mb-2">
                            {{ trans_db('plan.custom_plan_desc_p1') }}
                        </p>
                        <p class="text-gray-600 text-base">
                            {{ trans_db('plan.custom_plan_desc_p2') }}
                        </p>
                        <p class="text-gray-600 text-base mt-2">{{ trans_db('plan.contact_us_label') }}</p>
                        <div class="my-4">
                            <i class="fas fa-phone-alt text-5xl text-blue-600"></i>
                        </div>
                        @foreach($phones as $phone)
                            {{--  تعديل اتجاه الهاتف وزر النسخ  --}}
                            <div class="flex items-center {{ $isRtl ? 'flex-row-reverse' : 'flex-row' }} justify-between mb-2">
                                <a href="tel:{{ $phone->phone_number }}"
                                   class="text-blue-600 hover:underline transition-colors duration-200 font-bold">
                                    {{ $phone->phone_number }}
                                </a>
                                <button type="button" onclick="copyToClipboard('{{ $phone->phone_number }}')"
                                        class="text-blue-500 hover:text-blue-700 transition-colors duration-200" title="{{ trans_db('general.copy_button_title') }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="min-h-[1rem]"></div>
            </div>

        </div>
    </div>

    <script>
        // هذه الوظيفة لعرض اسم الملف المرفوع
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.payment-upload').forEach(input => {
                input.addEventListener('change', function (e) {
                    const planId = e.target.dataset.plan;
                    const filenameDiv = document.getElementById(`filename-${planId}`);
                    if (e.target.files.length > 0) {
                        filenameDiv.textContent = e.target.files[0].name;
                    } else {
                        filenameDiv.textContent = '';
                    }
                });
            });
        });

        // الوظائف الأخرى
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                Toastify({
                    text: "تم نسخ القيمة",
                    duration: 2000,
                    gravity: "top",
                    position: "{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}",
                    backgroundColor: "#10B981",
                    stopOnFocus: true
                }).showToast();
            }).catch(err => {
                Toastify({
                    text: "فشل النسخ!",
                    duration: 2000,
                    gravity: "top",
                    position: "{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}",
                    backgroundColor: "#EF4444",
                    stopOnFocus: true
                }).showToast();
            });
        }

        function handlePlanChange(selectedPlanId) {
            document.querySelectorAll('.payment-upload').forEach(input => {
                input.removeAttribute('required');
            });

            const selectedInput = document.querySelector(`.payment-upload[data-plan="${selectedPlanId}"]`);
            if (selectedInput) {
                selectedInput.setAttribute('required', 'required');
            }
        }
    </script>

    <hr class="border-gray-300 my-4 fade-in" />
    @php
        // تحديد ما إذا كانت اللغة الحالية هي العربية (RTL)
        $isRtl = (app()->getLocale() === 'ar');
        $dirClass = $isRtl ? 'rtl' : 'ltr';
        // تحديد الهامش المناسب للأيقونات
        $iconMarginClass = $isRtl ? 'ms-2' : 'me-2';
        $textAlignClass = $isRtl ? 'text-right' : 'text-left';
    @endphp

    {{-- ======================================================= --}}
    {{-- 💰 سجل المعاملات (Subscription History) --}}
    {{-- ======================================================= --}}
    <section class="{{ $textAlignClass }} fade-in mb-8" dir="{{ $dirClass }}">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">{{ trans_db('history.transactions_log_title') }}</h2>

            <button id="sort-transactions" type="button" class="flex items-center bg-gray-100 hover:bg-gray-200 hover:shadow-md transition-all rounded-lg px-4 py-2 font-semibold">
                {{-- ضبط اتجاه الأيقونة والهامش --}}
                <span id="sort-text">{{ trans_db('history.sort_latest_first') }}</span>
                <i id="sort-icon" class="bi bi-arrow-down {{ $iconMarginClass }}"></i>
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="overflow-hidden rounded-lg shadow">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                    <tr class="{{ $textAlignClass }}">
                        {{-- ضبط اتجاه النصوص في رؤوس الأعمدة --}}
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_operation_type') }}</th>
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_plan_type') }}</th>
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_price') }}</th>
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_date') }}</th>
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($user->subscription && $user->subscription->histories->count() > 0)
                        @foreach($user->subscription->histories as $history)
                            <tr class="border-t hover:bg-gray-50 transition-colors cursor-pointer">
                                {{-- نوع العملية: initial, upgrade, renewal. نستخدم مفاتيح ترجمة للـ types --}}
                                <td class="px-4 py-3 {{ $textAlignClass }}">{{ trans_db('history.type_' . $history->type) }}</td>
                                <td class="px-4 py-3 {{ $textAlignClass }}">
                                    {{ $history->subscription->plan->name ?? trans_db('general.unknown') }}
                                </td>
                                <td class="px-4 py-3 {{ $textAlignClass }}">
                                    {{ intval ($history->subscription->plan->price ?? 0) }} {{ trans_db('wallet.currency_unit_short') }}
                                </td>
                                <td class="px-4 py-3 {{ $textAlignClass }}">{{ \Carbon\Carbon::parse($history->start_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 {{ $textAlignClass }}">
                                    @php
                                        $statusClass = '';
                                        switch($history->status) {
                                            case 'active':
                                                $statusClass = 'text-green-700 bg-green-50 border border-green-600';
                                                $statusText = trans_db('history.status_completed');
                                                break;
                                            case 'pending':
                                                $statusClass = 'text-yellow-700 bg-yellow-50 border border-yellow-600';
                                                $statusText = trans_db('history.status_pending_review');
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'text-red-700 bg-red-50 border border-red-600';
                                                $statusText = trans_db('history.status_rejected');
                                                break;
                                            case 'expired':
                                                $statusClass = 'text-red-700 bg-red-50 border border-red-600';
                                                $statusText = trans_db('history.status_expired');
                                                break;
                                            case 'paused':
                                                $statusClass = 'text-blue-700 bg-blue-50 border border-blue-600';
                                                $statusText = trans_db('history.status_paused');
                                                break;
                                            default:
                                                $statusClass = 'text-gray-700 bg-gray-50 border border-gray-600';
                                                // إذا لم يكن هناك مفتاح ترجمة للحالة، اعرض القيمة الأصلية
                                                $statusText = trans_db('history.status_' . $history->status, $history->status);
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
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                {{ trans_db('history.no_transactions_message') }}
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <hr class="border-gray-300 my-4 fade-in" />
    {{-- ======================================================= --}}
    {{-- 💸 سجل عمليات الشحن (Recharge History) --}}
    {{-- ملاحظة: تم استخدام نفس بيانات الـ histories لتجنب إنشاء حلقة تكرار وهمية --}}
    {{-- المفترض في الواقع استخدام جدول منفصل مثل 'recharge_histories' --}}
    {{-- ======================================================= --}}
    <section class="{{ $textAlignClass }} fade-in mb-8" dir="{{ $dirClass }}">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">{{ trans_db('history.recharge_log_title') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <div class="overflow-hidden rounded-lg shadow">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                    <tr class="{{ $textAlignClass }}">
                        {{-- تم تعديل رأس العمود الخامس --}}
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_operation_type') }}</th>
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_plan_type') }}</th>
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_recharge_amount') }}</th>
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_date') }}</th>
                        <th class="px-4 py-2 text-gray-700 {{ $textAlignClass }}">{{ trans_db('history.col_status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($user->subscription && $user->subscription->histories->count() > 0)
                        {{-- **تنبيه:** تم الإبقاء على نفس متغيرات الاشتراك/الباقة للتمثيل، لكن يجب استخدام بيانات الشحن الفعلية هنا --}}
                        @foreach($user->subscription->histories as $history)
                            <tr class="border-t hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-4 py-3 {{ $textAlignClass }}">{{ trans_db('history.type_' . $history->type) }}</td>
                                <td class="px-4 py-3 {{ $textAlignClass }}">
                                    {{ $history->subscription->plan->name ?? trans_db('general.unknown') }}
                                </td>
                                {{-- استخدام القيمة الافتراضية للتمثيل - يتم تعديلها حسب البيانات الفعلية للشحن --}}
                                <td class="px-4 py-3 {{ $textAlignClass }}">
                                    {{ intval ($history->subscription->plan->price ?? 0) }} {{ trans_db('wallet.currency_unit_short') }}
                                </td>
                                <td class="px-4 py-3 {{ $textAlignClass }}">{{ \Carbon\Carbon::parse($history->start_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 {{ $textAlignClass }}">
                                    @php
                                        $statusClass = '';
                                        // استخدام نفس منطق الحالات لـ recharge
                                        switch($history->status) {
                                            case 'active':
                                                $statusClass = 'text-green-700 bg-green-50 border border-green-600';
                                                $statusText = trans_db('history.status_completed');
                                                break;
                                            case 'pending':
                                                $statusClass = 'text-yellow-700 bg-yellow-50 border border-yellow-600';
                                                $statusText = trans_db('history.status_pending_review');
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'text-red-700 bg-red-50 border border-red-600';
                                                $statusText = trans_db('history.status_rejected');
                                                break;
                                            default:
                                                $statusClass = 'text-gray-700 bg-gray-50 border border-gray-600';
                                                $statusText = trans_db('history.status_failed');
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
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                {{ trans_db('history.no_recharges_message') }}
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

@include('partials.footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // =================================================================================
        //  1. وظائف نسخ إلى الحافظة (Clipboard)
        // =================================================================================

        window.copyToClipboard = function(input) {
            let textToCopy;

            // Check if the input is a DOM element ID
            const el = document.getElementById(input);
            if (el) {
                textToCopy = el.innerText || el.textContent;
            } else {
                // Assume the input is the text itself
                textToCopy = input;
            }

            if (!textToCopy) {
                return;
            }

            navigator.clipboard.writeText(textToCopy).then(() => {
                Toastify({
                    text: "تم النسخ إلى الحافظة",
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
        };

        // =================================================================================
        //  2. وظائف نافذة شحن المحفظة المنبثقة (Popup)
        // =================================================================================

        // تحديد العناصر
        const chargeWalletBtn = document.getElementById('charge-wallet-btn');
        const walletChargePopup = document.getElementById('wallet-charge-popup');
        const closePopupBtn = document.getElementById('close-popup-btn');
        const chargeWalletForm = document.getElementById('charge-wallet-form');

        // دالة مساعدة لإخفاء البوب أب
        function hidePopup() {
            walletChargePopup.classList.add('hidden');
            chargeWalletForm.reset();
        }

        // دالة مساعدة لعرض رسائل Toastify
        function showToast(message, type = 'success') {
            const backgroundColor = (type === 'success') ? '#10B981' : '#EF4444';
            Toastify({
                text: message,
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: backgroundColor,
                stopOnFocus: true
            }).showToast();
        }

        // فتح نافذة البوب أب عند الضغط على زر "شحن المحفظة"
        chargeWalletBtn.addEventListener('click', function(event) {
            event.preventDefault();
            walletChargePopup.classList.remove('hidden');
        });

        // إغلاق البوب أب عند الضغط على زر الإغلاق (X)
        closePopupBtn.addEventListener('click', hidePopup);

        // إغلاق البوب أب عند الضغط على الخلفية الشفافة
        walletChargePopup.addEventListener('click', function(event) {
            if (event.target === walletChargePopup) {
                hidePopup();
            }
        });

        // التعامل مع إرسال الفورم
        chargeWalletForm.addEventListener('submit', function(event) {
            const chargeAmount = document.getElementById('charge-amount').value;
            const receiptFile = document.getElementById('receipt-upload').files[0];

            if (!chargeAmount || !receiptFile) {
                event.preventDefault();
                showToast('الرجاء إدخال قيمة الشحن ورفع الوصل.', 'error');
            }
        });

        // =================================================================================
        // 3. وظائف إرفاق وصل الدفع
        // =================================================================================

        document.querySelectorAll('.payment-upload').forEach(input => {
            input.addEventListener('change', function (e) {
                const planId = e.target.dataset.plan;
                const filenameDiv = document.getElementById(`filename-${planId}`);
                if (e.target.files.length > 0) {
                    filenameDiv.textContent = e.target.files[0].name;
                } else {
                    filenameDiv.textContent = '';
                }
            });
        });

    });


    // =================================================================================
    // 4. وظائف ترتيب جدول العمليات (الأحدث للأقدم أولاً)
    // =================================================================================

    const sortTransactionsBtn = document.getElementById('sort-transactions');
    const sortTextSpan = document.getElementById('sort-text');
    const sortIcon = document.getElementById('sort-icon');

    // ⭐ التعديل الأول: نجعل الترتيب الافتراضي تنازلياً (false = الأحدث أولاً)
    // true = تصاعدي (الأقدم أولاً) / false = تنازلي (الأحدث أولاً)
    let isAscending = false;

    /**
     * دالة مساعدة لتحويل تنسيق التاريخ (YYYY-M-D) إلى كائن Date للمقارنة.
     * @param {string} dateString - سلسلة التاريخ من الخلية (مثل "2023-05-25").
     * @returns {Date} - كائن Date.
     */
    function formatDate(dateString) {
        // التاريخ في الجدول يأتي بتنسيق 'Y-m-d' من Carbon، يمكن تمريره مباشرة
        return new Date(dateString.trim());
    }

    /**
     * دالة لترتيب الجداول بناءً على التاريخ في العمود الرابع.
     */
    function sortTable() {
        const tables = document.querySelectorAll('table');

        tables.forEach(table => {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            // تخطي الجدول إذا كان يحتوي على رسالة "لا يوجد سجل معاملات"
            if (rows.length === 1 && rows[0].querySelector('td[colspan]')) {
                return;
            }

            rows.sort((a, b) => {
                // الحصول على خلية التاريخ (العمود الرابع - الفهرس 3)
                const dateA = formatDate(a.cells[3].textContent.trim());
                const dateB = formatDate(b.cells[3].textContent.trim());

                let comparison = 0;

                if (dateA > dateB) {
                    comparison = 1; // A أحدث من B
                } else if (dateA < dateB) {
                    comparison = -1; // A أقدم من B
                }

                // ⭐ التعديل الثاني في منطق الترتيب
                // إذا كان تصاعدياً (isAscending = true) قارن طبيعي (الأقدم أولاً).
                // إذا كان تنازلياً (isAscending = false) اعكس المقارنة (الأحدث أولاً).
                return isAscending ? comparison : comparison * -1;
            });

            // إعادة إضافة الصفوف بالترتيب الجديد إلى الـ tbody
            rows.forEach(row => tbody.appendChild(row));
        });
    }

    /**
     * دالة لتبديل حالة الترتيب وتحديث الجدول والزر.
     */
    function toggleSortOrder() {
        // عكس حالة الترتيب
        isAscending = !isAscending;

        // تحديث نص وأيقونة الزر ليعكس الترتيب *الجديد*
        if (isAscending) {
            // إذا أصبح تصاعدياً (الأقدم أولاً)
            sortTextSpan.textContent = 'الأقدم أولاً';
            sortIcon.className = 'bi bi-arrow-up ms-2';
        } else {
            // إذا أصبح تنازلياً (الأحدث أولاً)
            sortTextSpan.textContent = 'الأحدث أولاً';
            sortIcon.className = 'bi bi-arrow-down ms-2';
        }

        // استدعاء دالة الترتيب
        sortTable();
    }

    // تنفيذ الترتيب الافتراضي (الأحدث أولاً) عند تحميل الصفحة
    // تأكد من أن الدالة sortTable يتم استدعاؤها بعد تحميل الجدول بالكامل
    window.onload = function() {
        if (sortTransactionsBtn) {
            // ربط الدالة بالزر
            sortTransactionsBtn.addEventListener('click', toggleSortOrder);

            // تطبيق الترتيب الأولي (الأحدث أولاً)
            sortTable();
        }
    };
</script>

<script src=" {{ asset('js/cash.js') }}"></script>
</body>
</html>
