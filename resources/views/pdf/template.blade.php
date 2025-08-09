{{--<!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--    <style>--}}
{{--        body { margin: 0; padding: 0; }--}}
{{--        .background {--}}
{{--            width: {{ $canvasWidth }}px; /* كان $imageWidth، الآن $canvasWidth */--}}
{{--            height: {{ $canvasHeight }}px; /* كان $imageHeight، الآن $canvasHeight */--}}
{{--            background-color: #fff;--}}
{{--            position: absolute;--}}
{{--        }--}}
{{--        .text {--}}
{{--            position: absolute;--}}
{{--            color: black;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<img src="{{ $background }}" class="background" style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px;" />--}}
{{--@php--}}
{{--    $frontendCanvasWidth  = $canvasWidth;  // <--- صحيح الآن--}}
{{--    $frontendCanvasHeight = $canvasHeight; // <--- صحيح الآن--}}

{{--    $pdfRenderWidth  = $frontendCanvasWidth;--}}
{{--    $pdfRenderHeight = $frontendCanvasHeight;--}}

{{--    $scaleX = $pdfRenderWidth / $frontendCanvasWidth;--}}
{{--    $scaleY = $pdfRenderHeight / $frontendCanvasHeight;--}}
{{--@endphp--}}

{{--@foreach ($fields as $field)--}}
{{--    <div class="text" style="--}}
{{--    left: {{ ($field['x'] * $scaleX) - 80 }}px; /* عدّل هذا السطر */--}}
{{--        top: {{ ($field['y'] * $scaleY) - 3 }}px;   /* عدّل هذا السطر */--}}
{{--        font-family: {{ $field['font'] }};--}}
{{--        font-size: {{ ($field['size'] * $scaleY) }}px; /* عدّل هذا السطر */--}}
{{--        --}}{{--left: {{ $field['x'] * $scaleX }}px;--}}
{{--        --}}{{--top: {{ $field['y'] * $scaleY }}px;--}}
{{--        font-family: {{ $field['font'] }};--}}
{{--        --}}{{--font-size: {{ $field['size'] * $scaleY }}px;--}}
{{--        color: {{ $field['color'] }};--}}
{{--        text-align: {{ $field['align'] }};--}}
{{--        font-weight: {{ $field['weight'] }};--}}
{{--        transform: rotate({{ $field['rotation'] }}deg);--}}
{{--    ">--}}
{{--        {{ $field['text'] }}--}}
{{--    </div>--}}
{{--@endforeach--}}
{{--</body>--}}
{{--</html>--}}

{{--    <!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--    <style>--}}
{{--        body { margin: 0; padding: 0; }--}}
{{--        .background {--}}
{{--            width: {{ $canvasWidth }}px;--}}
{{--            height: {{ $canvasHeight }}px;--}}
{{--            background-color: #fff;--}}
{{--            position: absolute;--}}
{{--        }--}}

{{--        .text {--}}
{{--            position: absolute;--}}
{{--            color: black;--}}
{{--        }--}}
{{--        .footer {--}}
{{--            width: {{ $canvasWidth * 0.8 }}px; /* 80% من $canvasWidth */--}}
{{--            height: 60px; /* ارتفاع مشابه للـ 40px للصور + padding */--}}
{{--            position: absolute;--}}
{{--            background-color: white;--}}
{{--            border: 1px solid #ccc;--}}
{{--            border-radius: 4px;--}}
{{--            padding: 10px;--}}
{{--            display: flex;--}}
{{--            justify-content: space-around;--}}
{{--            align-items: center;--}}
{{--            box-sizing: border-box;--}}
{{--        }--}}
{{--        .logo {--}}
{{--            width: 40px;--}}
{{--            height: 40px;--}}
{{--        }--}}
{{--        .qr-code {--}}
{{--            width: 40px;--}}
{{--            height: 40px;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<!-- الوجه الأمامي -->--}}
{{--<img src="{{ $backgroundFront }}" class="background" style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px;" />--}}
{{--@foreach ($frontFields as $field)--}}
{{--    <div class="text" style="--}}
{{--        left: {{ $field['x'] }}px; /* بدون scaling أو offset */--}}
{{--        top: {{ $field['y'] }}px;  /* بدون scaling أو offset */--}}
{{--        font-family: {{ $field['font'] }};--}}
{{--        font-size: {{ $field['size'] }}px;--}}
{{--        color: {{ $field['color'] }};--}}
{{--        text-align: {{ $field['align'] }};--}}
{{--        font-weight: {{ $field['weight'] }};--}}
{{--        transform: rotate({{ $field['rotation'] }}deg);--}}
{{--    ">--}}
{{--        {{ $field['text'] }}--}}
{{--    </div>--}}
{{--@endforeach--}}

{{--<!-- الوجه الخلفي (لو موجود) -->--}}
{{--@if ($hasBack)--}}
{{--    <img src="{{ $backgroundBack }}" class="background" style="top: {{ $canvasHeight }}px; left: 0; width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px;" />--}}
{{--    @foreach ($backFields as $field)--}}
{{--        <div class="text" style="--}}
{{--            left: {{ $field['x'] }}px;--}}
{{--            top: {{ $field['y'] + $canvasHeight }}px;--}}
{{--            font-family: {{ $field['font'] }};--}}
{{--            font-size: {{ $field['size'] }}px;--}}
{{--            color: {{ $field['color'] }};--}}
{{--            text-align: {{ $field['align'] }};--}}
{{--            font-weight: {{ $field['weight'] }};--}}
{{--            transform: rotate({{ $field['rotation'] }}deg);--}}
{{--        ">--}}
{{--            {{ $field['text'] }}--}}
{{--        </div>--}}
{{--    @endforeach--}}
{{--@endif--}}

{{--<!-- الـ Footer (تحت الوجه الأمامي لو فيه وجه أمامي بس، أو تحت الخلفي لو فيه وجهين) -->--}}
{{--@php--}}
{{--    $footerTop = $hasBack ? ($canvasHeight * 2) : $canvasHeight;--}}
{{--    $logoPath = file_exists(public_path('assets/logo.jpg')) ? public_path('assets/logo.jpg') : asset('images/default-logo.jpg');--}}
{{--@endphp--}}
{{--<div class="footer" style="top: {{ $footerTop }}px; left: {{ ($canvasWidth - ($canvasWidth * 0.8)) / 2 }}px;">--}}
{{--    <img src="{{ asset('assets/logo.jpg') }}" class="logo" alt="Logo">--}}
{{--    <img src="{{ $qrPath ?? asset('images/default-qr.png') }}" class="qr-code" alt="QR Code">--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}

<!DOCTYPE html>
<html>
<head>
    <style>
        body { margin: 0; padding: 0; }
        .background {
            width: {{ $canvasWidth }}px;
            height: {{ $canvasHeight }}px;
            background-color: #fff;
            position: absolute;
        }
        .footer {
            width: {{ $canvasWidth * 0.8 }}px;
            height: 60px;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            /* الخصائص دي هي اللي تم تعديلها أو التأكيد عليها */
            display: flex;
            justify-content: space-between; /* ده بيخلي العناصر تروح للأطراف ويسيب مسافة بينهم */
            align-items: center;
            box-sizing: border-box;
            padding: 10px 30px;
        }
        .logo {
            width: 40px;
            height: 40px;
            /* اللوجو هيكون على اليمين بشكل طبيعي لأنه آخر عنصر */
        }
        .qr-code {
            width: 40px;
            height: 40px;
            /* الـ QR Code هيكون على الشمال */
        }
    </style>
</head>
<body>
<!-- الوجه الأمامي -->
<img src="{{ $backgroundFront }}" class="background" style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px;" />
@foreach ($frontFields as $field)

{{--    @dd($field)--}}
    <div class="text" style="
        left: {{ $field['x'] }}px;
        top: {{ $field['y'] }}px;
        font-family: {{ $field['font'] }};
        font-size: {{ $field['size'] }}px;
        color: {{ $field['color'] }};
        text-align: {{ $field['align'] }};
        font-weight: {{ $field['weight'] }};
        transform: rotate({{ $field['rotation'] }}deg);
    ">
        {{ $field['text'] }}
    </div>
@endforeach

<!-- الوجه الخلفي (لو موجود) -->
@if ($hasBack)
    <img src="{{ $backgroundBack }}" class="background" style="top: {{ $canvasHeight }}px; left: 0; width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px;" />
    @foreach ($backFields as $field)
        <div class="text" style="
            left: {{ $field['x'] }}px;
            top: {{ $field['y'] + $canvasHeight }}px;
            font-family: {{ $field['font'] }};
            font-size: {{ $field['size'] }}px;
            color: {{ $field['color'] }};
            text-align: {{ $field['align'] }};
            font-weight: {{ $field['weight'] }};
            transform: rotate({{ $field['rotation'] }}deg);
        ">
            {{ $field['text'] }}
        </div>
    @endforeach
@endif

<!-- الـ Footer (تحت الوجه الأمامي لو فيه وجه أمامي بس، أو تحت الخلفي لو فيه وجهين) -->
@php
    $footerTop = $hasBack ? ($canvasHeight * 2) : $canvasHeight;
    $logoPath = file_exists(public_path('assets/logo.jpg')) ? public_path('assets/logo.jpg') : asset('images/default-logo.jpg');
    $qrCodePath = file_exists(public_path('assets/qr-code.jpg')) ? public_path('assets/qr-code.jpg') : asset('images/default-logo.jpg');
@endphp
<div class="footer" style="top: {{ $footerTop }}px; left: {{ ($canvasWidth - ($canvasWidth * 0.8)) / 2 }}px;">
    <img src="{{ $logoPath }}" class="logo" alt="Logo">
    <img src="{{ $qrCodePath }}" class="qr-code" alt="QR Code">
</div>
</body>
</html>


{{--    <!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--    <style>--}}
{{--        body { margin: 0; padding: 0; }--}}
{{--        .page { position: relative; width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px; background-image: url('{{ $backgroundFront }}'); background-size: contain; }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<div class="page">--}}
{{--    @foreach ($frontFields as $field)--}}
{{--        <div style="--}}
{{--                position: absolute;--}}
{{--                left: {{ $field['x'] }}px;--}}
{{--                top: {{ $field['y'] }}px;--}}
{{--                font-family: {{ $field['font'] }};--}}
{{--                font-size: {{ $field['size'] }}px;--}}
{{--                color: {{ $field['color'] }};--}}
{{--                text-align: {{ $field['align'] }};--}}
{{--                font-weight: {{ $field['weight'] }};--}}
{{--                transform: rotate({{ $field['rotation'] }}deg);--}}
{{--                border: 1px solid red; /* للتجربة */--}}
{{--            ">--}}
{{--            {{ $field['text'] }}--}}
{{--        </div>--}}
{{--    @endforeach--}}
{{--    <!-- تجربة نص ثابت -->--}}
{{--    <div style="position: absolute; left: 50px; top: 50px; color: #000000;">Test Text</div>--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}
