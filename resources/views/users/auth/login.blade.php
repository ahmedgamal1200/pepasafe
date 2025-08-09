<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>صفحة تسجيل الدخول</title>

    <!-- تحميل Tailwind مرة واحدة -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome للأيقونات -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>



    <style>
        /* إعادة تعيين أساسيات الصندوق */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        /* تنسيقات الجسم لجعل المحتوى في المنتصف تمامًا */
        body {
            background-color: #f3f9f9;
            font-family: "Inter", "Segoe UI", Tahoma, Verdana, sans-serif; /* استخدام Inter كخط أساسي */
            margin: 0;
            padding: 0;
            /*display: flex;*/
            justify-content: center; /* توسيط أفقي */
            align-items: center;     /* توسيط عمودي */
            min-height: 100vh;       /* ضمان أن الجسم يأخذ ارتفاع الشاشة بالكامل */
            overflow: auto;          /* للسماح بالتمرير إذا كان المحتوى أكبر من الشاشة */
        }

        /* تنسيقات حاوية النموذج */
        .form-container {
            background-color: #fff;
            width: 90%; /* نسبة مئوية ليتكيف مع الشاشات */
            max-width: 500px; /* أقصى عرض للحفاظ على التصميم */
            padding: 20px;
            border-radius: 12px; /* زوايا دائرية أكثر */
            box-shadow: 0 4px 20px rgba(0,0,0,0.1); /* ظل أكبر وأكثر وضوحًا */
            text-align: center; /* هذا يؤثر على محاذاة المحتوى داخل الحاوية بشكل عام */
            /* تم إزالة margin-top: 30px; للسماح بالتوسيط العمودي */
            margin: 20px auto; /* إضافة هامش علوي وسفلي مع توسيط أفقي تلقائي */
        }

        /* استجابة الشاشات الصغيرة */
        @media (max-width: 600px) {
            .form-container {
                width: 95%; /* زيادة العرض على الشاشات الصغيرة جدًا */
                max-width: 320px;
                padding: 15px;
                margin: 15px auto; /* هامش أقل على الشاشات الصغيرة */
            }
            .form-header {
                font-size: 20px;
                margin-bottom: 15px;
            }
        }

        /* رأس النموذج */
        .form-header {
            font-size: 28px; /* حجم أكبر للعنوان */
            font-weight: bold;
            color: #333;
            margin-bottom: 25px; /* مسافة أكبر أسفل العنوان */
        }

        /* أزرار التبديل (إن وجدت - لم يتم استخدامها في هذا النموذج) */
        .toggle-buttons a { flex: 1; }
        .toggle-buttons a button { width: 100%; }
        .toggle-buttons button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }

        /* أيقونات الأزرار (إن وجدت) */
        .btn-google i { color: #db4437; }
        .btn-apple i { color: #000; }

        /* حقول الإدخال */
        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 12px; /* زيادة حجم الحشو */
            margin-bottom: 15px; /* زيادة المسافة بين الحقول */
            border: 1px solid #ddd; /* لون حدود أفتح */
            border-radius: 8px; /* زوايا دائرية أكثر */
            font-size: 16px;
            /* تم إزالة text-align: left; هنا للسماح للمتصفح بتحديد المحاذاة بناءً على اتجاه الصفحة (dir) */
            transition: border-color 0.3s ease, box-shadow 0.3s ease; /* إضافة انتقال سلس */
        }
        .form-container input[type="text"]:focus,
        .form-container input[type="password"]:focus {
            border-color: #2e65d8; /* لون الحدود عند التركيز */
            box-shadow: 0 0 0 3px rgba(46, 101, 216, 0.2); /* ظل عند التركيز */
            outline: none; /* إزالة الخطوط العريضة الافتراضية */
        }

        /* تسميات الحقول */
        .form-container label {
            display: block; /* لجعل كل تسمية تأخذ سطرًا جديدًا */
            text-align: start; /* لجعل التسمية تتبع اتجاه النص (يمين لـ RTL، يسار لـ LTR) */
            font-size: 15px;
            color: #444;
            margin-bottom: 5px; /* مسافة صغيرة أسفل التسمية */
            font-weight: 500;
        }

        /* مجموعة خيارات (نسيت كلمة المرور وتذكرني) */
        .options-group {
            width: 100%;
            margin-bottom: 20px; /* مسافة أسفل المجموعة */
            text-align: start; /* لجعل المحتوى داخل المجموعة يتبع اتجاه النص */
        }

        /* رابط إعادة تعيين كلمة السر */
        .forgot-password {
            margin-bottom: 10px; /* مسافة أسفل الرابط وقبل مربع التذكر */
        }
        .forgot-password a {
            font-size: 14px;
            color: #2e65d8;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .forgot-password a:hover {
            color: #1a4ab9; /* لون أغمق عند التحويم */
            text-decoration: underline;
        }

        /* تنسيقات مربع "تذكرني" */
        .remember-me {
            display: flex; /* لترتيب مربع الاختيار والتسمية في سطر واحد */
            align-items: center; /* لمحاذاة العناصر عمودياً في المنتصف */
            /* لا حاجة لـ justify-content هنا لأن العنصر الأب options-group سيتولى المحاذاة */
        }

        .remember-me input[type="checkbox"] {
            /* إزالة أي تنسيقات افتراضية قد تتعارض */
            margin: 0;
            /* ضبط الهامش ليتناسب مع اتجاه اللغة */
            margin-inline-end: 8px; /* هامش بعد مربع الاختيار */
            width: 16px;
            height: 16px;
            cursor: pointer;
            vertical-align: middle; /* لمحاذاة أفضل مع النص */
        }

        .remember-me label {
            margin-bottom: 0; /* إزالة الهامش السفلي من تسمية الـ checkbox */
            font-size: 14px;
            color: #555;
            cursor: pointer;
            text-align: start; /* لضمان أن التسمية تتبع اتجاه النص */
            display: inline; /* لجعل التسمية بجانب مربع الاختيار */
        }


        /* زر تسجيل الدخول */
        .login-btn {
            width: 100%;
            padding: 14px; /* زيادة حجم الحشو */
            font-size: 18px; /* حجم خط أكبر */
            background-color: #2e65d8;
            color: #fff;
            border: none;
            border-radius: 8px; /* زوايا دائرية أكثر */
            cursor: pointer;
            margin-bottom: 20px; /* مسافة أكبر */
            transition: background-color 0.3s ease, transform 0.2s ease; /* انتقال سلس */
            font-weight: 600; /* خط سميك */
        }
        .login-btn:hover {
            background-color: #1a4ab9; /* لون أغمق عند التحويم */
            transform: translateY(-2px); /* تأثير رفع بسيط */
        }
        .login-btn:active {
            transform: translateY(0); /* إعادة الزر لمكانه عند الضغط */
        }

        /* قسم التسجيل */
        .signup {
            font-size: 14px;
            color: #555;
        }
        .signup a {
            color: #2e65d8;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .signup a:hover {
            color: #1a4ab9;
            text-decoration: underline;
        }
    </style>
</head>

<body>
<div dir="ltr" class="text-right">
    @include('partials.navbar')
</div>

    <div class="min-h-screen flex items-center justify-center">
<div class="form-container">
    <div class="form-header">{{ trans_db('login.title') }}</div>

    <form method="POST" action="{{ 'login' }}">
        @csrf
        {{-- تسمية حقل البريد الإلكتروني --}}
        <label for="email">{{ trans_db('login.email.or.phone.label') }}:</label>
        <input type="text" id="email" name="email-or-phone" placeholder="{{ trans_db('login.email.placeholder') }}" value="{{ old('email-or-phone') }}" class="block w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('email-or-phone')
        <div class="text-red-500 text-sm mt-1 text-center">{{ $message }}</div>
        @enderror

        {{-- تسمية حقل كلمة المرور --}}
        <label for="password">{{ trans_db('login.password.label') }}:</label>
        <input type="password" id="password" name="password" placeholder="{{ trans_db('login.password.placeholder') }}" class="block w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('password')
        <div class="text-red-500 text-sm mt-1 text-center">{{ $message }}</div>
        @enderror

        <div class="options-group">
            {{-- رابط إعادة تعيين كلمة السر --}}
            <div class="forgot-password">
                <a href="{{ route('password.request') }}">{{ trans_db('login.forget.password') }}</a>
            </div>

            {{-- مربع اختيار "تذكرني" --}}
            <div class="remember-me">
                <input type="checkbox" id="remember_me" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <label for="remember_me" class="ml-2 text-sm text-gray-600">{{ trans_db('login.remember.me') }}</label>
            </div>
        </div>


        <button class="login-btn">{{ trans_db('login.btn.login') }}</button>
        <div class="signup">
            {{ trans_db('login.href.question') }} <a href="{{ route('register') }}">{{ trans_db('login.href.create.account') }}</a>
        </div>
    </form>
</div>
    </div>

</body>
</html>
