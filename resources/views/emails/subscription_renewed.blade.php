@component('mail::message')
    # تم تجديد اشتراكك 🎉

    تم تجديد اشتراكك في باقة **{{ $plan->name }}** بنجاح.
    - تاريخ البداية: {{ $subscription->start_date->format('Y-m-d') }}
    - تاريخ النهاية: {{ $subscription->end_date->format('Y-m-d') }}
    - الرصيد المضاف: {{ $plan->credit_amount }}

    @component('mail::button', ['url' => url('wallet')])
        اذهب إلى حسابك
    @endcomponent

    شكرًا لك،
    {{ config('app.name') }}
@endcomponent
