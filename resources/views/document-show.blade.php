<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بحث وعرض النتائج</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />


</head>
<body class="bg-gray-50 p-6 space-y-12">
<!-- عنوان قائمة الشهادات -->

<h1 class="text-2xl font-bold text-center mb-6">ابحث وتحقق من الشهادات   <i class="bi bi-patch-check-fill text-blue-500 mr-2 text-xl"></i></h1>

<!-- قسم البحث -->
@include('partials.search-bar')

<!-- عنوان قائمة الشهادات -->
<h2 class="text-xl font-medium text-right mt-4 mb-2">قائمة الشهادات</h2>

<!-- بطاقة النتيجة صغيرة على اليمين -->
<section class="max-w-xs ml-auto bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105">
    <img src="/path/to/image.jpg" alt="وصف الصورة" class="w-full h-32 object-cover">
    <div class="p-3 space-y-2 text-right">
        <h2 class="text-lg font-semibold">عنوان النتيجة</h2>
        <p class="text-gray-600 text-sm">هذا وصف مختصر يشرح المحتوى المعروض.</p>
        <h3 class="text-gray-500 text-sm">عنوان فرعي</h3>
        <span class="inline-block border border-gray-300 p-1 rounded hover:border-blue-600 transition">
        <i class="bi bi-eye-fill text-gray-500 hover:text-blue-600 cursor-pointer"></i>
      </span>
    </div>
</section>

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

</body>
</html>
