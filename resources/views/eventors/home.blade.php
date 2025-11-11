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
@if(auth()->check() && auth()->user()->hasAnyRole(['eventor', 'super admin', 'employee']))
    <div dir="rtl" class="text-left">
        @include('partials.search-bar')
    </div>
@endif
<div class="flex flex-wrap items-center justify-start gap-2">
    {{-- الزر الأول (Sort Button) --}}
    <button id="sort-button" data-sort-order="desc"
        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold
               py-1 px-2 sm:py-2 sm:px-4
               rounded-lg transition-colors duration-200
               inline-block w-auto text-center order-2 sm:order-1">
        <i class="bi bi-sort-up ltr:mr-2 rtl:ml-2"></i>
        <span>{{ trans_db('event.sort.newest') }}</span>
    </button>

    {{-- الروابط الأخرى (Conditional Links) --}}
    @if($events->isNotEmpty())
        {{-- الرابط الثاني (Your Docs) --}}
        <a href="{{ route('home.users') }}"
           class="bg-gray-600 hover:bg-green-700 text-white font-semibold
                  py-1 px-2 sm:py-2 sm:px-4
                  rounded-lg transition-colors duration-200
                  inline-block w-auto text-center order-1 sm:order-2">
            {{ trans_db('event.your.docs') }}
        </a>

        {{-- الرابط الثالث (Create Event) --}}
        <a href="{{ route('create-event') }}"
           class="bg-green-600 hover:bg-green-700 text-white font-semibold
                  py-1 px-2 sm:py-2 sm:px-4
                  rounded-lg transition-colors duration-200
                  inline-block w-auto text-center order-3">
            + {{ trans_db('home.event.create.event') }}
        </a>
    @endif
</div>





{{-- قسم الانتظار--}}
@if(auth()->check() && auth()->user()->hasRole('eventor') && $user->paymentReceipts->where('status', 'pending')->isNotEmpty())
    @php
        // تحديد ما إذا كانت اللغة الحالية هي العربية (RTL)
        $isRtl = (app()->getLocale() === 'ar');
        $dirClass = $isRtl ? 'rtl' : 'ltr';
        // ضبط فئة المحاذاة حسب الاتجاه. text-center يعمل جيدًا هنا.
        $textAlignClass = $isRtl ? 'text-right' : 'text-left';

        // افتراض أن موديل PhoneNumber موجود
        $phones = \App\Models\PhoneNumber::pluck('phone_number')->toArray();
    @endphp

    <section class="w-full bg-[#F9FAFB] py-6 sm:py-8" dir="{{ $dirClass }}">
        <div class="max-w-xl mx-auto text-center px-4 sm:px-6">
            <h2 class="text-3xl sm:text-5xl font-bold text-black">
                {{ trans_db('approval.pending_title') }}
            </h2>
            <p class="mt-4 text-gray-700 leading-relaxed text-sm sm:text-base">
                {{ trans_db('approval.pending_message') }}
            </p>

            <div class="mt-8 flex justify-center items-center">
                {{-- الأيقونة الدوارة (Spinning Icon) --}}
                <div
                    class="animate-spin ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 border-t-blue-500"></div>
                {{-- ضبط margin-left/margin-right حسب الاتجاه --}}
                <p class="{{ $isRtl ? 'me-4' : 'ml-4' }} text-lg font-medium text-blue-600">
                    {{ trans_db('approval.reviewing_request') }}
                </p>
            </div>

            @if(count($phones))
                <p class="mt-6 text-gray-700 leading-relaxed text-sm sm:text-base">
                    {{ trans_db('contact.call_us_prefix') }}
                    <br>
                    @foreach ($phones as $index => $phone)
                        <span class="font-semibold text-blue-600">{{ $phone }}</span>
                        @if ($index < count($phones) - 1)
                            {{ trans_db('contact.or') }}
                        @endif
                    @endforeach
                </p>
            @endif
        </div>
    </section>
@endif



