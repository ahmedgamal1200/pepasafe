@php use App\Models\PhoneNumber; @endphp
    <!DOCTYPE html>
<html lang="ar" dir="rtl">
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

@include('partials.auth-navbar')

<!-- قسم البحث -->
@if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']) && $user->paymentReceipts->where('status', 'approved')->isNotEmpty())
    @include('partials.search-bar')
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
                نعتذر لإبلاغك بأنه تم رفض طلبك بعد مراجعته. يُرجى مراجعة البريد الإلكتروني الخاص بك للمزيد من التفاصيل
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

            <p class="mt-6 text-gray-700 leading-relaxed text-sm sm:text-base">
                إذا كان لديك أي استفسار، يرجى التواصل معنا على الأرقام التالية:
                <br>
                <span class="font-semibold text-blue-600">01X-XXX-XXXX</span> أو
                <span class="font-semibold text-blue-600">01X-XXX-XXXX</span>
            </p>

        </div>
    </section>
@endif



<!-- القسم الثاني: إنشاء الحدث  حالة الموافقة -->
@if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin']) && $user->paymentReceipts->where('status', 'approved')->isNotEmpty())
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
</body>
</html>
