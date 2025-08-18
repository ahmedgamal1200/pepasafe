<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\AttendanceTemplate;
use App\Repositories\Eventor\AttendanceDocumentRepository;
use App\Repositories\Eventor\DocumentRepository;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
//use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Laravel\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DocumentDataImport;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


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
////        dd($documentRows);
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
////            dd($frontFields->pluck('field_key')->toArray());
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
    public function generateDocuments(DocumentTemplate $template, array $recipients, $templateDataFile, $canvasWidth, $canvasHeight): void
    {
        $frontTemplate = $template->templateFiles()->where('side', 'front')->first();
        $backTemplate = $template->templateFiles()->where('side', 'back')->first();

        if (!$frontTemplate) {
            throw new \Exception('Front document template not found');
        }

        $documentDataImport = new DocumentDataImport();
        Excel::import($documentDataImport, $templateDataFile);
        $documentRows = $documentDataImport->rows;

        if (count($documentRows) < count($recipients)) {
            throw new \Exception('Not enough data rows in Excel for all recipients');
        }

        $backgroundPathFront = storage_path('app/public/' . $frontTemplate->file_path);
        if (!file_exists($backgroundPathFront)) {
            throw new \Exception("Front background file not found: $backgroundPathFront");
        }

        $backgroundPathBack = null;
        if ($backTemplate) {
            $backgroundPathBack = storage_path('app/public/' . $backTemplate->file_path);
            if (!file_exists($backgroundPathBack)) {
                throw new \Exception("Back background file not found: $backgroundPathBack");
            }
        }

        $totalHeight = $backTemplate ? $canvasHeight * 2 : $canvasHeight;

        foreach ($recipients as $index => $recipient) {
            $dataRow = $documentRows[$index] ?? [];
            $frontFields = $template->fields()->where('side', 'front')->get();
            $backFields = $template->fields()->where('side', 'back')->get();

            $frontRenderedFields = [];
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

            $backRenderedFields = [];
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

            $pdf = Pdf::loadView('pdf.template', [
                'backgroundFront' => $backgroundPathFront,
                'backgroundBack' => $backgroundPathBack,
                'frontFields' => $frontRenderedFields,
                'backFields' => $backRenderedFields,
                'canvasWidth' => $canvasWidth,
                'canvasHeight' => $canvasHeight,
                'hasBack' => $backTemplate ? true : false,
            ])->setPaper([0, 0, $canvasWidth, $totalHeight], 'px');

            $pdfPath = "certificates/" . auth()->id() . "/" . uniqid() . ".pdf";
            $pdfFullPath = storage_path("app/public/{$pdfPath}");
            Storage::disk('public')->makeDirectory("certificates/" . auth()->id());
            $pdf->save($pdfFullPath);

            $document = $this->documentRepository->create([
                'file_path' => $pdfPath,
                'document_template_id' => $template->id,
                'recipient_id' => $recipient->id,
                'valid_from' => $template->valid_from,
                'valid_until' => $template->valid_until,
            ]);

//            dd($result);

//            $document = $result['document'];
//            $qrPath = $result['qrPath'];
//
//
//            $pdf = Pdf::loadView('pdf.template', [
//                'backgroundFront' => $backgroundPathFront,
//                'backgroundBack' => $backgroundPathBack,
//                'frontFields' => $frontRenderedFields,
//                'backFields' => $backRenderedFields,
//                'canvasWidth' => $canvasWidth,
//                'canvasHeight' => $canvasHeight,
//                'hasBack' => $backTemplate ? true : false,
//                'qrPath' => $qrPath, // أضف $qrPath كمتغير صريح
//                'qrCodeUrl' => url("storage/{$qrPath}"), // احتفظ بالـ URL
//            ])->setPaper([0, 0, $canvasWidth, $totalHeight], 'px');
//
//            $pdf->save($pdfFullPath);

            $this->dispatchCertificate($document, $template->send_at);
        }
    }



    public function generateAttendanceDocuments(AttendanceTemplate $template, array $recipients, $templateDataFile): void
    {
        $frontTemplate = $template->templateFiles()->where('side', 'front')->first();
        $backTemplate = $template->templateFiles()->where('side', 'back')->first();

        if (!$frontTemplate) {
            throw new \Exception('Front attendance template not found');
        }

        // استيراد بيانات الإكسيل
        $attendanceDataImport = new DocumentDataImport();
        Excel::import($attendanceDataImport, $templateDataFile);
        $attendanceDataRows = $attendanceDataImport->rows;

//        dd($attendanceDataRows);

        // التحقق من أن عدد الصفوف يطابق عدد المستلمين
        if (count($attendanceDataRows) < count($recipients)) {
            throw new \Exception('Not enough data rows in Excel for all attendance recipients');
        }

        $canvasWidth = 900;
        $canvasHeight = 600;
        $totalHeight = $backTemplate ? $canvasHeight * 2 : $canvasHeight;

        $backgroundPathFront = storage_path('app/public/' . $frontTemplate->file_path);
        $resizedBackgroundPathFront = storage_path('app/public/templates/resized_' . basename($frontTemplate->file_path));

        if (!file_exists($backgroundPathFront)) {
            throw new \Exception("Front background file not found: $backgroundPathFront");
        }

        $image = Image::read($backgroundPathFront)->resize(900, 600, function ($constraint) {
            $constraint->aspectRatio();
        })->save($resizedBackgroundPathFront);

        $backgroundPathBack = null;
        $resizedBackgroundPathBack = null;
        if ($backTemplate) {
            $backgroundPathBack = storage_path('app/public/' . $backTemplate->file_path);
            $resizedBackgroundPathBack = storage_path('app/public/templates/resized_' . basename($backTemplate->file_path));

            if (!file_exists($backgroundPathBack)) {
                throw new \Exception("Back background file not found: $backgroundPathBack");
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

            $pdfPath = "attendance_certificates/" . auth()->id() . "/" . uniqid() . ".pdf";
            $pdfFullPath = storage_path("app/public/{$pdfPath}");
            Storage::disk('public')->makeDirectory("attendance_certificates/" . auth()->id());

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
        \App\Jobs\SendCertificateJob::dispatch($certificate)->delay($sendAt);
    }
}