{{--قسم الرفض--}}
@if(auth()->check() && auth()->user()->hasRole('eventor') && $user->paymentReceipts->where('status', 'rejected')->isNotEmpty())
    @php
        // تحديد ما إذا كانت اللغة الحالية هي العربية (RTL)
        $isRtl = (app()->getLocale() === 'ar');
        $dirClass = $isRtl ? 'rtl' : 'ltr';
        // لتحديد اتجاه النص داخل عنصر flex
        $marginClass = $isRtl ? 'me-4' : 'ml-4';

        // افتراض أن موديل PhoneNumber موجود
        $phones = \App\Models\PhoneNumber::pluck('phone_number')->toArray();
    @endphp

    <section class="w-full bg-[#F9FAFB] py-6 sm:py-8" dir="{{ $dirClass }}">
        <div class="max-w-xl mx-auto text-center px-4 sm:px-6">
            <h2 class="text-3xl sm:text-5xl font-bold text-black">
                {{ trans_db('approval.rejected_title') }}
            </h2>
            <p class="mt-4 text-gray-700 leading-relaxed text-sm sm:text-base">
                {{ trans_db('approval.rejected_message') }}
            </p>

            <div class="mt-8 flex justify-center items-center">
                {{-- أيقونة الرفض (X داخل دائرة) --}}
                <svg class="h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{-- ضبط margin-left/margin-right حسب الاتجاه --}}
                <p class="{{ $marginClass }} text-lg font-medium text-red-600">
                    {{ trans_db('approval.rejection_status') }}
                </p>
            </div>

            @if(count($phones))
                <p class="mt-6 text-gray-700 leading-relaxed text-sm sm:text-base">
                    {{ trans_db('contact.inquiry_prefix') }}
                    <br>
                    @foreach ($phones as $index => $phone)
                        <span class="font-semibold text-blue-600">{{ $phone }}</span>
                        @if ($index < count($phones) - 1)
                            {{ trans_db('contact.or') }}
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
    @php
        // تحديد ما إذا كانت اللغة الحالية هي العربية (RTL)
        $isRtl = (app()->getLocale() === 'ar');
        $dirClass = $isRtl ? 'rtl' : 'ltr';
        // ضبط الهامش لجانب الأيقونة داخل الزر
        $iconMarginClass = $isRtl ? 'me-2' : 'ml-2';
    @endphp

    <section class="w-full bg-[#F9FAFB] py-6 sm:py-8" dir="{{ $dirClass }}">
        <div class="max-w-xl mx-auto text-center px-4 sm:px-6">
            <h2 class="text-3xl sm:text-5xl font-bold text-black">
                {{ trans_db('cta.create_event_title') }}
            </h2>
            <p class="mt-4 text-gray-700 leading-relaxed text-sm sm:text-base">
                {{ trans_db('cta.create_event_description') }}
            </p>



{{--                                        الاحداث في حالة ان المنظم عنده احداث --}}

        {{--                   section search for event --}}
    @if($events)
{{--        <div class="flex flex-wrap gap-4 justify-start">--}}
<div class="flex flex-wrap" id="events-container">
    @foreach($events as $event)
            <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 p-2 event-item" data-date="{{ \Carbon\Carbon::parse($event->start_date)->timestamp }}">
        <a href="#">
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
    <div class="inline-block p-1 rounded transition relative border
        {{ $event->visible_on_profile ? 'border-gray-300 hover:border-blue-600' : 'border-red-400 bg-red-50' }}">
        <form method="POST" action="{{ route('events.toggleVisibility', $event->slug) }}">
            @csrf
            @method('PATCH')
            <button type="submit" 
                    class="{{ $event->visible_on_profile ? 'text-blue-600 hover:text-blue-800' : 'text-red-500 hover:text-red-700' }}">
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


    <script>
        window.i18n = {
            event_sort_older: "{{ trans_db('event.sort.older') }}",
            event_sort_newest: "{{ trans_db('event.sort.newest') }}",
        };
    </script>


    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sortButton = document.getElementById('sort-button');
            const container = document.getElementById('events-container');

            if (!sortButton || !container) return; // تحقق من وجود العناصر

            sortButton.addEventListener('click', () => {
                // الحصول على جميع عناصر الأحداث
                const eventItems = Array.from(container.querySelectorAll('.event-item'));

                // الحصول على ترتيب الفرز الحالي والتبديل إليه
                let currentSortOrder = sortButton.getAttribute('data-sort-order') || 'desc';
                const newSortOrder = currentSortOrder === 'desc' ? 'asc' : 'desc';

                // دالة الترتيب (Sort Function)
                eventItems.sort((a, b) => {
                    // استخراج التواريخ (كأرقام Timestamp) من خاصية data-date
                    const dateA = parseInt(a.getAttribute('data-date'));
                    const dateB = parseInt(b.getAttribute('data-date'));

                    if (newSortOrder === 'asc') {
                        // تصاعدي: الأقدم أولاً (ASC)
                        return dateA - dateB;
                    } else {
                        // تنازلي: الأحدث أولاً (DESC)
                        return dateB - dateA;
                    }
                });

                // إعادة ترتيب العناصر في DOM
                eventItems.forEach(item => {
                    container.appendChild(item);
                });

                // تحديث حالة الزرار
                sortButton.setAttribute('data-sort-order', newSortOrder);
                updateButtonText(newSortOrder);
            });

            /**
             * تحديث نص وأيقونة الزر بناءً على حالة الترتيب الجديدة
             */
            function updateButtonText(order) {
                const icon = sortButton.querySelector('i');
                const textSpan = sortButton.querySelector('span');

                if (order === 'asc') {
                    // الترتيب الآن هو الأقدم أولاً، النص التالي يجب أن يكون 'الأحدث أولاً'
                    icon.classList.remove('bi-sort-down');
                    icon.classList.add('bi-sort-up');
                    textSpan.textContent = window.i18n.event_sort_newest;
                } else {
                    // الترتيب الآن هو الأحدث أولاً، النص التالي يجب أن يكون 'الأقدم أولاً'
                    icon.classList.remove('bi-sort-up');
                    icon.classList.add('bi-sort-down');
                    textSpan.textContent = window.i18n.event_sort_older;
                }
            }

            // لضمان عرض النص الصحيح عند التحميل الأولي
            updateButtonText('desc'); // نبدأ بالافتراضي وهو 'desc' (الأحدث أولاً)
        });
    </script> --}}

