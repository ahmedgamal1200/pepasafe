<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Services\CertificateDispatchService;
use App\Services\DocumentGenerationService;
use App\Services\EventService;
use App\Services\RecipientService;
use App\Services\SubscriptionService;
use App\Services\TemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;


class DocumentGenerationController extends Controller
{
    protected EventService $eventService;
    protected TemplateService $templateService;
    protected DocumentGenerationService $documentGenerationService;
    protected RecipientService $recipientService;
    protected SubscriptionService $subscriptionService;
    protected CertificateDispatchService $certificateDispatchService;

    public function __construct(
        EventService $eventService,
        TemplateService $templateService,
        DocumentGenerationService $documentGenerationService,
        RecipientService $recipientService,
        SubscriptionService $subscriptionService,
        CertificateDispatchService $certificateDispatchService
    ) {
        $this->eventService = $eventService;
        $this->templateService = $templateService;
        $this->documentGenerationService = $documentGenerationService;
        $this->recipientService = $recipientService;
        $this->subscriptionService = $subscriptionService;
        $this->certificateDispatchService = $certificateDispatchService;
    }

    /**
     * @throws Throwable
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
//        dd($request->all());
        set_time_limit(300);

        DB::beginTransaction();

        try {
            // Check the subscription balance
            $recipientCount = $this->recipientService->getRecipientCount($request->file('recipient_file_path'));
            if (!$this->subscriptionService->hasEnoughBalance($recipientCount)) {
                return back()->with('error', "عدد الشهادات المطلوبة ($recipientCount) أكبر من رصيدك الحالي.");
            }

            // Create event
            $event = $this->eventService->createEvent($request->validated());

            // Create document template
            $documentTemplate = $this->templateService->createDocumentTemplate($request->validated(), $event->id);

            // Create attendance template if enabled
            $attendanceTemplate = $this->templateService->createAttendanceTemplate($request->validated(), $event->id);

            // Create recipients
           $recipients = $this->recipientService->createRecipients($request->file('recipient_file_path'), $event->id);


//            $recipients = Recipient::where('event_id', $event->id)->get()->toArray();

            // جلب ابعاد الصورة الخاصة ب الشهادات من الريكوست
            $certificateData = json_decode($request->input('certificate_text_data'), true);
            $canvasWidth = $certificateData[array_key_first($certificateData)]['canvasWidth'] ?? 900;
            $canvasHeight = $certificateData[array_key_first($certificateData)]['canvasHeight'] ?? 600;

            $this->documentGenerationService->generateDocuments(
                $documentTemplate, $recipients,
                $request->file('template_data_file_path'),
                $canvasWidth, $canvasHeight,
                $request->certificate_text_data,
            );


            if ($attendanceTemplate) {
                // جلب الأبعاد من attendance_text_data
                $attendanceData = json_decode($request->input('attendance_text_data'), true);
                $attendanceCanvasWidth = $attendanceData[array_key_first($attendanceData)]['canvasWidth'] ?? 900;
                $attendanceCanvasHeight = $attendanceData[array_key_first($attendanceData)]['canvasHeight'] ?? 600;

                $this->documentGenerationService->generateAttendanceDocuments(
                    $attendanceTemplate,
                    $recipients,
                    $request->file('attendance_template_data_file_path'),
                    $attendanceCanvasWidth,
                    $attendanceCanvasHeight
                );
            }

            // Generate documents
//            $this->documentGenerationService->generateDocuments($documentTemplate, $recipients, $request->file('template_data_file_path'));
//
//            // Generate attendance documents if enabled
//            if ($attendanceTemplate) {
//                $this->documentGenerationService->generateAttendanceDocuments(
//                    $attendanceTemplate,
//                    $recipients,
//                    $request->file('attendance_template_data_file_path')
//                );
//            }

            // Deduct balance
            $this->subscriptionService->chargeDocument($recipientCount);

            DB::commit();
            return back()->with('success', 'تم إنشاء الحدث وتجهيز الشهادات بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating documents: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إنشاء الحدث: ' . $e->getMessage());
        }
    }





}
