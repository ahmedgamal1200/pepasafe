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
        .toggle-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 1rem;
        }

        .toggle-buttons a,
        .toggle-buttons label {
            flex: 1;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            width: 100%;
        }

        .user-btn {
            background-color: #2e65d8;
            color: #fff;
            border: none;
        }

        .advanced-btn {
            background-color: #fff;
            color: #333;
            border: 1px solid #ccc;
            text-decoration: none;
        }

        .signup { font-size: 14px; color: #555; }
        .signup a { color: #2e65d8; text-decoration: none; }


    </style>
</head>
<body>

{{--@include('partials.auth-navbar')--}}
<div class="form-container">
    <div class="form-header">إنشاء حساب جديد</div>

    <div class="text-sm font-medium text-right mb-2">نوع الحساب</div>

    <form method="POST" action="{{ route('register.user') }}" enctype="multipart/form-data">
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
            <input type="radio" name="role" id="acct_user" value="user" class="hidden" checked required>
            <label for="acct_user" class="user-btn label-btn">مستخدم</label>

            <a href="{{ route('register') }}" class="advanced-btn">منظم</a>
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


        <div class="w-full text-right mb-6">
            <label for="terms" class="inline-block align-middle">
                <input type="checkbox" id="terms" name="terms_agreement" required class="w-4 h-4 align-middle ml-2">
                <span>أوافق على <a href="{{ route('about') }}#terms" class="text-blue-500 underline">الشروط والأحكام</a></span>
            </label>
        </div>

        <button type="submit" class="login-btn">إنشاء الحساب</button>

        <div class="signup">
            لديك حساب ؟ <a href="{{ route('login') }}">تسجيل دخول </a>
        </div>
    </form>
</div>



{{--@include('partials.footer')--}}


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
</script>




</body>
</html>
