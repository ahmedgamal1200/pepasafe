<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>التحقق من الوثيقة</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" />
</head>
<body>
    @if(auth()->check())
        @include('partials.auth-navbar')
    @else
        @include('partials.navbar')
    @endif

    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-10 px-4">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-lg p-6 sm:p-8 relative">
    <!-- Title -->
    <h1 class="text-center text-xl sm:text-2xl font-bold mb-4">تحقق من الوثيقة</h1>

    <!-- Description with Icon -->
    <div class="flex items-center justify-center mb-6">
        <i class="fas fa-check-circle text-green-500 text-xl ml-2"></i>
        <p class="text-base text-green-500">تم التحقق بنجاح من صحة الوثيقة .</p>
    </div>

    <!-- Main Content (Image and Details Swapped) -->
    <div class="flex flex-col md:flex-row items-start mb-6">
        <!-- Left Side Image -->
        <div class="w-full md:w-1/2 md:pr-6 mb-6 md:mb-0 flex justify-center">
            <div class="relative w-full max-w-sm">

                {{-- الشهادة --}}
                <iframe
                    id="cert-image"
                    src="{{ asset('storage/' . $document->file_path) }}"
                    class="w-full h-auto object-cover rounded-lg border transition-all duration-300
                   {{ auth()->check() ? '' : 'blur-sm' }}">
                </iframe>

                @guest
                    {{-- بلور مع قفل وزر تسجيل الدخول --}}
                    <div class="absolute inset-0 flex flex-col justify-center items-center text-white text-center space-y-2 bg-transparent">
                        <i class="fas fa-lock text-3xl text-gray-800 drop-shadow-md"></i>
                        <a href="{{ route('login') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium shadow-md">
                            سجل الدخول لعرض الشهادة
                        </a>
                    </div>
                @endguest

            </div>
        </div>



        <!-- Divider -->
        <div class="hidden md:block w-px bg-gray-200 self-stretch mx-4"></div>

        <!-- Right Side Details -->
        <div class="w-full md:w-1/2 md:pl-6 mb-6 md:mb-0">
            <h2 class="text-lg font-semibold mb-4">تفاصيل الوثيقة</h2>
            <div class="flex justify-between mb-3">
                <span class="text-gray-600">اسم الحدث:</span>
                <span class="font-medium">{{ $document->template->event->title ?? ''}}</span>
            </div>
            <div class="flex justify-between mb-3">
                <span class="text-gray-600"> اسم المنظم:</span>
                <span class="font-medium">{{ $document->template->event->user->name ?? ''}}</span>
            </div>
            <div class="flex justify-between mb-3">
                <span class="text-gray-600">جهة الإصدار:</span>
                <span class="font-medium">{{ $document->template->event->issuer ?? ''}}</span>
            </div>
            <div class="flex justify-between mb-3">
                <span class="text-gray-600">تاريخ الإصدار:</span>
                <span class="font-medium">{{ $document->template->send_at->format('Y-m-d')}}</span>
            </div>
            <div class="flex justify-between mb-3">
                <span class="text-gray-600">الصلاحية:</span>
                <span class="font-medium">
                    @if($document->template->validity === 'permanent')
                        دائمة
                    @elseif($document->template->validity === 'temporary')
                        مؤقتة
                    @endif
                </span>
            </div>

            <div class="flex justify-between mb-3">
                <span class="text-gray-600">اسم المستخدم:</span>
                <span class="font-medium">{{ $document->recipient->user->name }}</span>
            </div>
            <div class="flex justify-between mb-3">
                <span class="text-gray-600">كود الوثيقة:</span>
                <span class="font-medium border border-gray-300 bg-gray-100 rounded px-2 py-1" id="cert-code">{{ $document->unique_code }}</span>
            </div>
        </div>
    </div>

    <hr class="border-t border-gray-200 mb-6" />

    <!-- QR and Link Section: QR on Left, Link on Right -->
    <div class="w-full flex justify-between items-center mb-6">
        <!-- QR Code on Left -->
        <div class="w-24 h-24 flex-shrink-0">
            <img src="{{ asset('storage/' . $document->qr_code_path) }}" alt="QR Code" class="w-full h-full object-contain" />
        </div>

        <!-- Link Text on Right -->

    </div>
    <div class="text-right pr-4">
        <p class="text-gray-600 mb-1">
            <i class="fas fa-info-circle text-blue-500 ml-1"></i> احتفظ برمز QR للتحقق من صحة الوثيقة لاحقًا.
        </p>
    </div>


    <hr class="border-t border-gray-200 mb-6" />

    <!-- Buttons -->
    <div class="w-full flex justify-center">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 max-w-lg">
            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="flex items-center justify-center py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-print ml-2"></i> طباعة الشهادة
            </a>
            <a href="{{ asset('storage/' . $document->file_path) }}"
               download
               class="flex items-center justify-center py-2 px-4 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                <i class="fas fa-download ml-2"></i> تحميل PDF
            </a>

            @if($document->recipient && $document->recipient->user_id === auth()->id())
            <button id="share-btn" data-url="{{ route('documents.verify', $document->uuid) }}" class="flex items-center justify-center py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="bi bi-share-fill ml-2"></i>مشاركة
            </button>
            @endif
        </div>
    </div>
</div>
    </div>

<!-- Image Modal -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg overflow-hidden relative max-w-lg w-full">
        <button id="modal-close" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
            <i class="fas fa-times fa-lg"></i>
        </button>
        <img id="modal-image" src="" alt="صورة موسعة" class="w-full h-auto object-contain" />
    </div>
</div>

<script src="{{ asset('js/show-document.js') }}"></script>


</body>
</html>