<script>
    window.i18n = {
        event_sort_older: "{{ trans_db('event.sort.older') }}",
        event_sort_newest: "{{ trans_db('event.sort.newest') }}",
    };

    document.addEventListener('DOMContentLoaded', () => {
        const sortButton = document.getElementById('sort-button');

        if (!sortButton) return;

        // 1. قراءة حالة الفرز الحالية من الـ URL عند تحميل الصفحة
        const urlParams = new URLSearchParams(window.location.search);
        // نستخدم 'sort' كمعامل في الـ URL. القيمة الافتراضية هي 'desc' (الأحدث أولاً).
        let currentSortOrder = urlParams.get('sort') || 'desc'; 
        
        // **2. دالة تحديث نص الزر**
        function updateButtonText(order) {
            const icon = sortButton.querySelector('i');
            const textSpan = sortButton.querySelector('span');

            // الترتيب الذي سيتم تفعيله عند الضغطة التالية
            const nextOrder = order === 'desc' ? 'asc' : 'desc';

            if (nextOrder === 'asc') {
                // الترتيب التالي سيكون الأقدم أولاً.
                icon.classList.remove('bi-sort-up');
                icon.classList.add('bi-sort-down'); // الأيقونة تشير إلى الترتيب التالي: 'الأقدم أولاً'
                textSpan.textContent = window.i18n.event_sort_older; 
            } else {
                // الترتيب التالي سيكون الأحدث أولاً.
                icon.classList.remove('bi-sort-down');
                icon.classList.add('bi-sort-up'); // الأيقونة تشير إلى الترتيب التالي: 'الأحدث أولاً'
                textSpan.textContent = window.i18n.event_sort_newest;
            }
        }

        // تطبيق حالة الزر بناءً على الـ URL
        updateButtonText(currentSortOrder === 'desc' ? 'asc' : 'desc'); // نعكس القيمة لقراءة ما هو متوقع

        // **3. وظيفة معالج الضغط على الزرار (المهم)**
        sortButton.addEventListener('click', () => {
            // الترتيب الجديد هو عكس الترتيب الحالي المقروء من الـ URL
            const newSortOrder = currentSortOrder === 'desc' ? 'asc' : 'desc';

            // بناء الرابط الجديد
            const url = new URL(window.location.href);
            url.searchParams.set('sort', newSortOrder); // وضع معامل الفرز الجديد
            url.searchParams.set('page', 1); // **العودة للصفحة الأولى دائماً عند تغيير الفرز**

            // إعادة توجيه المستخدم لكي يقوم الخادم بفرز البيانات كاملة
            window.location.href = url.toString();
        });
    });
</script>

@include('partials.footer')

</body>
</html>
