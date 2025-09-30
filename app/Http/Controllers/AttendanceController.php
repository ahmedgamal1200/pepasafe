<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDocument;
use App\Models\AttendanceTemplate;
use App\Models\Document;
use App\Models\DocumentTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function show($uuid): View|Application|Factory
    {
        $document = AttendanceDocument::query()->with(['template.event', 'recipient.user'])
            ->where('uuid', $uuid)->firstOrFail();

        return view('show-attendance', compact('document'));
    }

    public function downloadAll(AttendanceTemplate $template)
    {
        $eventId = $template->event_id;

        $documents = AttendanceDocument::whereHas('template', function ($query) use ($eventId) {
            $query->where('event_id', $eventId);
        })->get();

        if ($documents->isEmpty()) {
            return back()->with('error', 'لا توجد شهادات لهذا الحدث لتنزيلها.');
        }

        $combinedHtml = '';

        foreach ($documents as $document) {

            // ⚠️ مهم: تأكد أن 'file_path' هو الحقل الصحيح الذي يحمل اسم ملف الشهادة
            $certificateFileName = $document->file_path ?? 'placeholder.jpg';
            $fullCertificatePath = public_path('storage/' . $certificateFileName);
            $base64Image = '';

            if (file_exists($fullCertificatePath)) {
                $base64Image = base64_encode(file_get_contents($fullCertificatePath));
            }

            // تمرير بيانات التشفير إلى الـ View
            $documentHtml = view('templates.bages-attendance', [
                'base64Image' => $base64Image,
                'certificateFileName' => $certificateFileName,
            ])->render();

            $combinedHtml .= $documentHtml;

            // إضافة فاصل صفحة بعد كل شهادة باستثناء الأخيرة
            if (!$document->is($documents->last())) {
                // فاصل صفحة بسيط لضمان بداية كل شهادة في صفحة جديدة
                $combinedHtml .= '<div style="page-break-after: always; height: 1px;"></div>';
            }
        }

        // 3. توليد ملف PDF واحد وإرساله للتنزيل
        try {
            ini_set('memory_limit', '512M'); // زيادة الذاكرة للمستندات الكبيرة

            // تعيين خيارات Dompdf الضرورية لمعالجة Base64 والملفات المحلية
            $pdf = Pdf::setOptions([
                'isPhpEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ])->loadHTML($combinedHtml);

            // يمكنك ضبط حجم الورقة هنا (مثل A4)
//            $pdf->setPaper('A4', 'portrait');

            $fileName = 'All_Bages_Attendances_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $storagePath = 'public/' . $fileName;

            // الحفظ باستخدام Facade Storage
            Storage::put($storagePath, $pdf->output());

            // الحصول على المسار الكامل للتنزيل
            $downloadPath = Storage::path($storagePath);

            // إرسال ملف PDF للتنزيل وحذفه بعد الإرسال
            return response()->download($downloadPath, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'فشل في توليد ملف PDF الموحد: ' . $e->getMessage());
        }
    }

}
