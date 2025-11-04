@php use App\Models\AttendanceDocument; @endphp
    <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.jpg') }}">
    <title>Home | pepasafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>


    <link rel="stylesheet" href=" {{ asset('css/eventor-home.css') }} ">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Basic box model reset - Tailwind's preflight handles most of this */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        /* Custom font if 'Inter' is not part of default Tailwind sans-serif stack */
        body {
            font-family: "Inter", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0; /* لون خلفية خفيف لرؤية الناف بار بوضوح */
        }
    </style>

</head>
<body class="space-y-12 bg-white">

@include('partials.auth-navbar')

<div dir="rtl" class="text-left">
    @include('partials.search-bar')
</div>

@include('components.cookie-banner')






{{--  section not found document     --}}

@if(request()->has('query') && !$document && !$event)
    <section class="w-full py-10 px-4 sm:px-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center space-y-4">
                <i class="fas fa-times-circle text-7xl text-red-500"></i>
                <p class="text-gray-600 text-lg leading-relaxed">
                  {{ trans_db('search.no.results') }}
                </p>
            </div>
        </div>
    </section>
@endif



{{--                   section search for event --}}
@if(request()->has('query') && $event)
    @php $isAr = app()->getLocale() == 'ar'; @endphp
    <a href="#">
        <section class="max-w-xs bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105
                        {{ $isAr ? 'mr-4 ml-auto' : 'ml-4 mr-auto' }} px-4 sm:px-0">

            <div class="p-3 space-y-2 {{ $isAr ? 'text-right' : 'text-left' }}">
                <h2 class="text-xl font-bold mb-3">
                    {{ trans_db('search.event.title') }}: {{ $event->title }}
                </h2>

                {{-- Start Date --}}
                <div class="flex items-center text-gray-600 text-sm {{ $isAr ? 'justify-between flex-row-reverse' : 'justify-start' }}">
                    <i class="bi bi-calendar-check-fill text-lg text-blue-600 {{ $isAr ? 'ml-2' : 'mr-2' }}"></i>
                    <span class="font-medium">
                        {{ trans_db('search.event.start.date') }}: {{ $event->start_date }}
                    </span>
                </div>

                {{-- End Date --}}
                <div class="flex items-center text-gray-600 text-sm {{ $isAr ? 'justify-between flex-row-reverse' : 'justify-start' }}">
                    <i class="bi bi-send-fill text-lg text-gray-500 {{ $isAr ? 'ml-2' : 'mr-2' }}"></i>
                    <span class="font-medium">
                        {{ trans_db('search.event.end.date') }}: {{ $event->end_date }}
                    </span>
                </div>

                {{-- Template Count --}}
                <div class="flex items-center text-gray-600 text-sm {{ $isAr ? 'justify-between flex-row-reverse' : 'justify-start' }}">
                    <i class="bi bi-file-earmark-fill text-lg text-gray-500 {{ $isAr ? 'ml-2' : 'mr-2' }}"></i>
                    <span class="font-medium">
                        {{ trans_db('search.event.count.template') }}: {{ $templateCount }}
                    </span>
                </div>

                {{-- Participants Count --}}
                <div class="flex items-center text-gray-600 text-sm mb-4 {{ $isAr ? 'justify-between flex-row-reverse' : 'justify-start' }}">
                    <i class="bi bi-people-fill text-lg text-gray-500 {{ $isAr ? 'ml-2' : 'mr-2' }}"></i>
                    <span class="font-medium">
                        {{ trans_db('search.event.count.participants') }}: {{ $recipientCount }}
                    </span>
                </div>

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
@endif






<!-- بطاقة النتيجة صغيرة على اليمين -->
@if(request()->has('query') && $document)
    @php
        // تحديد ما إذا كانت اللغة الحالية هي العربية (RTL)
        $isRtl = (app()->getLocale() === 'ar');
        $dirClass = $isRtl ? 'rtl' : 'ltr';
        // فئة المحاذاة للنصوص داخل القسم
        $textAlignStart = $isRtl ? 'text-right' : 'text-left';
        // فئة الهامش للعنوان العلوي (Mr/Ml)
        $marginStart = $isRtl ? 'ml-2' : 'mr-2';

        // **التعديل المطلوب:**
        // العربية (RTL): mx-auto lg:mr-5 lg:ml-auto (محاذاة لليمين)
        // الإنجليزية (LTR): mx-auto lg:ml-5 lg:mr-auto (محاذاة لليسار)
        $cardMargin = $isRtl ? 'mx-auto lg:mr-5 lg:ml-auto' : 'mx-auto lg:ml-5 lg:mr-auto';
    @endphp

    <div dir="{{ $dirClass }}">
        <!-- عنوان قائمة الشهادات -->
        <h2 class="text-xl font-bold {{ $textAlignStart }} mt-4 mb-2 {{ $marginStart }}">
            {{ trans_db('search.result_title') }}
        </h2><br>

        <a href="{{ route('documents.show', $document->uuid) }}">
            <section class="max-w-xs bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105
                        {{ $cardMargin }}">
                <img src="{{ asset('storage/' . $document->file_path) }}"
                     alt="{{ trans_db('document.image_alt') }}"
                     class="w-full h-auto object-contain rounded-lg shadow-md"/>

                <div class="p-3 space-y-2 {{ $textAlignStart }}">
                    <h2 class="text-lg font-semibold">
                        {{ trans_db('document.title_prefix') }}: {{ $document->template->title ?? ''}}
                    </h2>
                    <h3 class="text-gray-500 text-sm">
                        {{ trans_db('document.issue_date') }}: {{ $document->template->send_at->format('Y-m-d')}}
                    </h3>
                    <p class="text-gray-600 text-sm">
                        {{ $document->template->event->title ?? ''}}
                    </p>

                    @if(auth()->id() === $document->recipient->user_id)
                        {{-- زر تبديل الرؤية --}}
                        <div class="inline-block border border-gray-300 p-1 rounded hover:border-blue-600 transition relative">
                            <form method="POST" action="{{ route('documents.toggleVisibility', $document->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-gray-500 hover:text-blue-600" title="{{ trans_db($document->visible_on_profile ? 'document.hide_profile' : 'document.show_profile') }}">
                                    <i class="bi {{ $document->visible_on_profile ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </section>
        </a>
    </div>
