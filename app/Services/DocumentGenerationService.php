<?php

namespace App\Services;

use App\Imports\DocumentDataImport;
use App\Jobs\GenerateAttendanceDocumentJob;
use App\Jobs\GenerateDocumentsJob;
use App\Jobs\SendCertificateJob;
use App\Models\AttendanceTemplate;
use App\Models\DocumentTemplate;
use App\Repositories\Eventor\AttendanceDocumentRepository;
use App\Repositories\Eventor\DocumentRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Maatwebsite\Excel\Facades\Excel;

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

    /**
     * @throws Exception
     */
    public function generateDocuments(DocumentTemplate $template,
                                      array $recipients,
                                                       $templateDataFile,
                                                       $canvasWidth,
                                                       $canvasHeight,
                                      string $certificateTextData = ''): void
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

        $frontTextData = [];
        $frontQrCodesData = [];
        // **[تعديل: إضافة متغير لبيانات الخلفي إذا لزم الأمر]**
        $backTextData = [];
        $backQrCodesData = [];

        if (! empty($certificateTextData)) {
            $parsedTextData = json_decode($certificateTextData, true);
            $frontKey = 'document_template_file_path[]-front';
            $backKey = 'document_template_file_path[]-back';

            if (json_last_error() === JSON_ERROR_NONE) {

                // 1. استخلاص بيانات الأمامي
                $frontTextData = $parsedTextData[$frontKey]['texts'] ?? [];
                $frontQrCodesData = $parsedTextData[$frontKey]['qrCodes'] ?? [];

                $canvasWidth = $parsedTextData[$frontKey]['canvasWidth'] ?? $canvasWidth;
                $canvasHeight = $parsedTextData[$frontKey]['canvasHeight'] ?? $canvasHeight;

                // 2. 💥 التعديل الحاسم: استخلاص ودمج بيانات الوجه الخلفي
                if (isset($parsedTextData[$backKey])) {
                    $backQrCodesData = $parsedTextData[$backKey]['qrCodes'] ?? [];
                    $backTextData = $parsedTextData[$backKey]['texts'] ?? [];

                    // دمج مصفوفات QR Code لجميع الأوجه في مصفوفة واحدة (لإرسالها للـ Job)
                    $frontQrCodesData = array_merge($frontQrCodesData, $backQrCodesData);
                }
            }
        }

        // ملاحظة: الـ Job الحالي لديك يستخدم فقط $frontTextData و $frontQrCodesData للنصوص/الرموز المُعدَّلة.
        // يفضل إرسال نصوص الخلفي أيضاً في وسيط خاص بالـ Job إذا كنت تريد تجاوز نصوص الـ DB للخلفي.

        foreach ($recipients as $index => $recipient) {
            $dataRow = $documentRows[$index] ?? [];

            // إرسال الـ Job إلى الـ Queue
            GenerateDocumentsJob::dispatch(
                $template,
                $recipient,
                $dataRow,
                $frontTextData,
                $frontQrCodesData, // ⬅️ الآن تحتوي على QR Codes من الأمامي والخلفي
                $backgroundPathFront,
                $backgroundPathBack,
                $canvasWidth,
                $canvasHeight
            );
        }
    }


    /**
     * @throws Exception
     */
    public function generateAttendanceDocuments(
        AttendanceTemplate $template,
        array $recipients,
                           $templateDataFile,
        $attendanceCanvasWidth,
        $attendanceCanvasHeight,
        string $attendanceTextData = ''
    ): void {
        $frontTemplate = $template->templateFiles()->where('side', 'front')->first();
        $backTemplate = $template->templateFiles()->where('side', 'back')->first();

        if (! $frontTemplate) {
            throw new Exception('Front attendance template not found');
        }

        // 1. استيراد بيانات الإكسيل
        $attendanceDataImport = new DocumentDataImport;
        Excel::import($attendanceDataImport, $templateDataFile);
        $attendanceDataRows = $attendanceDataImport->rows;

        // 2. التحقق من عدد الصفوف
        if (count($attendanceDataRows) < count($recipients)) {
            throw new Exception('Not enough data rows in Excel for all attendance recipients');
        }

        // 4. معالجة الخلفية الأمامية
        $manager = new ImageManager(new Driver);


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



        $frontTextData = [];
        $frontQrCodesData = [];
        // **[تعديل: إضافة متغير لبيانات الخلفي إذا لزم الأمر]**
        $backTextData = [];
        $backQrCodesData = [];

        if (! empty($attendanceTextData)) {
            $parsedTextData = json_decode($attendanceTextData, true);
            $frontKey = 'attendance_template_data_file_path-front';
            $backKey = 'attendance_template_data_file_path-back';

            if (json_last_error() === JSON_ERROR_NONE) {

                // 1. استخلاص بيانات الأمامي
                $frontTextData = $parsedTextData[$frontKey]['texts'] ?? [];
                $frontQrCodesData = $parsedTextData[$frontKey]['qrCodes'] ?? [];

                $canvasWidth = $parsedTextData[$frontKey]['canvasWidth'] ?? $attendanceCanvasWidth;
                $canvasHeight = $parsedTextData[$frontKey]['canvasHeight'] ?? $attendanceCanvasHeight;

                // 2. 💥 التعديل الحاسم: استخلاص ودمج بيانات الوجه الخلفي
                if (isset($parsedTextData[$backKey])) {
                    $backQrCodesData = $parsedTextData[$backKey]['qrCodes'] ?? [];
                    $backTextData = $parsedTextData[$backKey]['texts'] ?? [];

                    // دمج مصفوفات QR Code لجميع الأوجه في مصفوفة واحدة (لإرسالها للـ Job)
                    $frontQrCodesData = array_merge($frontQrCodesData, $backQrCodesData);
                }
            }
        }

        if (empty($frontQrCodesData)) {
            Log::warning('FRONTEND_DATA_ISSUE: No QR code data found in the provided attendanceTextData.');
        } else {
            Log::info('FRONTEND_DATA_VALID: ' . count($frontQrCodesData) . ' QR codes found and will be dispatched to the job.');
        }


//        --------------------------------------------
        // 6. إرسال Jobs لكل مستلم
        foreach ($recipients as $index => $recipient) {
            $dataRow = $attendanceDataRows[$index] ?? [];

            GenerateAttendanceDocumentJob::dispatch(
                $template,
                $recipient,
                $dataRow,
                $frontTextData,
                $frontQrCodesData, // ⬅️ الآن تحتوي على QR Codes من الأمامي والخلفي
                $backgroundPathFront,
                $backgroundPathBack,
                $attendanceCanvasWidth,
                $attendanceCanvasHeight
            );
        }
    }



    public function dispatchCertificate($certificate, $sendAt): void
    {
        SendCertificateJob::dispatch($certificate)->delay($sendAt);
    }
}
