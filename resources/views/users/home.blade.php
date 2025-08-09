<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة الشهادات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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





<!-- بطاقة النتيجة صغيرة على اليمين -->
@if(request()->has('query') && $document)
<!-- عنوان قائمة الشهادات -->
<h2 class="text-xl font-medium text-right mt-4 mb-2 mr-2">قائمة الشهادات</h2><br>

<a href="{{ route('documents.show', $document->uuid) }}">
    <section class="max-w-xs bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105
                    mx-auto lg:ml-auto lg:mr-5"> <iframe src="{{ asset('storage/' . $document->file_path) }}" class="w-full h-32 object-cover"></iframe>

        <div class="p-3 space-y-2 text-right">
            <h2 class="text-lg font-semibold"> وثيقة: {{ $document->template->title ?? ''}}</h2>
            <h3 class="text-gray-500 text-sm">تاريخ الإصدار: {{ $document->template->send_at->format('Y-m-d')}}</h3>
            <p class="text-gray-600 text-sm">{{ $document->template->event->title ?? ''}}</p>
            <span class="inline-block border border-gray-300 p-1 rounded hover:border-blue-600 transition">
            <i class="bi bi-eye-fill text-gray-500 hover:text-blue-600 cursor-pointer"></i>
          </span>
        </div>
    </section>
</a>
@endif

@if(!request()->has('query') && $user->documents->isEmpty())
<section class="w-full py-10 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto"> {{-- تم التعديل هنا من max-w-xl إلى max-w-3xl --}}
        <div class="bg-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center space-y-4">
            <i class="fas fa-folder-open text-7xl text-gray-400"></i>
            <p class="text-gray-600 text-lg leading-relaxed">
                لم يتم إضافة أي شهادات إلى حسابك بعد. أضف شهادتك ، لبدء استخدامها.
            </p>
        </div>
    </div>
</section>
@endif



@if(request()->has('query') && $event)
{{--                   section search for event --}}
    <a href="#">
        <section class="max-w-xs bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105
                        mr-4 ml-auto px-4 sm:px-0">

            <div class="p-3 space-y-2 text-right">
                <h2 class="text-xl font-bold mb-3">
                    {{ trans_db('search.event.title') }}: {{ $event->title }}
                </h2>

                <div class="flex items-center text-gray-600 text-sm justify-start">
                    <i class="bi bi-calendar-check-fill text-lg text-blue-600"></i>
                    <span class="font-medium ml-4">
                        {{ trans_db('search.event.start.date') }}: {{ $event->start_date }}
                    </span>
                </div>

                <div class="flex items-center text-gray-600 text-sm justify-start">
                    <i class="bi bi-send-fill text-lg text-gray-500"></i>
                    <span class="font-medium ml-4">
                        {{ trans_db('search.event.end.date') }}: {{ $event->end_date }}
                    </span>
                </div>

                <div class="flex items-center text-gray-600 text-sm justify-start">
                    <i class="bi bi-file-earmark-fill text-lg text-gray-500"></i>
                    <span class="font-medium ml-4">
                        {{ trans_db('search.event.count.template') }}: {{ $templateCount }}
                    </span>
                </div>

                <div class="flex items-center text-gray-600 text-sm mb-4 justify-start">
                    <i class="bi bi-people-fill text-lg text-gray-500"></i>
                    <span class="font-medium ml-4">
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












{{--  section not found document     --}}

@if(request()->has('query') && !$document && !$event)
<section class="w-full py-10 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center space-y-4">
            <i class="fas fa-times-circle text-7xl text-red-500"></i>
            <p class="text-gray-600 text-lg leading-relaxed">
                لم يتم العثور على شهادة او حدث بهذا الكود او بهذا الاسم.
            </p>
        </div>
    </div>
</section>
@endif

<!-- ثلاث بطاقات تعريفية للموقع -->
<section class="max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6">
    <!-- بطاقة 1 -->
    <div class="bg-white p-6 shadow-md rounded-lg text-center space-y-3 transition-transform duration-200 hover:scale-105">
        <i class="bi bi-fingerprint text-blue-600 text-2xl mx-auto"></i>
        <h4 class="font-semibold text-lg">كود فريد</h4>
        <p class="text-gray-600 text-sm">كود خاص بكل شهادة</p>
    </div>
    <!-- بطاقة 2 -->
    <div class="bg-white p-6 shadow-md rounded-lg text-center space-y-3 transition-transform duration-200 hover:scale-105">
        <i class="bi bi-qr-code text-blue-600 text-2xl mx-auto"></i>
        <h4 class="font-semibold text-lg">QR Code</h4>
        <p class="text-gray-600 text-sm">مسح سريع التحقق</p>
    </div>
    <!-- بطاقة 3 -->
    <div class="bg-white p-6 shadow-md rounded-lg text-center space-y-3 transition-transform duration-200 hover:scale-105">
        <i class="bi bi-link-45deg text-blue-600 text-3xl mx-auto"></i>
        <h4 class="font-semibold text-lg">روابط مباشرة</h4>
        <p class="text-gray-600 text-sm">مشاركة سريعة وسهلة</p>
    </div>
</section>

@include('partials.footer')



</body>
</html>
