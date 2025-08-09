<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة التحقق من OTP</title>

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons (if needed, though not used directly here) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>

    <style>
        /* Basic box model reset */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        /* Body styling for centering content */
        body {
            background-color: #f3f9f9;
            font-family: "Inter", "Segoe UI", Tahoma, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Horizontal centering */
            align-items: center;     /* Vertical centering */
            min-height: 100vh;       /* Ensure body takes full viewport height */
            overflow: auto;          /* Allow scrolling if content overflows */
        }

        /* Form container styling */
        .otp-container {
            background-color: #fff;
            width: 90%; /* Responsive width */
            max-width: 500px; /* Max width to maintain design */
            padding: 24px; /* Increased padding */
            border-radius: 12px; /* More rounded corners */
            box-shadow: 0 6px 24px rgba(0,0,0,0.15); /* Stronger shadow */
            text-align: center;
            margin: 20px auto; /* Top/bottom margin with auto horizontal centering */
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 600px) {
            .otp-container {
                width: 95%;
                max-width: 320px;
                padding: 20px;
                margin: 15px auto;
            }
            .otp-header {
                font-size: 22px;
                margin-bottom: 20px;
            }
        }

        /* Header styling */
        .otp-header {
            font-size: 30px; /* Larger font size for title */
            font-weight: bold;
            color: #333;
            margin-bottom: 30px; /* More space below header */
        }

        /* Input field styling */
        .otp-input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px; /* Space between inputs */
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 18px; /* Larger font for OTP input */
            letter-spacing: 2px; /* Spacing for OTP digits */
            text-align: start; /* Aligns with dir attribute */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .otp-input:focus {
            border-color: #2e65d8;
            box-shadow: 0 0 0 3px rgba(46, 101, 216, 0.2);
            outline: none;
        }

        /* Label styling */
        .otp-label {
            display: block; /* Each label takes a new line */
            text-align: start; /* Follows dir attribute (right for RTL, left for LTR) */
            font-size: 16px;
            color: #444;
            margin-bottom: 8px; /* Space below label */
            font-weight: 500;
        }

        /* Submit button styling */
        .submit-btn {
            width: 100%;
            padding: 14px;
            font-size: 18px;
            background-color: #2e65d8;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px; /* Space above button */
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 600;
        }
        .submit-btn:hover {
            background-color: #1a4ab9;
            transform: translateY(-2px);
        }
        .submit-btn:active {
            transform: translateY(0);
        }

        /* Resend OTP link styling */
        .resend-otp {
            font-size: 14px;
            color: #2e65d8;
            text-decoration: none;
            margin-top: 15px;
            display: block; /* Ensure it takes its own line */
            text-align: center; /* Center the link */
        }
        .resend-otp:hover {
            color: #1a4ab9;
            text-decoration: underline;
        }
    </style>
</head>
<body>


{{-- Main OTP verification form --}}

<div class="otp-container">
    <div class="otp-header">التحقق من رمز OTP</div>

    <form method="POST" action="{{ route('verify.otp.submit') }}">
        @csrf
        {{-- Email OTP Input --}}
        <label for="email_otp" class="otp-label">رمز OTP للبريد الإلكتروني:</label>
        <input type="text" id="email_otp" name="email_otp" placeholder="أدخل رمز البريد الإلكتروني"
               class="otp-input" inputmode="numeric" pattern="[0-9]*" maxlength="6">
        @error('email_otp')
        <div class="text-red-500 text-sm mt-1 text-center">{{ $message }}</div>
        @enderror

        {{-- Phone OTP Input --}}
{{--        <label for="phone_otp" class="otp-label">رمز OTP للهاتف:</label>--}}
{{--        <input type="text" id="phone_otp" name="phone_otp" placeholder="أدخل رمز الهاتف"--}}
{{--               class="otp-input" inputmode="numeric" pattern="[0-9]*" maxlength="6">--}}
{{--        @error('phone_otp')--}}
{{--        <div class="text-red-500 text-sm mt-1 text-center">{{ $message }}</div>--}}
{{--        @enderror--}}

        <button type="submit" class="submit-btn">التحقق</button>

        <style>
            /* تنسيق زر إعادة الإرسال */
            .resend-otp {
                color: #1a73e8; /* لون أزرق جذاب */
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }
            .resend-otp:hover {
                text-decoration: underline;
            }

            /* تنسيق حالة التعطيل */
            .resend-otp.disabled {
                color: #9e9e9e; /* لون رمادي ليدل على التعطيل */
                cursor: not-allowed;
                text-decoration: none;
            }

            /* تنسيق المؤقت */
            .resend-otp .timer {
                font-weight: 600;
                margin-left: 5px;
            }
            html[dir="rtl"] .resend-otp .timer {
                margin-left: 0;
                margin-right: 5px;
            }
        </style>

        <form method="POST" action="{{ route('resend.otp') }}" id="resendOtpForm">
            @csrf
            <button type="button" id="resendOtpButton" class="resend-otp disabled" disabled>
                <span>إعادة إرسال رمز OTP؟</span>
                <span class="timer"></span>
            </button>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const resendButton = document.getElementById('resendOtpButton');
                const timerElement = resendButton.querySelector('.timer');
                const form = document.getElementById('resendOtpForm');
                let countdown = 120; // 120 ثانية = دقيقتين
                let timerInterval;

                function updateTimer() {
                    const minutes = Math.floor(countdown / 60);
                    const seconds = countdown % 60;
                    timerElement.textContent = `(${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')})`;

                    if (countdown <= 0) {
                        clearInterval(timerInterval);
                        resendButton.disabled = false;
                        resendButton.classList.remove('disabled');
                        timerElement.style.display = 'none';
                    } else {
                        countdown--;
                    }
                }

                // تشغيل المؤقت عند تحميل الصفحة
                timerInterval = setInterval(updateTimer, 1000);

                // عند الضغط على الزر
                resendButton.addEventListener('click', function () {
                    if (!resendButton.disabled) {
                        // إرسال النموذج
                        form.submit();
                        // إعادة تشغيل المؤقت بعد الإرسال
                        countdown = 120;
                        resendButton.disabled = true;
                        resendButton.classList.add('disabled');
                        timerElement.style.display = 'inline';
                        clearInterval(timerInterval);
                        timerInterval = setInterval(updateTimer, 1000);
                    }
                });
            });
        </script>

    </form>
</div>

</body>
</html>
