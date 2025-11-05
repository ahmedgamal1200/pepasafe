<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.jpg') }}">
    <title>Create New Account For Eventor</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        .navbar { box-sizing: content-box; }
        body {
            background-color: #f3f9f9;
            font-family: "Segoe UI", Tahoma, Verdana, sans-serif;
            margin: 0; padding: 0;
            /*display: flex; flex-direction: column; align-items: center;*/
            min-height: 100vh;
        }
        .form-container {
            background-color: #fff;
            /* تم زيادة العرض ليكون أكبر على الشاشات الكبيرة */
            width: 90%;
            max-width: 600px;
            padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            margin: 30px 0; text-align: center;
        }
        @media (max-width: 600px) {
            .form-container {
                /* تم زيادة العرض على الموبايل ليملأ مساحة أكبر */
                width: 95%;
                max-width: 380px;
                padding: 15px; margin: 20px 0;
            }
        }
        .form-header { font-size:24px; font-weight:bold; color:#333; margin-bottom:20px; }
        .login-btn {
            width:100%; padding:12px; font-size:16px;
            background-color:#2e65d8; color:#fff; border:none;
            border-radius:4px; cursor:pointer; margin-top:20px;
        }
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


        /* hiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii*/
        .toggle-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .toggle-buttons a,
        .toggle-buttons label {
            flex: 1;
            text-align: center;
            text-decoration: none; /* يشيل الخط تحت اللينك */
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
        }

        .user-btn {
            background-color: #fff;
            color: #333;
        }

        .advanced-btn {
            background-color: #2e65d8;
            color: #fff;
            border: none;
        }

        .signup { font-size: 14px; color: #555; }
        .signup a { color: #2e65d8; text-decoration: none; }

        .custom-space-fix > *:not(:last-child) {
            margin-inline-end: 1rem; /* حوالي 16px */
        }
        [dir="rtl"] .custom-space-fix > *:not(:last-child) {
            margin-inline-end: 0;
            margin-inline-start: 1rem;
        }
    </style>
</head>
<body>

{{-- <div dir="ltr" class="text-right"> --}}
    <div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">

    @include('partials.navbar')
</div>

<div class="flex flex-col items-center">
    <div class="form-container">
        <div class="form-header">{{ trans_db('register.title') }}</div>

        @php
            // هذا الجزء يجب أن يكون معرّفًا مرة واحدة في بداية ملف الـ Blade
            $isRTL = app()->getLocale() === 'ar';
            $textAlignment = $isRTL ? 'text-right' : 'text-left'; // المحاذاة الديناميكية
            $direction = $isRTL ? 'rtl' : 'ltr'; // الاتجاه الديناميكي
        @endphp

        <div class="text-sm font-medium {{ $textAlignment }} mb-2" dir="{{ $direction }}">
            {{ trans_db('account.type') }}
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" onsubmit="return validatePlanSelection()" >
            @csrf

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 border border-red-400 p-4 rounded mb-6">
                    <ul class="list-disc list-inside text-right">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="toggle-buttons">
                <a href="{{ route('register.user') }}" class="user-btn">{{ trans_db('type.user') }}</a>
                <input type="radio" name="role" id="acct_org" value="eventor" class="hidden" checked required>
                <label for="acct_org" class="advanced-btn label-btn">{{ trans_db('type.eventor') }}</label>

            </div>



            @php
                $isRTL = app()->getLocale() === 'ar';
                $textAlignment = $isRTL ? 'text-right' : 'text-left';
                $direction = $isRTL ? 'rtl' : 'ltr';
                $placeholderAlignment = $isRTL ? 'placeholder-right' : 'placeholder-left'; // قد تحتاج لتطبيق هذا في CSS
            @endphp

            <label for="name" class="block text-sm font-medium {{ $textAlignment }} mb-1" dir="{{ $direction }}">
                {{ trans_db('register.name') }}
            </label>

            <input type="text"
                   id="name"
                   name="name"
                   required
                   placeholder="{{ trans_db('register.name.placeholder') }}"
                   value="{{ old('name') }}"
                   class="w-full p-2 border border-gray-300 rounded mb-4 {{ $textAlignment }}"
                   dir="{{ $direction }}">

            <label for="phone" class="block text-sm font-medium {{ $textAlignment }} mb-1" dir="{{ $direction }}">
                {{ trans_db('register.phone') }}
            </label>
            <input type="text"
                   id="phone"
                   name="phone"
                   required
                   placeholder="{{ trans_db('register.phone.placeholder') }}"
                   value="{{ old('phone') }}"
                   class="w-full p-2 border border-gray-300 rounded mb-4 {{ $textAlignment }}"
                   dir="{{ $direction }}">

            <label for="email" class="block text-sm font-medium {{ $textAlignment }} mb-1" dir="{{ $direction }}">
                {{ trans_db('register.email') }}
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   placeholder="{{ trans_db('register.email.placeholder') }}"
                   required
                   value="{{ old('email') }}"
                   class="w-full p-2 border border-gray-300 rounded mb-4 {{ $textAlignment }}"
                   dir="{{ $direction }}">

            <label for="password" class="block text-sm font-medium {{ $textAlignment }} mb-1" dir="{{ $direction }}">
                {{ trans_db('login.password') }}
            </label>
            <input type="password"
                   id="password"
                   placeholder="{{ trans_db('login.password.placeholder') }}"
                   name="password"
                   required
                   class="w-full p-2 border border-gray-300 rounded mb-4 {{ $textAlignment }}"
                   dir="{{ $direction }}">

            <label for="confirm_password" class="block text-sm font-medium {{ $textAlignment }} mb-1" dir="{{ $direction }}">
                {{ trans_db('login.password.conf') }}
            </label>
            <input type="password"
                   id="confirm_password"
                   placeholder="{{ trans_db('login.password.placeholder') }}"
                   name="password_confirmation"
                   required
                   class="w-full p-2 border border-gray-300 rounded mb-6 {{ $textAlignment }}"
                   dir="{{ $direction }}">


            <div class="text-sm font-medium mb-2 {{ $textAlignment }}"
                 dir="{{ $direction }}">{{ trans_db('category') }}</div>
            <select id="category" name="category" required class="w-full p-2 border border-gray-300 rounded">
                <option value="">{{ trans_db('choose.cat') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            data-icon='{!! App\Helpers\IconHelper::get($category->icon) !!}'>
                        {{ $category->type }}
                    </option>
                @endforeach
            </select>


            <script>
                new TomSelect("#category",{
                    render: {
                        option: function(data, escape) {
                            return `<div style="display:flex;align-items:center;gap:6px;">
                        ${data.icon ?? ''}
                        <span>${escape(data.text)}</span>
                    </div>`;
                        },
                        item: function(data, escape) {
                            return `<div style="display:flex;align-items:center;gap:6px;">
                        ${data.icon ?? ''}
                        <span>${escape(data.text)}</span>
                    </div>`;
                        }
                    },
                    data: {
                        icon: function(option) {
                            return option.getAttribute('data-icon');
                        }
                    }
                });
            </script>


            <div class="w-full mb-6 {{ $textAlignment }}"
                 dir="{{ $direction }}">
                <label for="terms" class="inline-block align-middle">
                    <input type="checkbox" id="terms" name="terms_agreement" required class="w-4 h-4 ml-2" >
                    <span>{{ trans_db('i.agree') }} <a href="{{ route('about') }}#terms" class="text-blue-500 underline">{{ trans_db('terms') }}</a></span>
                </label>
            </div>

            <div class="text-lg font-semibold mb-4  {{ $textAlignment }}"
                 dir="{{ $direction }}">{{ trans_db('choose.plan') }}</div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($plans as $plan)
                    @if($plan->is_public == 1)
                    <input type="radio" name="plan" id="plan-{{ $plan->id }}" value="{{ $plan->id }}" class="hidden" >
                    <label for="plan-{{ $plan->id }}" class="card-label bg-white rounded-lg p-4 hover:shadow-lg transition cursor-pointer">
                        @php
                            // هذا الجزء يفترض وجوده مرة واحدة في بداية ملف الـ Blade
                            $isRTL = app()->getLocale() === 'ar';
                            $textAlignment = $isRTL ? 'text-right' : 'text-left';
                            $direction = $isRTL ? 'rtl' : 'ltr';
                            // الهامش يكون يمين الأيقونة (mr-2) في RTL، ويسارها (ml-2) في LTR
                            $iconMargin = $isRTL ? 'mr-2' : 'ml-2';
                        @endphp

                        <div class="text-lg font-semibold {{ $textAlignment }} mb-3" dir="{{ $direction }}">
                            {{ $plan->name }}
                        </div>

                        <div class="{{ $textAlignment }} text-xl font-bold mb-2" dir="{{ $direction }}">
                            @if ($plan->compare_price)
                                <span class="text-gray-500 line-through text-base {{ $isRTL ? 'mr-2' : 'ml-2' }}">
                                    {{ $plan->compare_price }} {{ trans_db('EG') }}
                                </span>
                            @endif
                            <span>{{ $plan->price }} {{ trans_db('EG') }}</span>
                        </div>

                        <ul class="space-y-2 {{ $textAlignment }} mb-4" dir="{{ $direction }}">
                            @foreach ( $plan->features_list as $feature)
                                <li class="flex items-start {{ $isRTL ? 'flex-row-reverse' : 'flex-row' }}">
                                    <i class="fas fa-check-circle text-green-500 {{ $iconMargin }}"></i>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        @if ($plan->price > 0)
                            <div class="bg-gray-100 rounded p-3 mb-4 text-right">
                                @foreach($paymentMethods as $payment)
                                    <div class="mb-2" dir="{{ $direction }}">
                                        <div class="mb-1 font-semibold {{ $textAlignment }}">{{ $payment->key }}:</div>

                                        <div class="flex items-center justify-between bg-white p-2 rounded border font-mono text-sm">

                                            <span id="value-{{ $loop->index }}" class="{{ $textAlignment }}">{{ $payment->value }}</span>

                                            <button type="button" onclick="copyToClipboard('value-{{ $loop->index }}')" class="text-blue-500 hover:text-blue-700">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @php
                                // هذا الجزء يفترض وجوده مرة واحدة في بداية ملف الـ Blade
                                $isRTL = app()->getLocale() === 'ar';
                                $textAlignment = $isRTL ? 'text-right' : 'text-left';
                                $direction = $isRTL ? 'rtl' : 'ltr';
                                $flexDirection = $isRTL ? 'flex-row-reverse' : 'flex-row';
                            @endphp

                            <label for="payment_receipt_{{ $plan->id }}"
                                   class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition cursor-pointer
                                      flex items-center justify-center
                                      {{ $flexDirection }}
                                      gap-3"
                                   dir="{{ $direction }}">

                                <i class="fas fa-cloud-upload-alt"></i>

                                {{ trans_db('attachments') }}

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
                    @endif
                        @endforeach
            </div>


            <button type="submit" class="login-btn">{{ trans_db('create.acc') }}</button>

            <div class="signup">
                {{ trans_db('Do.you.have.an.account') }} <a href="{{ route('login') }}">{{trans_db('login')}} </a>
            </div>
        </form>
    </div>
</div>


<script>
    function copyToClipboard(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            Toastify({
                text: "تم نسخ القيمة",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10B981", // لون أخضر جميل
                stopOnFocus: true
            }).showToast();
        }).catch(err => {
            Toastify({
                text: "فشل النسخ!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#EF4444", // لون أحمر
                stopOnFocus: true
            }).showToast();
        });
    }

    // الجزء الخاص ب اختيار الباقة
    function handlePlanChange(selectedPlanId) {
        // احذف required من كل ملفات الدفع
        document.querySelectorAll('.payment-upload').forEach(input => {
            input.removeAttribute('required');
        });

        // ضيف required للي تخص الباقة المختارة
        const selectedInput = document.querySelector(`.payment-upload[data-plan="${selectedPlanId}"]`);
        if (selectedInput) {
            selectedInput.setAttribute('required', 'required');
        }
    }

    // validation for plans
    function validatePlanSelection() {
        const selectedPlan = document.querySelector('input[name="plan"]:checked');

        if (!selectedPlan) {
            Toastify({
                text: "من فضلك اختر باقة أولاً.",
                duration: 3000, // 3 ثواني
                gravity: "top", // يظهر في الأعلى
                position: "{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}", // يظهر على جنب الشاشة (يمين في العربي، شمال في الإنجليزي)
                backgroundColor: "#EF4444", // لون أحمر للخطأ
                stopOnFocus: true
            }).showToast();
            return false;
        }

        return true;
    }

    // ✅ إخفاء الخطأ عند اختيار الباقة
    document.querySelectorAll('input[name="plan"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            // مش محتاجين نعمل حاجة هنا خلاص، رسالة الخطأ هتظهر وتختفي لوحدها
        });
    });

    // إضافة وظيفة جديدة لعرض اسم الملف عند رفعه
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
</script>

</body>
</html>
