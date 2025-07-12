<!DOCTYPE html>
<html lang="ar" dir="rtl"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لم يتم العثور على شهادة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50 p-6 space-y-12">
@include('partials.auth-navbar')

@include('partials.search-bar')

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

</body>
</html>
