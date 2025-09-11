<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رفض طلب ترقية الباقة</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px;">

<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

    <!-- Header -->
    <div style="background: #dc3545; color: #fff; padding: 16px; text-align: center;">
        <h2 style="margin: 0; font-size: 20px;">رفض طلب ترقية الباقة</h2>
    </div>

    <!-- Body -->
    <div style="padding: 24px; color: #333; line-height: 1.6;">
        <p style="margin: 0 0 16px;">مرحبًا <strong>{{ $planUpgradeRequest->user->name }}</strong>،</p>

        <p style="margin: 0 0 16px;">
            نود إبلاغك بأنه تم <span style="color: #dc3545; font-weight: bold;">رفض</span> طلب ترقية الباقة الخاص بك.
        </p>

        <div style="background: #f8d7da; color: #842029; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
            <strong>السبب:</strong> {{ $planUpgradeRequest->rejected_reason }}
        </div>

        <p style="margin: 0 0 12px;">
            إذا كان لديك أي استفسار، لا تتردد في التواصل معنا عبر البريد الإلكتروني أو من خلال أرقام التليفونات.
        </p>
    </div>

    <!-- Footer -->
    <div style="background: #f1f1f1; text-align: center; padding: 12px; font-size: 13px; color: #777;">
        © {{ date('Y') }} pepasafe. جميع الحقوق محفوظة.
    </div>
</div>

</body>
</html>
