<?php

namespace App\Services;

use App\Imports\DocumentDataImport;
use App\Jobs\SendCertificateJob;
use App\Models\AttendanceTemplate;
use App\Models\DocumentTemplate;
use App\Repositories\Eventor\AttendanceDocumentRepository;
use App\Repositories\Eventor\DocumentRepository;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Exception;
// use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

// أو Driver\\Imagick إذا كنت تستخدم Imagick

class DocumentGenerationService
{
    protected DocumentRepository $documentRepository;

    protected AttendanceDocumentRepository $attendanceDocumentRepository;

    public function __construct(
        DocumentRepository $documentRepository,
        AttendanceDocumentRepository $attendanceDocumentRepository
    ) {
        $this->documentRepository = $documentRepository;
        $this->attendanceDocumentRepository = $attendanceDocumentRepository;
    }

    //    public function generateDocuments(DocumentTemplate $template, array $recipients, $templateDataFile): void
    //    {
    //        $frontTemplate = $template->templateFiles()->where('side', 'front')->first();
    //        $backTemplate = $template->templateFiles()->where('side', 'back')->first();
    //
    //        if (!$frontTemplate) {
    //            throw new \Exception('Front document template not found');
    //        }
    //
    //        // استيراد بيانات الإكسيل
    //        $documentDataImport = new DocumentDataImport();
    //        Excel::import($documentDataImport, $templateDataFile);
    //        $documentRows = $documentDataImport->rows;
    //
    // //        dd($documentRows);
    //
    //        // التحقق من أن عدد الصفوف يطابق عدد المستلمين
    //        if (count($documentRows) < count($recipients)) {
    //            throw new \Exception('Not enough data rows in Excel for all recipients');
    //        }
    //
    //        $canvasWidth = 900;
    //        $canvasHeight = 600;
    //        $totalHeight = $backTemplate ? $canvasHeight * 2 : $canvasHeight;
    //
    //        $backgroundPathFront = storage_path('app/public/' . $frontTemplate->file_path);
    //        $resizedBackgroundPathFront = storage_path('app/public/templates/resized_' . basename($frontTemplate->file_path));
    //
    //        if (!file_exists($backgroundPathFront)) {
    //            throw new \Exception("Front background file not found: $backgroundPathFront");
    //        }
    //
    //        $image = Image::read($backgroundPathFront)->resize(900, 600, function ($constraint) {
    //            $constraint->aspectRatio();
    //        })->save($resizedBackgroundPathFront);
    //
    //        $backgroundPathBack = null;
    //        $resizedBackgroundPathBack = null;
    //        if ($backTemplate) {
    //            $backgroundPathBack = storage_path('app/public/' . $backTemplate->file_path);
    //            $resizedBackgroundPathBack = storage_path('app/public/templates/resized_' . basename($backTemplate->file_path));
    //
    //            if (!file_exists($backgroundPathBack)) {
    //                throw new \Exception("Back background file not found: $backgroundPathBack");
    //            }
    //
    //            $image = Image::read($backgroundPathBack)->resize(900, 600, function ($constraint) {
    //                $constraint->aspectRatio();
    //            })->save($resizedBackgroundPathBack);
    //        }
    //
    //        // ربط كل مستلم بصف من الإكسيل
    //        foreach ($recipients as $index => $recipient) {
    //            $dataRow = $documentRows[$index] ?? []; // أخذ الصف المطابق للمستلم
    //            $frontFields = $template->fields()->where('side', 'front')->get();
    // //            dd($frontFields->pluck('field_key')->toArray());
    //            $backFields = $template->fields()->where('side', 'back')->get();
    //
    //            $frontRenderedFields = [];
    //            foreach ($frontFields as $field) {
    //                $value = $dataRow[$field->field_key] ?? $field->field_key; // استخدام القيمة من الصف
    //                $frontRenderedFields[] = [
    //                    'text' => $value,
    //                    'x' => $field->position_x,
    //                    'y' => $field->position_y,
    //                    'font' => $field->font_family,
    //                    'size' => $field->font_size,
    //                    'color' => $field->font_color,
    //                    'align' => $field->text_align,
    //                    'weight' => $field->font_weight,
    //                    'rotation' => $field->rotation,
    //                ];
    //            }
    //
    //            $backRenderedFields = [];
    //            foreach ($backFields as $field) {
    //                $value = $dataRow[$field->field_key] ?? $field->field_key; // استخدام القيمة من الصف
    //                $backRenderedFields[] = [
    //                    'text' => $value,
    //                    'x' => $field->position_x,
    //                    'y' => $field->position_y,
    //                    'font' => $field->font_family,
    //                    'size' => $field->font_size,
    //                    'color' => $field->font_color,
    //                    'align' => $field->text_align,
    //                    'weight' => $field->font_weight,
    //                    'rotation' => $field->rotation,
    //                ];
    //            }
    //
    //            $pdf = Pdf::loadView('pdf.template', [
    //                'backgroundFront' => $resizedBackgroundPathFront,
    //                'backgroundBack' => $resizedBackgroundPathBack,
    //                'frontFields' => $frontRenderedFields,
    //                'backFields' => $backRenderedFields,
    //                'canvasWidth' => $canvasWidth,
    //                'canvasHeight' => $canvasHeight,
    //                'hasBack' => $backTemplate ? true : false,
    //            ])->setPaper([0, 0, $canvasWidth, $totalHeight], 'px');
    //
    //            $pdfPath = "certificates/" . auth()->id() . "/" . uniqid() . ".pdf";
    //            $pdfFullPath = storage_path("app/public/{$pdfPath}");
    //            Storage::disk('public')->makeDirectory("certificates/" . auth()->id());
    //            $pdf->save($pdfFullPath);
    //
    //            $document = $this->documentRepository->create([
    //                'file_path' => $pdfPath,
    //                'document_template_id' => $template->id,
    //                'recipient_id' => $recipient->id,
    //                'valid_from' => $template->valid_from,
    //                'valid_until' => $template->valid_until,
    //            ]);
    //
    //            $this->dispatchCertificate($document, $template->send_at);
    //        }
    //    }

