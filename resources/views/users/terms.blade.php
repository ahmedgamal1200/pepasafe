<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>الشروط والاحكام</title>
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

<!-- SECTION 5: الشروط والأحكام -->
<section id="terms" class="bg-[#F9FAFB] py-16">
    <div class="container mx-auto px-4 max-w-3xl text-right">
        <h2 class="text-3xl font-bold mb-4 text-center">الشروط والأحكام</h2>
        @foreach($termsAndConditions as $tc)
            <p class="leading-relaxed mb-4">
                {!! $tc?->content !!}
            </p>
        @endforeach
    </div>
</section>

@include('partials.footer')

</body>
</html>
