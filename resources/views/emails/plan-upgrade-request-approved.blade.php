<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تمت الموافقة على طلب ترقية الباقة</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px;">

<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

    <!-- Header -->
    <div style="background: #28a745; color: #fff; padding: 16px; text-align: center;">
        <h2 style="margin: 0; font-size: 20px;">تمت الموافقة على طلب ترقية الباقة</h2>
    </div>

    <!-- Body -->
    <div style="padding: 24px; color: #333; line-height: 1.6;">
        <p style="margin: 0 0 16px;">مرحبًا <strong>{{ $planUpgradeRequest->user->name }}</strong>،</p>

        <p style="margin: 0 0 16px;">
            نود إبلاغك بأنه تمت <span style="color: #28a745; font-weight: bold;">الموافقة</span> على طلب ترقية الباقة الخاص بك،
            وقد تم تحديث حسابك إلى الباقة الجديدة بنجاح.
        </p>

        <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; border: 1px solid #c3e6cb;">
            <div style="margin-bottom:6px;"><strong>تفاصيل الباقة:</strong></div>
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span style="font-weight:600;">الباقة الجديدة:</span>
                <span>{{ $planUpgradeRequest->plan->name ?? '—' }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span style="font-weight:600;">الرصيد المتاح في باقتك:</span>
                <span>{{ $planUpgradeRequest->subscription->remaining ?? 0 }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span style="font-weight:600;">تاريخ التفعيل:</span>
                <span>{{ $planUpgradeRequest->approved_at ?? now()->format('Y-m-d') }}</span>
            </div>
        </div>

        <p style="margin: 0 0 12px;">
            يمكنك الآن التمتع بمزايا باقتك الجديدة من خلال لوحة التحكم الخاصة بك. إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.
        </p>
    </div>

    <!-- Footer -->
    <div style="background: #f1f1f1; text-align: center; padding: 12px; font-size: 13px; color: #777;">
        © {{ date('Y') }} pepasafe. جميع الحقوق محفوظة.
    </div>
</div>

</body>
</html>
