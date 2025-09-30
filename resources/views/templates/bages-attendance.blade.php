<div class="certificate-wrapper" style="width: 100%; height: 100%; box-sizing: border-box;">

    <style>
        /* إلغاء جميع هوامش الـ PDF */
        @page {
            margin: 0 !important;
            padding: 0 !important;
        }
        /* إلغاء هوامش عناصر HTML */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100%;
            height: 100%;
        }

        /* تنسيق الصورة: ملء العرض والحفاظ على الأبعاد */
        .full-page-image {
            width: 100%;
            height: auto; /* الحفاظ على الأبعاد */
            display: block;
            margin: 0;
            padding: 0;
        }
    </style>

    @if (isset($base64Image) && $base64Image)
        <img
            src="data:image/jpeg;base64,{{ $base64Image ?? '' }}"
            alt="الشهادة"
            class="full-page-image"
        />
    @else
        <div style="text-align: center; color: red; padding: 100px;">
            ⚠️ فشل في عرض الصورة، يرجى التأكد من مسار الملف في الـ Controller.
        </div>
    @endif

</div>
