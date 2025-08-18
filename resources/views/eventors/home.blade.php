@php use App\Models\PhoneNumber;
    use App\Models\Setting;
 @endphp
    <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>بحث وعرض النتائج وإنشاء الأحداث</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2DyQNf2M8ZHuKDl2Usk0Sz3KZUkcRvV0VxMjWptF7C0F7CXvN3ocST4z+jr9eKmw=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href=" {{ asset('css/eventor-home.css') }} ">
</head>
<body class="space-y-12 bg-white">

<div dir="ltr" class="text-right">
@include('partials.auth-navbar')

    @include('components.cookie-banner')

</div>

@if(session('status') === 'event-visibility-toggled')
    <div id="flash-message" class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300">
        <strong class="font-bold">تم بنجاح!</strong>
        <span class="ml-2">تم تحديث حالة ظهور الحدث في الملف الشخصي.</span>
    </div>

    <script>
        setTimeout(() => {
            const msg = document.getElementById('flash-message');
            if (msg) {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 300); // يُزال من الـ DOM بعد الإخفاء
            }
        }, 3000); // تختفي بعد 3 ثوانٍ
    </script>
@endif

@if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-2 rounded mb-4">
        {{ session('error') }}
    </div>
@endif



<!-- قسم البحث -->
@if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']) && $user->paymentReceipts->where('status', 'approved')->isNotEmpty())
    <div dir="rtl" class="text-left">
        @include('partials.search-bar')
    </div>
@endif

@if($events->isNotEmpty())
<a href="{{ route('create-event') }}"
   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 ms-40">
   + {{ trans_db('home.event.create.event') }}
</a>
@endif



{{-- قسم الانتظار--}}
@if(auth()->check() && auth()->user()->hasRole('eventor') && $user->paymentReceipts->where('status', 'pending')->isNotEmpty())
    <section class="w-full bg-[#F9FAFB] py-6 sm:py-8">
        <div class="max-w-xl mx-auto text-center px-4 sm:px-6">
            <h2 class="text-3xl sm:text-5xl font-bold text-black">
                في انتظار الموافقة على طلبك
            </h2>
            <p class="mt-4 text-gray-700 leading-relaxed text-sm sm:text-base">
                طلبك قيد المراجعة حاليًا من قبل فريقنا. سنقوم بإبلاغك فور الموافقة عليه. نشكرك على صبرك وتفهمك.
            </p>

            <div class="mt-8 flex justify-center items-center">
                <div
                    class="animate-spin ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 border-t-blue-500"></div>
                <p class="ml-4 text-lg font-medium text-blue-600">جارٍ مراجعة طلبك...</p>
            </div>

            @php
                $phones = PhoneNumber::pluck('phone_number')->toArray();
            @endphp

            @if(count($phones))
                <p class="mt-6 text-gray-700 leading-relaxed text-sm sm:text-base">
                    لتواصل معنا، يرجى الاتصال على الأرقام التالية:
                    <br>
                    @foreach ($phones as $index => $phone)
                        <span class="font-semibold text-blue-600">{{ $phone }}</span>
                        @if ($index < count($phones) - 1)
                            أو
                        @endif
                    @endforeach
                </p>
            @endif


        </div>
    </section>
@endif



{{--قسم الرفض--}}
@if(auth()->check() && auth()->user()->hasRole('eventor') && $user->paymentReceipts->where('status', 'rejected')->isNotEmpty())
    <section class="w-full bg-[#F9FAFB] py-6 sm:py-8">
        <div class="max-w-xl mx-auto text-center px-4 sm:px-6">
            <h2 class="text-3xl sm:text-5xl font-bold text-black">
                عفواً، تم رفض طلبك
            </h2>
            <p class="mt-4 text-gray-700 leading-relaxed text-sm sm:text-base">
                نعتذر لإبلاغك بأنه تم رفض طلبك بعد مراجعته. يُرجى التواصل معنا للمزيد من التفاصيل
                حول أسباب الرفض وكيفية إعادة التقديم.
            </p>

            <div class="mt-8 flex justify-center items-center">
                <svg class="h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-4 text-lg font-medium text-red-600">للأسف، لم يتم قبول طلبك.</p>
            </div>

            @php
                $phones = PhoneNumber::pluck('phone_number')->toArray();
            @endphp

            @if(count($phones))
                <p class="mt-6 text-gray-700 leading-relaxed text-sm sm:text-base">
                    إذا كان لديك أي استفسار، يرجى التواصل معنا على الأرقام التالية:
                    <br>
                    @foreach ($phones as $index => $phone)
                        <span class="font-semibold text-blue-600">{{ $phone }}</span>
                        @if ($index < count($phones) - 1)
                            أو
                        @endif
                    @endforeach
                </p>
            @endif

        </div>
    </section>