    // ما قبل الجوب
    /**
     * @throws Exception
     */
    //    public function generateDocuments(DocumentTemplate $template, array $recipients, $templateDataFile, $canvasWidth, $canvasHeight): void
    //    {
    //        $frontTemplate = $template->templateFiles()->where('side', 'front')->first();
    //        $backTemplate = $template->templateFiles()->where('side', 'back')->first();
    //
    //        if (!$frontTemplate) {
    //            throw new Exception('Front document template not found');
    //        }
    //
    //        $documentDataImport = new DocumentDataImport();
    //        Excel::import($documentDataImport, $templateDataFile);
    //        $documentRows = $documentDataImport->rows;
    //
    //        if (count($documentRows) < count($recipients)) {
    //            throw new Exception('Not enough data rows in Excel for all recipients');
    //        }
    //
    //        $backgroundPathFront = storage_path('app/public/' . $frontTemplate->file_path);
    //        if (!file_exists($backgroundPathFront)) {
    //            throw new Exception("Front background file not found: $backgroundPathFront");
    //        }
    //
    //        $backgroundPathBack = null;
    //        if ($backTemplate) {
    //            $backgroundPathBack = storage_path('app/public/' . $backTemplate->file_path);
    //            if (!file_exists($backgroundPathBack)) {
    //                throw new Exception("Back background file not found: $backgroundPathBack");
    //            }
    //        }
    //
    //        $totalHeight = $backTemplate ? $canvasHeight * 2 : $canvasHeight;
    //        $manager = new ImageManager(new Driver());
    //
    //        foreach ($recipients as $index => $recipient) {
    //            $dataRow = $documentRows[$index] ?? [];
    //            $frontFields = $template->fields()->where('side', 'front')->get();
    //            $backFields = $template->fields()->where('side', 'back')->get();
    //
    //            $frontRenderedFields = [];
    //            foreach ($frontFields as $field) {
    //                $value = $dataRow[$field->field_key] ?? $field->field_key;
    //                $frontRenderedFields[] = [
    //                    'text' => $value,
    //                    'x' => $field->position_x,
    //                    'y' => $field->position_y,
    //                    'font' => $field->font_family,
    //                    'size' => $field->font_size,
    //                    'color' => $field->font_color,
    //                    'align' => $field->text_align,
    //                    'weight' => $field->font_weight,
    //                    'rotation' => $field->rotation,
    //                ];
    //            }
    //
    //            $backRenderedFields = [];
    //            foreach ($backFields as $field) {
    //                $value = $dataRow[$field->field_key] ?? $field->field_key;
    //                $backRenderedFields[] = [
    //                    'text' => $value,
    //                    'x' => $field->position_x,
    //                    'y' => $field->position_y,
    //                    'font' => $field->font_family,
    //                    'size' => $field->font_size,
    //                    'color' => $field->font_color,
    //                    'align' => $field->text_align,
    //                    'weight' => $field->font_weight,
    //                    'rotation' => $field->rotation,
    //                ];
    //            }
    //
    //            // ----------------------------------------------------
    //            // الكود المعدل يبدأ من هنا
    //            // ----------------------------------------------------
    //
    //            // إنشاء الصورة من قالب الواجهة الأمامية
    //            // إنشاء الصورة من قالب الواجهة الأمامية
    //            $image = $manager->read($backgroundPathFront);
    //
    //            // إضافة حقول النص على الصورة
    //            foreach ($frontRenderedFields as $field) {
    //                // يتم ضبط الخط والخصائص الأخرى هنا
    //                $image->text($field['text'], $field['x'], $field['y'], function($font) use ($field) {
    //                    // قد تحتاج إلى تعديل مسار الخط ليتناسب مع مشروعك
    //                    $fontPath = public_path('fonts/' . $field['font'] . '.ttf');
    //                    if (file_exists($fontPath)) {
    //                        $font->file($fontPath);
    //                    } else {
    //                        $font->file(public_path('fonts/arial.ttf')); // استخدام خط احتياطي
    //                    }
    //
    //                    $font->size($field['size']);
    //                    $font->color($field['color']);
    //                    $font->align($field['align']);
    //                    $font->valign('top');
    //                });
    //            }
    //
    //            // التحقق من وجود قالب الوجه الخلفي وإضافته
    //            if ($backgroundPathBack) {
    //                $backImage = $manager->read($backgroundPathBack);
    //
    //                $image->scale(width: $canvasWidth); // يتم تصغير الصورة الأمامية إلى العرض المطلوب
    //                $backImage->scale(width: $canvasWidth); // يتم تصغير الصورة الخلفية إلى نفس العرض
    //                $image->place($backImage, 'bottom');
    //
    //                // إضافة حقول النص على الوجه الخلفي
    //                foreach ($backRenderedFields as $field) {
    //                    // y_position of the back-side fields should be shifted by the height of the front-side image
    //                    $yPosition = $field['y'] + $canvasHeight;
    //                    $image->text($field['text'], $field['x'], $yPosition, function($font) use ($field) {
    //                        $fontPath = public_path('fonts/' . $field['font'] . '.ttf');
    //                        if (file_exists($fontPath)) {
    //                            $font->file($fontPath);
    //                        } else {
    //                            $font->file(public_path('fonts/arial.ttf')); // استخدام خط احتياطي
    //                        }
    //
    //                        $font->size($field['size']);
    //                        $font->color($field['color']);
    //                        $font->align($field['align']);
    //                        $font->valign('top');
    //                    });
    //                }
    //            }
    //
    //
    //            // تحديد مسار الصورة وحفظها
    //            $imagePath = "certificates/" . auth()->id() . "/" . uniqid() . ".jpg";
    //            $imageFullPath = storage_path("app/public/{$imagePath}");
    //            Storage::disk('public')->makeDirectory("certificates/" . auth()->id());
    //            $image->save($imageFullPath);
    //
    //            $document = $this->documentRepository->create([
    //                'file_path' => $imagePath,
    //                'document_template_id' => $template->id,
    //                'recipient_id' => $recipient->id,
    //                'valid_from' => $template->valid_from,
    //                'valid_until' => $template->valid_until,
    //            ]);
    //
    //            $this->dispatchCertificate($document, $template->send_at);
    //        }
    //    }

// أضف $documentUuid كمعامل جديد
    public function generateDocuments(DocumentTemplate $template, array $recipients, $templateDataFile, $canvasWidth, $canvasHeight, string $certificateTextData = ''): void
    {
        $frontTemplate = $template->templateFiles()->where('side', 'front')->first();
        $backTemplate = $template->templateFiles()->where('side', 'back')->first();

        if (! $frontTemplate) {
            throw new Exception('Front document template not found');
        }

        $documentDataImport = new DocumentDataImport;
        Excel::import($documentDataImport, $templateDataFile);
        $documentRows = $documentDataImport->rows;

        if (count($documentRows) < count($recipients)) {
            throw new Exception('Not enough data rows in Excel for all recipients');
        }

        $backgroundPathFront = storage_path('app/public/'.$frontTemplate->file_path);
        if (! file_exists($backgroundPathFront)) {
            throw new Exception("Front background file not found: $backgroundPathFront");
        }

        $backgroundPathBack = null;
        if ($backTemplate) {
            $backgroundPathBack = storage_path('app/public/'.$backTemplate->file_path);
            if (! file_exists($backgroundPathBack)) {
                throw new Exception("Back background file not found: $backgroundPathBack");
            }
        }

        $totalHeight = $backTemplate ? $canvasHeight * 2 : $canvasHeight;
        $manager = new ImageManager(new Driver);

        $frontTextData = [];
        $frontQrCodesData = [];

        if (! empty($certificateTextData)) {
            $parsedTextData = json_decode($certificateTextData, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $frontTextData = $parsedTextData['document_template_file_path[]-front']['texts'] ?? [];
                $frontQrCodesData = $parsedTextData['document_template_file_path[]-front']['qrCodes'] ?? [];

                $canvasWidth = $parsedTextData['document_template_file_path[]-front']['canvasWidth'] ?? $canvasWidth;
                $canvasHeight = $parsedTextData['document_template_file_path[]-front']['canvasHeight'] ?? $canvasHeight;
            }
        }

        foreach ($recipients as $index => $recipient) {
            $dataRow = $documentRows[$index] ?? [];

            // ***** إنشاء UUID و Unique Code لكل مستلم هنا *****
            $currentDocumentUuid = Str::uuid()->toString();
            $currentUniqueCode = Str::random(10); // إنشاء unique_code هنا

            $frontRenderedFields = [];
            if (! empty($frontTextData)) {
                foreach ($frontTextData as $jsonField) {
                    $placeholder = $jsonField['text'];
                    $value = $dataRow[$placeholder] ?? $placeholder;
                    $frontRenderedFields[] = [
                        'text' => $value,
                        'x' => $jsonField['left'],
                        'y' => $jsonField['top'],
                        'font' => $jsonField['fontFamily'],
                        'size' => $jsonField['fontSize'],
                        'color' => $jsonField['fill'],
                        'align' => 'left',
                        'rotation' => $jsonField['angle'],
                        'baseline' => $jsonField['textBaseline'],
                    ];
                }
            } else {
                $frontFields = $template->fields()->where('side', 'front')->get();
                foreach ($frontFields as $field) {
                    $value = $dataRow[$field->field_key] ?? $field->field_key;
                    $frontRenderedFields[] = [
                        'text' => $value,
                        'x' => $field->position_x,
                        'y' => $field->position_y,
                        'font' => $field->font_family,
                        'size' => $field->font_size,
                        'color' => $field->font_color,
                        'align' => $field->text_align,
                        'weight' => $field->font_weight,
                        'rotation' => $field->rotation,
                    ];
                }
            }

            $backRenderedFields = [];
            $backFields = $template->fields()->where('side', 'back')->get();
            foreach ($backFields as $field) {
                $value = $dataRow[$field->field_key] ?? $field->field_key;
                $backRenderedFields[] = [
                    'text' => $value,
                    'x' => $field->position_x,
                    'y' => $field->position_y,
                    'font' => $field->font_family,
                    'size' => $field->font_size,
                    'color' => $field->font_color,
                    'align' => $field->text_align,
                    'weight' => $field->font_weight,
                    'rotation' => $field->rotation,
                ];
            }

            $image = $manager->read($backgroundPathFront);
            $image->resize($canvasWidth, $canvasHeight);

            foreach ($frontRenderedFields as $field) {
                $image->text($field['text'], $field['x'], $field['y'], function ($font) use ($field) {
                    $fontPath = public_path('fonts/'.$field['font'].'.ttf');
                    if (file_exists($fontPath)) {
                        $font->file($fontPath);
                    } else {
                        $font->file(public_path('fonts/arial.ttf'));
                    }
                    $font->size($field['size']);
                    $font->color($field['color']);
                    $font->align($field['align']);
                    $font->valign($field['baseline'] ?? 'top');
                    $font->angle($field['rotation']);
                });
            }

            // --- إضافة QR Codes هنا ---
            if (! empty($frontQrCodesData)) {
                foreach ($frontQrCodesData as $qrCodeData) {
                    $qrCodeLink = route('documents.verify', $currentDocumentUuid);

                    $options = new QROptions([
                        'version'    => QRCode::VERSION_AUTO,
                        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                        'scale'      => 10,
                        'imageTransparent' => false,
                        'bgColor' => [255, 255, 255],
                        'fgColor' => [0, 0, 0],
                    ]);

                    $qrCodeGenerator = new QRCode($options);
                    $qrCodeImageBase64 = $qrCodeGenerator->render($qrCodeLink);

                    $qrImage = $manager->read($qrCodeImageBase64);

                    $qrWidth = $qrCodeData['width'] ?? 150;
                    $qrHeight = $qrCodeData['height'] ?? 150;

                    $qrImage->resize($qrWidth, $qrHeight);

                    $qrCodeX = (int) $qrCodeData['left'];
                    $qrCodeY = (int) $qrCodeData['top'];

                    $image->place(
                        $qrImage,
                        'top-left',
                        $qrCodeX,
                        $qrCodeY
                    );

                    // ***** إضافة الـ unique_code تحت الـ QR code مباشرةً *****
                    $textForUniqueCode = "Code: " . $currentUniqueCode; // النص الذي سيظهر

                    // تحديد حجم الخط ومسافة الهامش
                    $fontSize = 14; // حجم الخط لـ unique_code
                    $margin = 5;    // مسافة بين الـ QR code والنص

                    // حساب موضع النص (y) أسفل الـ QR code
                    // $qrCodeY هو بداية الـ QR code من الأعلى
                    // $qrHeight هو ارتفاع الـ QR code
                    $uniqueCodeY = $qrCodeY + $qrHeight + $margin;

                    // حساب موضع النص (x) ليكون في منتصف الـ QR code
                    // $qrCodeX هو بداية الـ QR code من اليسار
                    // $qrWidth هو عرض الـ QR code
                    $uniqueCodeX = $qrCodeX + ($qrWidth / 2); // لمنتصف الـ QR code

                    $image->text($textForUniqueCode, $uniqueCodeX, $uniqueCodeY, function ($font) use ($fontSize) {
                        $font->file(public_path('fonts/arial.ttf')); // استخدم خطًا احتياطيًا أو حدد خطًا آخر
                        $font->size($fontSize);
                        $font->color('#000000'); // لون أسود
                        $font->align('center'); // ليكون النص في المنتصف أفقيا
                        $font->valign('top'); // لتحديد نقطة بداية النص من الأعلى
                    });
                }
            }
            // --- نهاية إضافة QR Codes ---

            if ($backgroundPathBack) {
                $backImage = $manager->read($backgroundPathBack);
                $backImage->resize($canvasWidth, $canvasHeight);
                $image->resizeCanvas($canvasWidth, $totalHeight, 'top-left', false, false);
                $image->place($backImage, 'top-left', 0, $canvasHeight);

                foreach ($backRenderedFields as $field) {
                    $yPosition = $field['y'] + $canvasHeight;
                    $image->text($field['text'], $field['x'], $yPosition, function ($font) use ($field) {
                        $fontPath = public_path('fonts/'.$field['font'].'.ttf');
                        if (file_exists($fontPath)) {
                            $font->file($fontPath);
                        } else {
                            $font->file(public_path('fonts/arial.ttf'));
                        }
                        $font->size($field['size']);
                        $font->color($field['color']);
                        $font->align($field['align']);
                        $font->valign('top');
                        $font->angle($field['rotation']);
                    });
                }
            }

            $imagePath = 'certificates/'.auth()->id().'/'.uniqid().'.jpg';
            $imageFullPath = storage_path("app/public/{$imagePath}");
            Storage::disk('public')->makeDirectory('certificates/'.auth()->id());
            $image->save($imageFullPath);

            // ***** تمرير الـ UUID و Unique Code إلى DocumentRepository::create *****
            $documentResult = $this->documentRepository->create([
                'file_path' => $imagePath,
                'document_template_id' => $template->id,
                'recipient_id' => $recipient->id,
                'valid_from' => $template->valid_from,
                'valid_until' => $template->valid_until,
            ], $currentDocumentUuid, $currentUniqueCode); // هنا نمرر الـ uuid والـ unique_code

            $document = $documentResult['document'];

            $this->dispatchCertificate($document, $template->send_at);
        }
    }


