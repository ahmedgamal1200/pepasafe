<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.jpg') }}">
    <title>Create Account</title>

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

    {{-- <div dir="ltr" class="text-right"> --}}
        
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
    @include('partials.navbar')
</div>

<div class="flex flex-col items-center">
    <div class="form-container">
        <div class="form-header">{{ trans_db('register.title') }}</div>

        @php
            $isRTL = app()->getLocale() === 'ar';
            $textAlignment = $isRTL ? 'text-right' : 'text-left'; // المحاذاة الديناميكية
            $direction = $isRTL ? 'rtl' : 'ltr'; // الاتجاه الديناميكي
        @endphp

        <div class="text-sm font-medium {{ $textAlignment }} mb-2" dir="{{ $direction }}">
            {{ trans_db('account.type') }}
        </div>

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
                <label for="acct_user" class="user-btn label-btn">{{ trans_db('type.user') }}</label>

                <a href="{{ route('register') }}" class="advanced-btn">{{ trans_db('type.eventor') }}</a>
            </div>



            @php
                // هذا الجزء يجب أن يكون معرّفًا مرة واحدة في بداية ملف الـ Blade
                $isRTL = app()->getLocale() === 'ar';
                $textAlignment = $isRTL ? 'text-right' : 'text-left'; // محاذاة النص
                $direction = $isRTL ? 'rtl' : 'ltr'; // اتجاه العنصر
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

            <div class="w-full mb-6 {{ $textAlignment }}"
                 dir="{{ $direction }}">
                <label for="terms" class="inline-block align-middle">
                    <input type="checkbox" id="terms" name="terms_agreement" required class="w-4 h-4 ml-2" >
                    <span>{{ trans_db('i.agree') }} <a href="{{ route('about') }}#terms" class="text-blue-500 underline">{{ trans_db('terms') }}</a></span>
                </label>
            </div>

            <button type="submit" class="login-btn">{{ trans_db('create.acc') }}</button>

            <div class="signup">
                {{ trans_db('Do.you.have.an.account') }} <a href="{{ route('login') }}">{{trans_db('login')}} </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