@endif



<!-- القسم الثاني: إنشاء الحدث  حالة الموافقة -->
{{--@if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']) || $user->paymentReceipts->where('status', 'approved')->isNotEmpty())--}}
@if(
    $events->isEmpty()
    &&
    (
    (auth()->check() && auth()->user()->hasRole('eventor') && $user->paymentReceipts->where('status', 'approved')->isNotEmpty())
    ||
    (auth()->check() && auth()->user()->hasRole('super admin'))
    )
)
<section class="w-full bg-[#F9FAFB] py-6 sm:py-8">
        <div class="max-w-xl mx-auto text-center px-4 sm:px-6">
            <h2 class="text-3xl sm:text-5xl font-bold text-black">
                أنشئ حدثك الأول الآن
            </h2>
            <p class="mt-4 text-gray-700 leading-relaxed text-sm sm:text-base">
                استعد لمشاركة أفكارك وأهدافك مع جمهورك! في هذا القسم، يمكنك بسهولة
                تحديد عنوان الحدث وإضافة وصف تفصيلي يغطي كافة التفاصيل، مثل الزمان
                والمكان والموضوع. بمجرد استكمال البيانات، سيظهر الحدث جاهزًا للنشر
                والمشاركة مع الجميع في دقائق.
            </p>

            <a
                href="{{ route('create-event') }}"
                class="mt-6 w-full sm:w-auto bg-blue-600 text-white rounded-lg px-6 py-3 hover:bg-blue-700 flex items-center justify-center mx-auto"
            >

                <i class="bi bi-plus-lg ml-2"></i>
                إنشاء الحدث الأول الآن
            </a>
            @endif
        </div>
</section>


{{--                                        الاحداث في حالة ان المنظم عنده احداث --}}

        {{--                   section search for event --}}
    @if($events)
{{--        <div class="flex flex-wrap gap-4 justify-start">--}}
<div class="flex flex-wrap">
    @foreach($events as $event)
            <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 p-2">
        <a href="#">
{{--            <section class="max-w-xs bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105--}}
{{--                        mr-4 ml-auto px-4 sm:px-0">--}}
            <section class="h-full flex flex-col justify-between bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105">


            <div class="p-3 space-y-2 rtl:text-right ltr:text-left">
                <h2 class="text-xl font-bold mb-3 min-h-[56px]" dir="auto">
                {{ trans_db('search.event.title') }}: {{ $event->title }}
                    </h2>

                <div class="flex items-center text-gray-600 text-sm justify-start">
                        <i class="bi bi-calendar-check-fill text-lg text-blue-600"></i>
                        <span class="font-medium ltr:ml-2 rtl:mr-2">
                        {{ trans_db('search.event.start.date') }}: {{ $event->start_date }}
                    </span>
                    </div>

                    <div class="flex items-center text-gray-600 text-sm justify-start">
                        <i class="bi bi-send-fill text-lg text-gray-500"></i>
                        <span class="font-medium ltr:ml-2 rtl:mr-2">
                        {{ trans_db('search.event.end.date') }}: {{ $event->end_date }}
                    </span>
                    </div>

                    <div class="flex items-center text-gray-600 text-sm justify-start">
                        <i class="bi bi-file-earmark-fill text-lg text-gray-500"></i>
                        <span class="font-medium ltr:ml-2 rtl:mr-2">
                        {{ trans_db('search.event.count.template') }}: {{ $templateCount }}
                    </span>
                    </div>

                    <div class="flex items-center text-gray-600 text-sm mb-4 justify-start">
                        <i class="bi bi-people-fill text-lg text-gray-500"></i>
                        <span class="font-medium ltr:ml-2 rtl:mr-2">
                        {{ trans_db('search.event.count.participants') }}: {{ $recipientCount }}
                    </span>
                    </div>

                @if (Setting::getValue('show_events_in_profile') === '1')
                <div class="inline-block border border-gray-300 p-1 rounded hover:border-blue-600 transition relative">
                    <form method="POST" action="{{ route('events.toggleVisibility', $event->slug) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-gray-500 hover:text-blue-600">
                            <i class="bi {{ $event->visible_on_profile ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>
                        </button>
                    </form>
                </div>
                @endif

                    @if(auth()->check() && auth()->user()->hasAnyPermission(['manage events', 'full access', 'full access to events']))
                        <div class="flex justify-center items-center mt-4">
                            <a href="{{ route('showEvent', $event->slug) }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-semibold">
                                {{ trans_db('search.event.manage.event') }}
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        </a>
            </div>
    @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $events->links('vendor.pagination.tailwind-custom') }}
        </div>

    @endif

@include('partials.footer')

</body>
</html>
