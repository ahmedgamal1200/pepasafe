<?php

namespace App\Jobs;

use AllowDynamicProperties;
use App\Services\DocumentGenerationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DocumentTemplate;
use App\Models\Recipient;
use App\Repositories\Eventor\DocumentRepository;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[AllowDynamicProperties]
class GenerateDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected DocumentTemplate $template;
    protected Recipient $recipient;
    protected array $dataRow;
    protected array $frontTextData;
    protected array $frontQrCodesData;
    protected string $certificateTextData;
    protected string $backgroundPathFront;
    protected ?string $backgroundPathBack;
    protected int $canvasWidth;
    protected int $canvasHeight;


    /**
     * Create a new job instance.
     */
    public function __construct(
        DocumentTemplate $template,
        Recipient $recipient,
        array $dataRow,
        array $frontTextData,
        array $frontQrCodesData,
        string $backgroundPathFront,
        ?string $backgroundPathBack,
        int $canvasWidth,
        int $canvasHeight,
    ) {
        $this->template = $template;
        $this->recipient = $recipient;
        $this->dataRow = $dataRow;
        $this->frontTextData = $frontTextData;
        $this->frontQrCodesData = $frontQrCodesData;
        $this->backgroundPathFront = $backgroundPathFront;
        $this->backgroundPathBack = $backgroundPathBack;
        $this->canvasWidth = $canvasWidth;
        $this->canvasHeight = $canvasHeight;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(DocumentRepository $documentRepository, DocumentGenerationService $service): void
    {
        // === التحقق الأمني من القالب (لحل مشكلة fields() on null) ===
        if (! $this->template) {
            \Log::error("MISSING_TEMPLATE_ERROR: DocumentTemplate is null when running GenerateDocumentsJob. Job aborted.", [
                'recipient_id' => $this->recipient->id ?? 'N/A',
            ]);
            return;
        }

        $manager = new ImageManager(new Driver);

        $currentDocumentUuid = Str::uuid()->toString();
        $currentUniqueCode = Str::random(10);

        \Log::info("DEBUG: Generated Unique Code: " . $currentUniqueCode); // 👈 (المفترض VhdI1VQKai)


        // 1. **منطق تجهيز البيانات (يبقى كما هو)**
        $frontRenderedFields = [];
        if (! empty($this->frontTextData)) {
            foreach ($this->frontTextData as $jsonField) {
                $placeholder = $jsonField['text'];
                $value = $this->dataRow[$placeholder] ?? $placeholder;
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
            $frontFields = $this->template->fields()->where('side', 'front')->get();
            foreach ($frontFields as $field) {
                $value = $this->dataRow[$field->field_key] ?? $field->field_key;
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
        $backFields = $this->template->fields()->where('side', 'back')->get();
        foreach ($backFields as $field) {
            $value = $this->dataRow[$field->field_key] ?? $field->field_key;
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


        // 2. **منطق قراءة وتجهيز الصور ودمجها (التعديل باستخدام 'create' لـ v3)**
        $frontImage = null;
        $image = null;

        try {
            // أ. قراءة الصورة الأمامية وتغيير حجمها
//            $frontImage = $manager->read($this->backgroundPathFront);
//            $frontImage->resize($this->canvasWidth, $this->canvasHeight);
//             أ. قراءة الصورة الأمامية وتغيير حجمها
//            $frontImage = $manager->read($this->backgroundPathFront);
//            $frontImage->scaleDown(width: $this->canvasWidth, height: $this->canvasHeight);
////
//            $newFrontImageCanvas = $manager->create($this->canvasWidth, $this->canvasHeight, 'transparent');
//            $newFrontImageCanvas->place($frontImage, 'center');
//
//            $frontImage = $newFrontImageCanvas;
            $frontImage = $manager->read($this->backgroundPathFront);
            $frontImage->cover($this->canvasWidth, $this->canvasHeight);

// يجب أن تتأكد أيضاً من حفظ الصورة النهائية كـ PNG أو WEBP
// لكي يتم دعم الشفافية في الملف المُولَّد. // الآن frontImage هو الكانفاس الجديد

            // ب. تحديد الكانفاس النهائي
            if ($this->backgroundPathBack) {
                // دمج صورتين
                // 1. قراءة الصورة الخلفية وتغيير حجمها
//                $backImage = $manager->read($this->backgroundPathBack);
//                $backImage->resize($this->canvasWidth, $this->canvasHeight);
                // 1. قراءة الصورة الخلفية وتغيير حجمها
                $backImage = $manager->read($this->backgroundPathBack);
//                $backImage->scaleDown(width: $this->canvasWidth, height: $this->canvasHeight);
//
//                $newBackImageCanvas = $manager->create($this->canvasWidth, $this->canvasHeight, '#FFFFFF'); // #FFFFFF هو لون الخلفية (أبيض)
//                $newBackImageCanvas->place($backImage, 'center');
//
//                $backImage = $newBackImageCanvas; // الآن backImage هو الكانفاس الجديد
                $backImage->cover($this->canvasWidth, $this->canvasHeight);

                // 2. **التعديل هنا:** استخدام create بدلاً من canvas
                $combinedImage = $manager->create(
                    $this->canvasWidth,
                    $this->canvasHeight * 2,
                    '#FFFFFF'
                );

                // 3. وضع الصورة الأمامية (المعدلة الحجم) في الجزء العلوي
                $combinedImage->place($frontImage, 'top-left', 0, 0);

                // 4. وضع الصورة الخلفية في الجزء السفلي
                $combinedImage->place($backImage, 'top-left', 0, $this->canvasHeight);

                $image = $combinedImage; // تعيين الكانفاس المدمج كصورة نهائية

            } else {
                // وجه واحد فقط
                $image = $frontImage;
            }

        } catch (DecoderException $e) {
            \Log::error("IMAGE_DECODE_ERROR_MERGE: Failed to decode one of the background images during merge process. Error: {$e->getMessage()}", [
                'front_path' => $this->backgroundPathFront,
                'back_path' => $this->backgroundPathBack ?? 'N/A'
            ]);
            throw new Exception("Image processing failed at initial merge phase. Verify background image files.");
        } catch (Exception $e) {
            \Log::error("GENERAL_IMAGE_PROCESSING_ERROR_MERGE: An unexpected error occurred during image merging. Error: {$e->getMessage()}", [
                'front_path' => $this->backgroundPathFront,
                'back_path' => $this->backgroundPathBack ?? 'N/A'
            ]);
            throw $e;
        }


        // 4. **رسم نصوص الوجه الأمامي**
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


        // 5. **إضافة QR Codes (مع التحقق)**
        if (! empty($this->frontQrCodesData)) {
            foreach ($this->frontQrCodesData as $qrCodeData) {
                $qrCodeLink = route('documents.show', $currentDocumentUuid);

                $options = new QROptions([
                    'version'    => QRCode::VERSION_AUTO,
                    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                    'scale'      => 10,
                    'imageTransparent' => true,
                    'bgColor' => [255, 255, 255, 0],
                    'fgColor' => [0, 0, 0],
                    'addQuietzone' => false,
                ]);

                $qrCodeGenerator = new QRCode($options);
                $qrCodeImageBase64 = $qrCodeGenerator->render($qrCodeLink);

                try {
                    $qrImage = $manager->read($qrCodeImageBase64);
                } catch (DecoderException $e) {
                    \Log::error("QR_CODE_ISSUE: Failed to decode QR Code image. Error: {$e->getMessage()}");
                    throw new Exception("Failed to generate or decode QR code image.");
                } catch (Exception $e) {
                    \Log::error("Error processing QR Code. Error: {$e->getMessage()}");
                    throw $e;
                }

                $qrWidth = $qrCodeData['width'] ?? 150;
                $qrHeight = $qrCodeData['height'] ?? 150;

                $qrImage->resize($qrWidth, $qrHeight);

                $qrCodeX = (int) $qrCodeData['left'];
                $qrCodeY = (int) $qrCodeData['top'];

                // تحديد الإزاحة (Y offset)
                $yOffset = 0;
                $qrSubtype = $qrCodeData['subtype'] ?? 'certificate-front';

                if ($qrSubtype === 'certificate-back' && $this->backgroundPathBack) {
                    $yOffset = $this->canvasHeight;

                    // *** التحقق من إحداثيات QR code للوجه الخلفي ***
                    if ($qrCodeY >= $this->canvasHeight || $qrCodeX >= $this->canvasWidth) {
                        \Log::error("FRONTEND_COORDINATE_ISSUE: QR Code for back face has out-of-bounds 'top' or 'left' coordinate. Expected 0-{$this->canvasHeight} (for Y) and 0-{$this->canvasWidth} (for X) within its own canvas. Received Y: {$qrCodeY}, X: {$qrCodeX}", ['qrCodeData' => $qrCodeData]);
                        throw new Exception("Frontend QR code data for back face has invalid coordinates.");
                    }
                }

                $finalQrCodeY = $qrCodeY + $yOffset;

                $scanMeText = "SCAN ME!";
                $scanMeFontSize = 12;
                $scanMeMargin = 5;

                // تطبيق الإزاحة على نص "SCAN ME!"
                $scanMeX = $qrCodeX + ($qrWidth / 2);
                $scanMeY = $finalQrCodeY - $scanMeMargin;

                $image->text($scanMeText, $scanMeX, $scanMeY, function ($font) use ($scanMeFontSize) {
                    $font->file(public_path('fonts/arial.ttf'));
                    $font->size($scanMeFontSize);
                    $font->color('#000000');
                    $font->align('center');
                    $font->valign('bottom');
                });

                $image->place(
                    $qrImage,
                    'top-left',
                    $qrCodeX,
                    $finalQrCodeY
                );

                $textForUniqueCode = "Code:" . $currentUniqueCode;

                $fontSize = 12;
                $margin = 5;

                // تطبيق الإزاحة على نص "Code"
                $uniqueCodeY = $finalQrCodeY + $qrHeight + $margin;
                $uniqueCodeX = $qrCodeX + ($qrWidth / 2);

                $image->text($textForUniqueCode, $uniqueCodeX, $uniqueCodeY, function ($font) use ($fontSize) {
                    $font->file(public_path('fonts/arial.ttf'));
                    $font->size($fontSize);
                    $font->color('#000000');
                    $font->align('center');
                    $font->valign('top');
                });
            }
        }
        // --- نهاية إضافة QR Codes ---

        // 6. **رسم نصوص الوجه الخلفي (مع الإزاحة)**
        // ...
        // 6. **رسم نصوص الوجه الخلفي (مع الإزاحة)**
        if ($this->backgroundPathBack) {
            foreach ($backRenderedFields as $field) {

                // *** تم إزالة كتلة التحقق الصارم التي كانت تسبب الخطأ عند السطر 304 ***
                // *** $field['y'] تأتي حاليًا بقيمة مطلقة (مثل 1014)، وهي خارج حدود الوجه الواحد (384).
                // *** سنعتمد على منطق الإزاحة التالي للتعامل مع كلتا الحالتين.

                $yPosition = $field['y'];

                // إذا كان الإحداثي Y المرسل من FE يبدأ من الصفر (إحداثي نسبي)، نقوم بإضافة الإزاحة
                // إذا كان أقل من ارتفاع الوجه الواحد، فهذا يعني أنه نسبي ويحتاج إلى إزاحة.
                if ($yPosition < $this->canvasHeight) {
                    $yPosition = $field['y'] + $this->canvasHeight;
                }
                // وإلا (إذا كان أكبر من canvasHeight، مثل 1014)، فسيتم استخدامه كإحداثي مطلق كما هو.

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

        // 7. **حفظ المستند (يبقى في النهاية)**
        $imagePath = 'certificates/'.auth()->id().'/'.uniqid().'.png';
        $imageFullPath = storage_path("app/public/{$imagePath}");
        Storage::disk('public')->makeDirectory('certificates/'.auth()->id());
        $image->save($imageFullPath);

        $documentResult = $documentRepository->create([
            'file_path' => $imagePath,
            'document_template_id' => $this->template->id,
            'recipient_id' => $this->recipient->id,
            'valid_from' => $this->template->valid_from,
            'valid_until' => $this->template->valid_until,
        ], $currentDocumentUuid, $currentUniqueCode);




        $document = $documentResult['document'];

        // إذا كان لديك dispatchCertificate()، قم باستدعائها هنا

        $service->dispatchCertificate($document, $this->template->send_at);


    }
}