@endif


@if(!request()->has('query') && $user->documents->isEmpty())
    <section class="w-full py-10 px-4 sm:px-6">
        <div class="max-w-3xl mx-auto"> {{-- تم التعديل هنا من max-w-xl إلى max-w-3xl --}}
            <div class="bg-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center space-y-4">
                <i class="fas fa-folder-open text-7xl text-gray-400"></i>
                <p class="text-gray-600 text-lg leading-relaxed">
                    {{ trans_db('banar.home.users') }}
                </p>
            </div>
        </div>
    </section>
@endif


{{-- section Doc For current user --}}

@if(session('status') === 'document-visibility-toggled')
    <div id="flash-message"
         class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300">
        <strong class="font-bold">تم بنجاح!</strong>
        <span class="ml-2">تم تحديث حالة ظهور الوثيقة في البروفايل.</span>
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


@if($documentsForCurrentUser->isNotEmpty())
    <h2 class="text-xl font-bold text-right mt-4 mb-2 mr-2">شهاداتك الحالية:</h2>

    {{-- Container لترتيب الكاردات من اليمين --}}
    <div class="flex flex-wrap justify-end gap-6 p-4">

        @foreach($documentsForCurrentUser as $doc)
            @php
                // تحديد ما إذا كان المستند هو Document أو AttendanceDocument
                $isAttendance = $doc instanceof AttendanceDocument;
                // تحديد مسار العرض بناءً على نوع المستند
                $route = $isAttendance ? route('attendance.show', $doc->uuid) : route('documents.show', $doc->uuid);
                // تحديد اسم القالب
                $title = $doc->template->title ?? ($doc->template ? 'وثيقة حضور' : '');
                // تحديد مسار الصورة
                $filePath = asset('storage/' . $doc->file_path);

                // تحديد تاريخ الإرسال/الإصدار
                $sendDate = $doc->template->send_at ? $doc->template->send_at->format('Y-m-d') : 'غير محدد';
                // تحديد الحدث
                $eventTitle = $doc->template->event->title ?? '';
            @endphp

            <a href="{{ $route }}">
                <section
                    class="max-w-xs bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 scale-105 -translate-y-8 mx-auto">

                    <img src="{{ $filePath }}"
                         alt="صورة المستند"
                         class="w-full h-auto object-contain rounded-lg shadow-md"/>

                    <div class="p-3 space-y-2 text-right">

                        <div class="p-3 space-y-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                            <h2 class="text-lg font-semibold">
                                    @if ($isAttendance)
                                        {{ trans_db('home.attendance') }}: {{ $eventTitle }}
                                    @else
                                        {{ trans_db('profile.doc') }}: {{ $title }}
                                    @endif
                            </h2>
                            <h3 class="text-gray-500 text-sm">
                                {{ trans_db('profile.doc.date') }}: {{ $sendDate }}
                            </h3>
                            <p class="text-gray-600 text-sm">
                                {{ $eventTitle }}
                            </p>
                        </div>

                        {{-- منطق زر التبديل (يفترض أن visibility logic ينطبق فقط على الوثائق العادية) --}}
                        @if(!$isAttendance && auth()->id() === $doc->recipient->user_id)
                            <div
                                class="inline-block border border-gray-300 p-1 rounded hover:border-blue-600 transition relative">
                                <form method="POST" action="{{ route('documents.toggleVisibility', $doc->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-500 hover:text-blue-600">
                                        <i class="bi {{ $doc->visible_on_profile ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </section>
            </a>
        @endforeach

    </div>

    <div class="mt-6 flex justify-center">
        {{ $documentsForCurrentUser->links('vendor.pagination.tailwind-custom') }}
    </div>

@endif



<!-- ثلاث بطاقات تعريفية للموقع -->
<section class="max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6">
    <!-- بطاقة 1 -->
    <div
        class="bg-white p-6 shadow-md rounded-lg text-center space-y-3 transition-transform duration-200 hover:scale-105">
        <i class="bi bi-fingerprint text-blue-600 text-2xl mx-auto"></i>
        <h4 class="font-semibold text-lg">{{ trans_db('words.unique') }}</h4>
        <p class="text-gray-600 text-sm">{{ trans_db('words.for') }}</p>
    </div>
    <!-- بطاقة 2 -->
    <div
        class="bg-white p-6 shadow-md rounded-lg text-center space-y-3 transition-transform duration-200 hover:scale-105">
        <i class="bi bi-qr-code text-blue-600 text-2xl mx-auto"></i>
        <h4 class="font-semibold text-lg">QR Code</h4>
        <p class="text-gray-600 text-sm">{{ trans_db('words.fast.dele') }}</p>
    </div>
    <!-- بطاقة 3 -->
    <div
        class="bg-white p-6 shadow-md rounded-lg text-center space-y-3 transition-transform duration-200 hover:scale-105">
        <i class="bi bi-link-45deg text-blue-600 text-3xl mx-auto"></i>
        <h4 class="font-semibold text-lg">{{ trans_db('words.links') }}</h4>
        <p class="text-gray-600 text-sm">{{ trans_db('words.share') }}</p>
    </div>
</section>

@include('partials.footer')


</body>
</html>
