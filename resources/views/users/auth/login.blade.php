<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>صفحة تسجيل الدخول</title>

    <!-- تحميل Tailwind مرة واحدة -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome للأيقونات -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>

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
            margin-top: 30px; text-align: center;
        }
        @media (max-width: 600px) {
            .form-container {
                width: 90%; max-width: 320px; padding: 15px; margin-top: 20px;
            }
            .form-header { font-size: 20px; margin-bottom: 15px; }
        }
        .form-header {
            font-size: 24px; font-weight: bold; color: #333;
            margin-bottom: 20px;
        }
        .toggle-buttons {
            display: flex; gap: 10px; margin-bottom: 20px; justify-content: center;
        }
        .toggle-buttons a { flex: 1; }
        .toggle-buttons a button { width: 100%; }
        .toggle-buttons button {
            padding: 10px; font-size: 16px;
            border: 1px solid #ccc; border-radius: 4px; cursor: pointer;
        }
        .user-btn { background-color: #2e65d8; color: #fff; border: none; }
        .advanced-btn { background-color: #fff; color: #333; }

        .btn-google, .btn-apple {
            width: 100%; padding: 12px; margin-bottom: 15px;
            border-radius: 4px; font-size: 16px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            background-color: #fff; border: 1px solid #ccc; color: #333;
            cursor: pointer;
        }
        .btn-google i { color: #db4437; }
        .btn-apple i { color: #000; }

        .or {
            display: flex; align-items: center; margin: 15px 0;
            color: #666; font-size: 14px;
        }
        .or::before, .or::after {
            content: ''; flex: 1; height: 1px; background: #ddd; margin: 0 10px;
        }

        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%; padding: 10px; margin-bottom: 5px;
            border: 1px solid #ccc; border-radius: 4px;
            font-size: 16px; text-align: left;
        }

        /* رابط إعادة تعيين كلمة السر */
        .forgot-password {
            text-align: right;
            margin-bottom: 15px;
        }
        .forgot-password a {
            font-size: 14px; color: #2e65d8;
            text-decoration: none;
        }
        .login-btn {
            width: 100%; padding: 12px; font-size: 16px;
            background-color: #2e65d8; color: #fff; border: none;
            border-radius: 4px; cursor: pointer; margin-bottom: 15px;
        }
        .signup { font-size: 14px; color: #555; }
        .signup a { color: #2e65d8; text-decoration: none; }
    </style>
</head>
<body>


<div class="form-container">
    <div class="form-header">تسجيل الدخول</div>
    <div class="toggle-buttons">
        <a href="{{ route('login') }}"><button class="user-btn">مستخدم</button></a>
        <a href="{{ route('register') }}"><button class="advanced-btn">منظم</button></a>
    </div>

    <a href="{{ route('auth.google') }}" class="btn-google">
        <i class="fab fa-google"></i> تسجيل الدخول بواسطة جوجل
    </a>
    <button class="btn-apple">
        <i class="fab fa-apple"></i> تسجيل الدخول بواسطة Apple
    </button>

    <div class="or">أو</div>
    <form method="POST" action="{{ 'login' }}">
        @csrf
        <input type="text" name="email" placeholder="البريد الإلكتروني أو الهاتف" value="{{ old('email') }}" class="block w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('email')
        <div class="text-red-500 text-sm mt-1 text-center">{{ $message }}</div>
        @enderror

        <input type="password" name="password" placeholder="كلمة المرور" class="block w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('password')
        <div class="text-red-500 text-sm mt-1 text-center">{{ $message }}</div>
        @enderror

        <div class="forgot-password">
            <a href="{{ route('password.request') }}">هل نسيت كلمة السر؟</a>
        </div>

        <button class="login-btn">تسجيل الدخول</button>
        <div class="signup">
            ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a>
        </div>
    </form>
</div>

<div id="footer" class="w-full"></div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        // تحميل Navbar
        const nav = document.getElementById('navbar-container');
        try {
            const res = await fetch('navbar.html');
            if (!res.ok) throw new Error(`Status ${res.status}`);
            nav.innerHTML = await res.text();
            const btnOpen  = nav.querySelector('#mobile-menu-btn');
            const btnClose = nav.querySelector('#mobile-menu-close');
            const menu     = nav.querySelector('#mobile-menu');
            btnOpen.addEventListener('click',  () => menu.classList.remove('translate-x-full'));
            btnClose.addEventListener('click', () => menu.classList.add('translate-x-full'));
        } catch (err) {
            nav.innerHTML = `<p class="text-red-500 p-4 text-center">تعذّر تحميل Navbar: ${err.message}</p>`;
        }

        // تحميل Footer
        const footer = document.getElementById('footer');
        try {
            const resF = await fetch('footer.html');
            if (!resF.ok) throw new Error(`Status ${resF.status}`);
            footer.innerHTML = await resF.text();
        } catch (err) {
            footer.innerHTML = `<p class="text-red-500 p-4 text-center">تعذّر تحميل Footer: ${err.message}</p>`;
            console.error('Footer load error:', err);
        }
    });
</script>
</body>
</html>
