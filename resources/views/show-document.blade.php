<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>التحقق من الوثيقة</title>
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

<div class="min-h-screen flex items-start justify-center bg-gray-100 py-10 px-4">
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-lg p-6 sm:p-8 relative">
        <h1 class="text-center text-xl sm:text-2xl font-bold mb-4">تحقق من الوثيقة</h1>

        <div class="flex items-center justify-center mb-6">
            <i class="fas fa-check-circle text-green-500 text-xl ml-2"></i>
            <p class="text-base text-green-500">تم التحقق بنجاح من صحة الوثيقة.</p>
        </div>

        <div class="mb-6">
            <div class="relative w-full overflow-hidden rounded-lg shadow-md border-2 border-gray-200">
                {{-- الشهادة --}}
                <img id="cert-image" src="{{ asset('storage/' . $document->file_path) }}"
                     alt="Document Image"
                     class="w-full h-auto object-contain rounded-lg
@guest blur-sm @endguest" />

                @guest
                    {{-- بلور مع قفل وزر تسجيل الدخول --}}
                    <div class="absolute inset-0 flex flex-col justify-center items-center text-white text-center space-y-2 bg-transparent">
                        <i class="fas fa-lock text-3xl text-gray-800 drop-shadow-md"></i>
                        <a href="{{ route('login') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md transition-colors">
                            سجل الدخول لعرض الشهادة
                        </a>
                    </div>
                @endguest

            </div>
            <div class="w-full px-4 py-3 bg-white rounded-b-lg shadow-md border-2 border-gray-200 border-t-0 -mt-2 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('/assets/logo.jpg') }}" alt="Pepasafe Logo" class="h-8 w-auto">
                    <span class="text-sm text-gray-500 font-medium">
            Verified by Pepasafe
        </span>
                </div>
            </div>
        </div>

        <hr class="border-t border-gray-300 mb-6" />

        <div class="flex flex-col md:flex-row gap-6 items-start mb-6" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

            <!-- Details Section -->
            <div class="w-full md:w-2/3 order-2 md:order-1">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">تفاصيل الوثيقة</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex flex-col">
                        <span class="text-gray-600">اسم الحدث:</span>
                        <span class="font-medium text-gray-800">{{ $document->template->event->title ?? ''}}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">اسم المنظم:</span>
                        <span class="font-bold text-gray-800 flex items-center gap-2">
                            <span>{{ $document->template->event->user->name ?? ''}}</span>
                            @if($document->template->event->user->category)
                                <span class="flex-shrink-0" title="{{ $document->template->event->user->category->icon }}">
                                    {!! App\Helpers\IconHelper::get($document->template->event->user->category->icon) !!}
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">جهة الإصدار:</span>
                        <span class="font-medium text-gray-800">{{ $document->template->event->issuer ?? ''}}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">تاريخ الإصدار:</span>
                        <span class="font-medium text-gray-800">{{ $document->template->send_at->format('Y-m-d')}}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">الصلاحية:</span>
                        <span class="font-medium text-gray-800">
                    @if($document->template->validity === 'permanent')
                                دائمة
                            @elseif($document->template->validity === 'temporary')
                                مؤقتة
                            @endif
                </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">اسم المستخدم:</span>
                        <span class="font-medium text-gray-800">{{ $document->recipient->user->name }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">كود الوثيقة:</span>
                        <span class="font-medium text-gray-800 border border-gray-300 bg-gray-100 rounded px-2 py-1 inline-block self-start" id="cert-code">{{ $document->unique_code }}</span>
                    </div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="w-full md:w-1/3 flex flex-col items-center md:items-end order-1 md:order-2">
                <div class="flex flex-col items-center text-center">
                    <img src="{{ asset('storage/' . $document->qr_code_path) }}" alt="QR Code" class="w-32 h-32 object-contain border border-gray-300 rounded-lg p-2" />
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle text-blue-500 @if(app()->getLocale() === 'ar') ml-1 @else mr-1 @endif"></i> احتفظ برمز QR للتحقق.
                    </p>
                </div>
            </div>

        </div>

        <hr class="border-t border-gray-300 my-6" />

        <div class="w-full flex justify-center">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-2xl w-full">
                <button id="print-btn" class="flex items-center justify-center py-3 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                    <i class="fas fa-print ml-2"></i> طباعة الشهادة
                </button>
                <button id="download-btn" class="flex items-center justify-center py-3 px-4 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors shadow-md">
                    <i class="fas fa-download ml-2"></i> تحميل
                </button>
                @if($document->recipient && $document->recipient->user_id === auth()->id())
                    <button id="share-btn" data-url="{{ route('documents.verify', $document->uuid) }}" class="flex items-center justify-center py-3 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-md">
                        <i class="bi bi-share-fill ml-2"></i>مشاركة
                    </button>
                @endif
            </div>
        </div>

    </div>
</div>


<script src="{{ asset('js/show-document.js') }}"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.getElementById('print-btn');
        const imagePath = "{{ asset('storage/' . $document->file_path) }}";
        const logoPath = "{{ asset('/assets/logo.jpg') }}";

        if (printBtn) {
            printBtn.addEventListener('click', () => {
                const tempImg = new Image();
                const tempLogo = new Image();
                tempImg.src = imagePath;
                tempLogo.src = logoPath;

                Promise.all([
                    new Promise((resolve) => tempImg.onload = resolve),
                    new Promise((resolve) => tempLogo.onload = resolve)
                ]).then(() => {
                    const imgCanvas = document.createElement('canvas');
                    const imgContext = imgCanvas.getContext('2d');
                    imgCanvas.width = tempImg.width;
                    imgCanvas.height = tempImg.height;
                    imgContext.drawImage(tempImg, 0, 0);
                    const base64Image = imgCanvas.toDataURL('image/jpeg');

                    const logoCanvas = document.createElement('canvas');
                    const logoContext = logoCanvas.getContext('2d');
                    logoCanvas.width = tempLogo.width;
                    logoCanvas.height = tempLogo.height;
                    logoContext.drawImage(tempLogo, 0, 0);
                    const base64Logo = logoCanvas.toDataURL('image/jpeg');

                    const footerHtml = `
                    <div style="
                        background-color: white;
                        border: 1px solid #ccc;
                        border-top: none;
                        border-radius: 0 0 4px 4px;
                        padding: 8px 16px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        width: 100%;
                        box-sizing: border-box;
                        font-family: Arial, sans-serif;
                        direction: ltr;  /* تم التغيير هنا */
                    ">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <img src="${base64Logo}" alt="Pepasafe Logo" style="height: 32px; width: auto;">
                            <span style="font-size: 14px; color: #6b7280; font-weight: 500; margin-left: 8px;">
                                Verified by Pepasafe
                            </span>
                        </div>
                        <span style="font-size: 12px; color: #9ca3af;">
                            <i class="fas fa-copyright" style="margin-right: 4px;"></i>
                        </span>
                    </div>
                `;

                    const printContent = `
                    <div style="display: flex; flex-direction: column; align-items: center; padding: 20px;">
                        <img src="${base64Image}" style="width: 100%; height: auto; max-width: 800px; border-radius: 8px 8px 0 0; border: 2px solid #e5e7eb; border-bottom: none;">
                        ${footerHtml}
                    </div>
                `;

                    const printWindow = window.open('', '_blank');
                    if (printWindow) {
                        printWindow.document.write('<html><head><title>طباعة الشهادة</title>');
                        printWindow.document.write('<style>');
                        printWindow.document.write('body { margin: 0; padding: 0; }');
                        printWindow.document.write('img { max-width: 100%; height: auto; }');
                        printWindow.document.write('</style>');
                        printWindow.document.write('</head><body>');
                        printWindow.document.write(printContent);
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();

                        setTimeout(() => {
                            printWindow.print();
                        }, 500);
                    }
                }).catch(error => {
                    console.error('فشل في تحميل الصور للطباعة.', error);
                    alert('عذراً، فشل تحميل الصورة للطباعة.');
                });
            });
        }

        const downloadBtn = document.getElementById('download-btn');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', () => {
                const tempImg = new Image();
                const tempLogo = new Image();

                tempImg.src = imagePath;
                tempLogo.src = logoPath;

                Promise.all([
                    new Promise((resolve) => tempImg.onload = resolve),
                    new Promise((resolve) => tempLogo.onload = resolve)
                ]).then(() => {
                    const finalWidth = tempImg.width;
                    const footerHeight = 50;
                    const finalHeight = tempImg.height + footerHeight;

                    const finalCanvas = document.createElement('canvas');
                    const finalContext = finalCanvas.getContext('2d');
                    finalCanvas.width = finalWidth;
                    finalCanvas.height = finalHeight;

                    finalContext.drawImage(tempImg, 0, 0, finalWidth, tempImg.height);
                    finalContext.fillStyle = '#fff';
                    finalContext.fillRect(0, tempImg.height, finalWidth, footerHeight);

                    // حساب أبعاد اللوجو
                    const logoHeight = 32;
                    const logoWidth = (tempLogo.width / tempLogo.height) * logoHeight;

                    // حساب أبعاد النص
                    const text = 'Verified by Pepasafe';
                    finalContext.font = `500 14px Arial, sans-serif`;

                    // تحديد المسافات من اليسار
                    const padding = 16;
                    const spaceBetween = 8;

                    // رسم اللوجو
                    const logoX = padding;
                    const logoY = tempImg.height + (footerHeight - logoHeight) / 2;
                    finalContext.drawImage(tempLogo, logoX, logoY, logoWidth, logoHeight);

                    // رسم النص
                    finalContext.fillStyle = '#6b7280';
                    finalContext.textAlign = 'left';
                    const textX = logoX + logoWidth + spaceBetween;
                    const textY = tempImg.height + (footerHeight / 2) + 5;
                    finalContext.fillText(text, textX, textY);

                    // تحويل الـ canvas إلى رابط تحميل
                    const downloadLink = document.createElement('a');
                    downloadLink.href = finalCanvas.toDataURL('image/jpeg', 1.0);
                    downloadLink.download = 'document-with-footer.jpg';
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }).catch(error => {
                    console.error('فشل في تحميل الصور للتحميل.', error);
                    alert('عذراً، فشل تحميل المستند للتحميل.');
                });
            });
        }
    });
</script>

@include('partials.footer')
</body>
</html>
