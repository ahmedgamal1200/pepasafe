<!DOCTYPE html>
<html lang="ar" dir="rtl">
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
</head>
<body class="space-y-12 bg-white">

@include('partials.auth-navbar')

@include('partials.search-bar')



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

{{--  section not found document     --}}

@if(request()->has('query') && !$document)
<section class="w-full py-10 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center space-y-4">
            <i class="fas fa-times-circle text-7xl text-red-500"></i>
            <p class="text-gray-600 text-lg leading-relaxed">
                لم يتم العثور على شهادة بهذا الكود.
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