    public function generateAttendanceDocuments(AttendanceTemplate $template, array $recipients, $templateDataFile): void
    {
        $frontTemplate = $template->templateFiles()->where('side', 'front')->first();
        $backTemplate = $template->templateFiles()->where('side', 'back')->first();

        if (! $frontTemplate) {
            throw new Exception('Front attendance template not found');
        }

        // استيراد بيانات الإكسيل
        $attendanceDataImport = new DocumentDataImport;
        Excel::import($attendanceDataImport, $templateDataFile);
        $attendanceDataRows = $attendanceDataImport->rows;

        //        dd($attendanceDataRows);

        // التحقق من أن عدد الصفوف يطابق عدد المستلمين
        if (count($attendanceDataRows) < count($recipients)) {
            throw new Exception('Not enough data rows in Excel for all attendance recipients');
        }

        $canvasWidth = 900;
        $canvasHeight = 600;
        $totalHeight = $backTemplate ? $canvasHeight * 2 : $canvasHeight;

        $backgroundPathFront = storage_path('app/public/'.$frontTemplate->file_path);
        $resizedBackgroundPathFront = storage_path('app/public/templates/resized_'.basename($frontTemplate->file_path));

        if (! file_exists($backgroundPathFront)) {
            throw new Exception("Front background file not found: $backgroundPathFront");
        }

        $image = Image::read($backgroundPathFront)->resize(900, 600, function ($constraint) {
            $constraint->aspectRatio();
        })->save($resizedBackgroundPathFront);

        $backgroundPathBack = null;
        $resizedBackgroundPathBack = null;
        if ($backTemplate) {
            $backgroundPathBack = storage_path('app/public/'.$backTemplate->file_path);
            $resizedBackgroundPathBack = storage_path('app/public/templates/resized_'.basename($backTemplate->file_path));

            if (! file_exists($backgroundPathBack)) {
                throw new Exception("Back background file not found: $backgroundPathBack");
            }

            $image = Image::read($backgroundPathBack)->resize(900, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($resizedBackgroundPathBack);
        }

        // ربط كل مستلم بصف من الإكسيل
        foreach ($recipients as $index => $recipient) {
            $dataRow = $attendanceDataRows[$index] ?? []; // أخذ الصف المطابق للمستلم
            $frontFields = $template->fields()->where('side', 'front')->get();
            $backFields = $template->fields()->where('side', 'back')->get();

            $frontRenderedFields = [];
            foreach ($frontFields as $field) {
                $value = $dataRow[$field->field_key] ?? ''; // استخدام القيمة من الصف
                $frontRenderedFields[] = [
                    'text' => $value,
                    'x' => $field->position_x,
                    'y' => $field->position_y,
                    'font' => $field->font_family,
                    'size' => $field->font_size,
                    'color' => $field->font_color,
                    'align' => $field->text_align,
                    'weight' => $field->font_weight,
                    'rotation' => $field->rotation,
                ];
            }

            $backRenderedFields = [];
            foreach ($backFields as $field) {
                $value = $dataRow[$field->field_key] ?? ''; // استخدام القيمة من الصف
                $backRenderedFields[] = [
                    'text' => $value,
                    'x' => $field->position_x,
                    'y' => $field->position_y,
                    'font' => $field->font_family,
                    'size' => $field->font_size,
                    'color' => $field->font_color,
                    'align' => $field->text_align,
                    'weight' => $field->font_weight,
                    'rotation' => $field->rotation,
                ];
            }

            $pdfPath = 'attendance_certificates/'.auth()->id().'/'.uniqid().'.pdf';
            $pdfFullPath = storage_path("app/public/{$pdfPath}");
            Storage::disk('public')->makeDirectory('attendance_certificates/'.auth()->id());

            $pdf = Pdf::loadView('pdf.template', [
                'backgroundFront' => $resizedBackgroundPathFront,
                'backgroundBack' => $resizedBackgroundPathBack,
                'frontFields' => $frontRenderedFields,
                'backFields' => $backRenderedFields,
                'canvasWidth' => $canvasWidth,
                'canvasHeight' => $canvasHeight,
                'hasBack' => $backTemplate ? true : false,
            ])->setPaper([0, 0, $canvasWidth, $totalHeight], 'px');

            $pdf->save($pdfFullPath);

            $attendanceDocument = $this->attendanceDocumentRepository->create([
                'file_path' => $pdfPath,
                'attendance_template_id' => $template->id,
                'recipient_id' => $recipient->id,
                'valid_from' => $template->valid_from,
                'valid_until' => $template->valid_until,
            ]);

            $this->dispatchCertificate($attendanceDocument, $template->send_at);
        }
    }

    protected function dispatchCertificate($certificate, $sendAt): void
    {
        SendCertificateJob::dispatch($certificate)->delay($sendAt);
    }
}
