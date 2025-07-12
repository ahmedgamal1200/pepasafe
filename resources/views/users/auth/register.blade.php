<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إنشاء حساب جديد</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        .navbar { box-sizing: content-box; }
        body {
            background-color: #f3f9f9;
            font-family: "Segoe UI", Tahoma, Verdana, sans-serif;
            margin: 0; padding: 0;
            display: flex; flex-direction: column; align-items: center;
            min-height: 100vh;
        }
        .form-container {
            background-color: #fff;
            width: 60%; max-width: 500px;
            padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            margin: 30px 0; text-align: center;
        }
        @media (max-width: 600px) {
            .form-container {
                width: 90%; max-width: 320px; padding: 15px; margin: 20px 0;
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


    /*    hiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii*/
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
    </style>
</head>
<body>

{{--@include('partials.navbar')--}}

<div class="form-container">
    <div class="form-header">إنشاء حساب جديد</div>

    <div class="text-sm font-medium text-right mb-2">نوع الحساب</div>

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
            <a href="{{ route('register.user') }}" class="user-btn">مستخدم</a>
            <input type="radio" name="role" id="acct_org" value="eventor" class="hidden" checked required>
            <label for="acct_org" class="advanced-btn label-btn">منظم</label>

        </div>

        <div class="text-lg font-semibold text-right mb-4">البيانات الأساسية</div>


        <label for="name" class="block text-sm font-medium text-right mb-1">الاسم الكامل</label>
        <input type="text" id="name" name="name" required value="{{ old('name') }}" class="w-full p-2 border border-gray-300 rounded mb-4 text-right">

        <label for="phone" class="block text-sm font-medium text-right mb-1">رقم الهاتف</label>
        <input type="text" id="phone" name="phone" required  value="{{ old('phone') }}" class="w-full p-2 border border-gray-300 rounded mb-4 text-right">

        <label for="email" class="block text-sm font-medium text-right mb-1">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}" class="w-full p-2 border border-gray-300 rounded mb-4 text-right">

        <label for="password" class="block text-sm font-medium text-right mb-1">كلمة المرور</label>
        <input type="password" id="password" placeholder="كلمة المرور" name="password" required class="w-full p-2 border border-gray-300 rounded mb-4 text-right">

        <label for="confirm_password" class="block text-sm font-medium text-right mb-1">تأكيد كلمة المرور</label>
        <input type="password" id="confirm_password" placeholder="تأكيد كلمة المرور" name="password_confirmation" required class="w-full p-2 border border-gray-300 rounded mb-6 text-right">

        <div class="text-sm font-medium text-right mb-2">الفئة</div>
        <select name="category"  required class="w-full p-2 border border-gray-300 rounded mb-4 text-right appearance-none bg-white">
            <option value="">اختر فئة</option>
             @foreach($categories as $category)
             <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>
                 {{ $category->type }}
             </option>
             @endforeach
        </select>

        <div class="w-full text-right mb-6">
            <label for="terms" class="inline-block align-middle">
                <input type="checkbox" id="terms" name="terms_agreement" required class="w-4 h-4 align-middle ml-2">
                <span>أوافق على <a href="{{ route('about') }}#terms" class="text-blue-500 underline">الشروط والأحكام</a></span>
            </label>
        </div>

        <div class="text-lg font-semibold text-right mb-4">أختيار الباقة </div>
            <div id="plan-error" class="text-red-600 text-base -mt-1 mb-2 hidden">
                من فضلك اختر باقة أولاً.
            </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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


        <button type="submit" class="login-btn">إنشاء الحساب</button>

        <div class="signup">
            لديك حساب ؟ <a href="{{ route('login') }}">تسجيل دخول </a>
        </div>
    </form>
</div>


<script>
    function copyToClipboard(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            Toastify({
                text: "تم نسخ القيمة!",
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
        const errorDiv = document.getElementById('plan-error');

        if (!selectedPlan) {
            errorDiv.classList.remove('hidden');
            return false;
        }

        errorDiv.classList.add('hidden');
        return true;
    }

    // ✅ إخفاء الخطأ عند اختيار الباقة
    document.querySelectorAll('input[name="plan"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            document.getElementById('plan-error').classList.add('hidden');
        });
    });
</script>

</body>
</html>
