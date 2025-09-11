<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>موقع المشروع</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>
<body class="antialiased text-gray-800">

@if(auth()->check())
        @include('partials.auth-navbar')
@else
    <div dir="ltr" class="text-right">
    @include('partials.navbar')
    </div>
@endif
<!-- SECTION 1: عن المشروع -->


<section id="about-us" class="bg-[#F9FAFB] py-16">
    <div class="container mx-auto px-4 max-w-3xl text-right">
        <h2 class="text-3xl font-bold mb-4 text-center">من نحن</h2>
        @foreach($about as $ab)
            <p class="leading-relaxed mb-4">
                {!! $ab?->description !!}
            </p>
        @endforeach
    </div>
</section>

<!-- SECTION 2: تواصل معنا -->
<section id="contact" class="bg-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-6">تواصل معنا</h2>
        <p class="mx-auto max-w-xl mb-8 leading-relaxed">
            لأي استفسار أو دعم، لا تتردد في مراسلتنا عبر البريد الإلكتروني أو نموذج الاتصال أدناه.
        </p>
        <form method="POST" action="{{ route('contact-us') }}" class="mx-auto max-w-lg space-y-4 text-right">
            @csrf
            {{-- رسائل النجاح أو الفشل --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <input
                type="text"
                name="name"
                required
                placeholder="اسمك"
                class="w-full border rounded-md px-4 py-2 focus:outline-none focus:ring transform transition duration-300 hover:scale-105"
            />
            <input
                type="email"
                name="email"
                placeholder="بريدك الإلكتروني"
                class="w-full border rounded-md px-4 py-2 focus:outline-none focus:ring transform transition duration-300 hover:scale-105"
            />
            <textarea
                placeholder="رسالتك"
                class="w-full border rounded-md px-4 py-2 h-32 resize-none focus:outline-none focus:ring transform transition duration-300 hover:scale-105"
            ></textarea>
            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-md transform transition duration-300 hover:scale-105 hover:bg-blue-700"
            >
                إرسال
            </button>
        </form>
    </div>
</section>

<!-- SECTION 3: الأسئلة الشائعة -->
<section id="faq" class="bg-[#F9FAFB] py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-8">الأسئلة الشائعة</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-right">
            <!-- سؤال 1 -->
            @foreach($faqs as $faq)
            <div class="bg-white p-6 rounded-lg shadow transform transition duration-300 hover:scale-105 hover:shadow-lg">
                <h3 class="font-semibold mb-2">س: {{ $faq?->question }}</h3>
                <p class="text-gray-600">ج: {{ $faq?->answer }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


@include('partials.footer')

</body>
</html>
