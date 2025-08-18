<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\ExcelUpload;
use App\Services\CertificateDispatchService;
use App\Services\DocumentGenerationService;
use App\Services\EventService;
use App\Services\RecipientService;
use App\Services\SubscriptionService;
use App\Services\TemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Imports\DocumentDataImport;
use App\Imports\RecipientsImport;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Event;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

use App\Jobs\SendCertificateJob;
use App\Models\AttendanceDocument;
use App\Models\AttendanceDocumentField;
use App\Models\AttendanceTemplate;
use App\Models\DocumentField;
use Throwable;


class DocumentGenerationController extends Controller
{

    public function storehi(Request $request)
    {
        /* todo: 1- التأكد ان اليوزر ده ليه باقة وان الباقة بتاعته دي فيها رصيد
                2- انشاء الحدث
                3- انشاء ال slug الخاص ب الحدث
                4- اتأكد ان كان اليوزر مفعل الحضور ولا لا
                5- لو مش مفعل الحضور الداتا الخاصة ب الحضور تخش ب null
                6- اما لو مفعل الحضور هنعملي الداتا زي تاريخ الارسال والرسالة و طريقة الارسال
                7- هنخزن الملف بتاع الداتا بتاعت التيمبلت في الداتا بيز
                8- هنشوف التيمبلت اللي هو رافعه ونتاكد ان كان رافع وجه امامي ولا الاتنين مع بعض
                9- ناخد ال positions بتاعت اللي هو حطها ع الكانقا نحطها ع التيمبلت ونعمل لكل صف في الملف الخاص ب الداتا بتاعت التيمبلت شهادة حضور
                10- نحفظ الشهادات بتاعت الحضور دي ك pdf و صورة
                11- نملي البيانات الخاصة ب الوثيقة (الشهادة)
                12- ونشوف ملف التواصل الخاص بالوثيقة ونعمل لكل صف فيه حساب جديد يعني نعمل create new user ونديله role user
                13- ونشوف ملف الداتا بتاعت التتيمبلت الخص ب الوثيقة وناخد كل صف ونشوف التيمبلت نفسه ومكان وجوده ال inputs زي حجم الخط والخ  ونحطها ع التيمبلت ده ونعمله وثيقة جديدة ك pdf و صورة
                14- ونحفظها في الداتا بيز
                15- ونبعت لينك الوثيقة دي + شهادة الحضور لو موجوده نبعتهم ع حسب طريقة اخنيار اليوزر لطريقة التواصل sms, whatsapp , email + الرسالة اللي اليوزر دخلها ب ايده ومعاه اللينك بتاع الشهادة
                16- بس الوثيقة او الحضور ميتبعتوش الا ع حسب معاد الارسال اللي اليوزر اختاره
                17- وطبعا كل صف من الشيت = واحد من الرصيد ويتخصم منه ولو الرصيد مكفاش يخرج وميكملش يقولي رصيدك غير كافي
                18- ويعمل لكل شهادة qr code خاص بيها و uuid , uniqe code
        */
        dd($request->all());

        Log::info('Full Request Data (from Combined FormData):', $request->all());
        // dd($request->all()); // ممكن تستخدم dd هنا للمراجعة السريعة

        // 1. استلام البيانات الجديدة (اللي جاية كـ JSON strings)
        $documentType = $request->input('documentType');
        $iTextElementsJson = $request->input('iTextElements');
        $designCanvasDimensionsJson = $request->input('designCanvasDimensions');

        // 2. تحويل الـ JSON strings إلى PHP arrays/objects
        $iTextElements = json_decode($iTextElementsJson, true); // true لتحويلها لـ associative array
        $designCanvasDimensions = json_decode($designCanvasDimensionsJson, true); // true لتحويلها لـ associative array

        // 3. استلام باقي الـ 21 حقل الأخرى كالمعتاد
        $eventTitle = $request->input('event_title');
        $issuer = $request->input('issuer');
        // ... استكمل باقي الحقول اللي بتجيلك من الفورم ...
        $userId = $request->input('user_id');
        $documentTitle = $request->input('document_title');
        // وهكذا لكل حقل من الـ 21 حقل بتوعك

        // 4. لو عندك ملفات مرفوعة، بتستقبلها بالطريقة العادية
        // $documentTemplateFile = $request->file('document_template_file_path');
        // if ($documentTemplateFile) {
        //     Log::info('Uploaded File Name:', ['name' => $documentTemplateFile->getClientOriginalName()]);
        // }

        // 5. سجل البيانات اللي تم فك تشفيرها (للتصحيح)
        Log::info('Decoded iTextElements:', $iTextElements ?? []);
        Log::info('Decoded designCanvasDimensions:', $designCanvasDimensions ?? []);
        Log::info('Document Type:', ['type' => $documentType]);

        // 6. هنا بقى تبدأ تكمل الـ logic بتاع توليد المستندات
        // هتستخدم $iTextElements و $designCanvasDimensions لطباعة النصوص على الصورة
        // وتستخدم باقي البيانات (eventTitle, issuer, إلخ) لتكملة المعلومات على المستند

        // 7. إرجاع رد للـ Front-end
        return response()->json([
            'status' => 'success',
            'message' => 'All data received and processed successfully!',
            // ممكن ترجع أي بيانات للـ Front-end لتأكيد الاستلام أو معلومات إضافية
        ], 200);




//        dd($request->all());
//        return redirect()->route('pry');
//
//        set_time_limit(300);
//        $user = auth()->user();
//        $subscription = $user->subscription;
//
//        // 1. تحقق من وجود رصيد
//        if ($subscription->remaining <= 0) {
//            return back()->with('error', 'رصيدك الحالي لا يسمح لأجراء اي وثيقة. برجاء شحن المحفظة ثم اعد المحاولة');
//        }
//
//        // 2. استيراد بيانات المشاركين من ملف Excel
//        $importRecipients = new RecipientsImport();
//        Excel::import($importRecipients, $request->recipient_file_path);
//        $recipientsData = $importRecipients->rows;
//        $neededRows = count($recipientsData);
//
//        // 3. التحقق من أن الرصيد يكفي
//        if ($subscription->remaining < $neededRows) {
//            return back()->with('error', "عدد الشهادات المطلوبة ($neededRows) أكبر من رصيدك الحالي ({$subscription->remaining}).");
//        }
//
//        // 4. إنشاء الحدث
//        $event = Event::create([
//            'title'      => $request->event_title,
//            'issuer'     => $request->issuer,
//            'start_date' => $request->event_start_date,
//            'end_date'   => $request->event_end_date,
//            'user_id'    => $user->id,
//        ]);
//
//        // 5. إنشاء التيمبلت المرتبط بالحدث
//        $template = DocumentTemplate::create([
//            'title'                 => $request->document_title,
//            'message'               => $request->document_message,
//            'send_at'               => $request->document_send_at,
//            'send_via'              => $request->document_send_via,
//            'is_attendance_enabled' => $request->is_attendance_enabled ?? false,
//            'event_id'              => $event->id,
//            'is_temporary'          => $request->document_validity === 'temporary',
//        ]);
//
//        // 6. حفظ ملفات Excel في قاعدة البيانات
//        $event->excelUploads()->create([
//            'file_path'   => $request->recipient_file_path->store('uploads'),
//            'upload_type' => 'recipients',
//        ]);
//
//        if ($request->hasFile('template_data_file_path')) {
//            $event->excelUploads()->create([
//                'file_path'   => $request->template_data_file_path->store('uploads'),
//                'upload_type' => 'document_data',
//            ]);
//        }
//
//// 1. جهّز الإيميلات وفلتر الموجودين
//        $emails = collect($recipientsData)->pluck('email')->toArray();
//        $existingEmails = User::whereIn('email', $emails)->pluck('email')->toArray();
//
//        $newUsers = [];
//        $now = now();
//
//        foreach ($recipientsData as $row) {
//            if (!in_array($row['email'], $existingEmails)) {
//                $newUsers[] = [
//                    'name'       => $row['name'],
//                    'email'      => $row['email'],
//                    'password'   => bcrypt('password123'), // مهم تشفر الباسورد
//                    'phone'      => $row['phone_number'] ?? null,
//                    'created_at' => $now,
//                    'updated_at' => $now,
//                ];
//            }
//        }
//
//// 2. إدخال المستخدمين دفعة واحدة
//        User::insert($newUsers);
//
//// 3. جلب الـ IDs بتوع المستخدمين الجدد
//        $newUserEmails = collect($newUsers)->pluck('email')->toArray();
//        $newUserIds = User::whereIn('email', $newUserEmails)->pluck('id');
//
//// 4. جلب الـ role_id الخاص بـ "user"
//        $roleId = Role::where('name', 'user')->value('id');
//
//// 5. تجهيز بيانات الربط بجدول model_has_roles
//        $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
//            return [
//                'role_id'    => $roleId,
//                'model_type' => User::class,
//                'model_id'   => $userId,
//            ];
//        })->toArray();
//
//// 6. إدخال الـ roles دفعة واحدة
//        DB::table('model_has_roles')->insert($roleAssignments);
//
//        // 8. ربط كل مستخدم بمشارك في الحدث
//        $recipients = [];
//        foreach ($recipientsData as $row) {
//            $participant = User::where('email', $row['email'])->first();
//            $recipients[] = Recipient::firstOrCreate([
//                'event_id' => $event->id,
//                'user_id'  => $participant->id,
//            ]);
//        }
//
//        // 9. خصم عدد الصفوف من رصيد الباقة
//        $subscription->decrement('remaining', $neededRows);
//
//        // 10. استيراد داتا التيمبلت من الملف
//        $documentRows = [];
//        if ($request->hasFile('template_data_file_path')) {
//            $importData = new DocumentDataImport();
//            Excel::import($importData, $request->template_data_file_path);
//            $documentRows = $importData->rows;
//        }
//
//        // 11. حفظ ملفات التيمبلت (front & back)
//        $templateFiles = $request->file('document_template_file_path', []);
//        $templateSides = $request->input('template_sides', []);
//        $frontPath = null;
//        $backPath  = null;
//
//        foreach ($templateFiles as $index => $file) {
//            $side = $templateSides[$index] ?? null;
//            $path = $file->store('templates', 'public');
//            if ($side === 'front') $frontPath = $path;
//            if ($side === 'back')  $backPath  = $path;
//        }
//
//        if (!$frontPath) {
//            return back()->with('error', 'يجب رفع تيمبلت الوجه الأمامي للشهادة.');
//        }
//
//        // 12. توليد الشهادات وربطها بالمشاركين
//        foreach ($recipients as $index => $recipient) {
//            $dataRow = $documentRows[$index] ?? [];
//
//            $fields = [];
//            foreach ($dataRow as $key => $value) {
//                $fields[] = [
//                    'text'  => $value,
//                    'x'     => 100, // Placeholder position
//                    'y'     => 200,
//                    'font'  => 'Arial',
//                    'size'  => 16,
//                    'color' => '#000000',
//                ];
//            }
//
//            $pdfPath = "certificates/{$user->id}/" . uniqid() . ".pdf";
//            $pdfFullPath = storage_path("app/public/{$pdfPath}");
//
//            $pdf = Pdf::loadView('pdf.template', [
//                'background' => storage_path("app/public/{$frontPath}"),
//                'fields'     => $fields,
//            ]);
//
//            $pdf->save($pdfFullPath);
//
//            // حفظ الشهادة
//            Certificate::create([
//                'user_id'       => $recipient->user_id,
//                'recipient_id'  => $recipient->id,
//                'template_id'   => $template->id,
//                'file_path'     => $pdfPath,
//                'sent_at'       => $template->send_at,
//                'send_via'      => $template->send_via,
//                'is_temporary'  => $template->is_temporary,
//            ]);
//        }
//
//        return back()->with('success', 'تم تجهيز الشهادات وإرفاقها بالمشاركين بنجاح.');
    }




    public function oldstore(Request $request)
    {
        dd($request->all());
        set_time_limit(300);
        $user = auth()->user();
        $remaining = $user->subscription->remaining;

        if ($remaining == 0) {
            return back()->with('error', 'رصيدك الحالي لا يسمح لأجراء اي وثيقة. برجاء شحن المحفظة ثم اعد المحاولة');
        }

//        DB::transaction(function () use ($request, $user) {
            // إنشاء الحدث
            $event = Event::create([
                'title'      => $request->event_title,
                'issuer'     => $request->issuer,
                'start_date' => $request->event_start_date,
                'end_date'   => $request->event_end_date,
                'user_id'    => $request->user_id,
            ]);

            // إنشاء التيمبلت
            $template = DocumentTemplate::create([
                'title'                 => $request->document_title,
                'message'               => $request->document_message,
                'send_at'               => $request->document_send_at,
                'send_via'              => $request->document_send_via,
                'is_attendance_enabled' => $request->is_attendance_enabled ?? false,
                'event_id'              => $event->id,
            ]);

            // تخزين ملفات الإكسل
            $event->excelUploads()->create([
                'file_path'   => $request->recipient_file_path->store('uploads'),
                'upload_type' => 'recipients',
            ]);

            if ($request->hasFile('template_data_file_path')) {
                $event->excelUploads()->create([
                    'file_path'   => $request->template_data_file_path->store('uploads'),
                    'upload_type' => 'document_data',
                ]);
            }

            // استيراد المستلمين
            $importRecipients = new RecipientsImport();
            Excel::import($importRecipients, $request->recipient_file_path);
            $recipientsData = $importRecipients->rows;

            // الإيميلات الموجودة
            $existingEmails = User::whereIn('email', collect($recipientsData)->pluck('email'))->pluck('email')->toArray();


            $newUsers = [];

            foreach ($recipientsData as $row) {
                if (in_array($row['email'], $existingEmails)) {
                    continue;
                }

                $newUsers[] = [
                    'name'       => $row['name'],
                    'email'      => $row['email'],
                    'password'   => bcrypt('password123'),
                    'phone'      => $row['phone_number'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            User::insert($newUsers);

            $recipients = [];

            foreach ($recipientsData as $row) {
                $user = User::where('email', $row['email'])->first();

                $recipient = Recipient::firstOrCreate([
                    'event_id' => $event->id,
                    'user_id'  => $user->id,
                ]);

                $recipients[] = $recipient;
            }

            // استيراد داتا التيمبلت
            $documentRows = [];

            if ($request->hasFile('template_data_file_path')) {
                $importData = new DocumentDataImport();
                Excel::import($importData, $request->template_data_file_path);
                $documentRows = $importData->rows;
            }


            // 1. تحققات أساسية وتحميل بيانات من الريكوست
            $templateFiles = $request->file('document_template_file_path', []);
            $templateSides = $request->input('template_sides', []); // ['front', 'back'] مثلاً


            $frontFile = null;
            $backFile = null;

            // 2. فرز الملفات حسب نوعها (front أو back)
            foreach ($templateFiles as $index => $file) {
                $side = $templateSides[$index] ?? null;

                $file_type = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';

                if ($side === 'front') {
                    $frontFile = $file;
                } elseif ($side === 'back') {
                    $backFile = $file;
                }
            }

            if (!$frontFile) {
                return back()->with('error', 'يرجى رفع ملف Front للوثيقة');
            }


            $frontPath = $frontFile->store('templates', 'public');
            $backPath = $backFile ? $backFile->store('templates', 'public') : null;

            $totalRows = count($recipientsData);

            // تحديد تقسيم الصفوف لو فيه back file
            $frontRows = [];
            $backRows = [];
            if ($backFile) {
                $half = intdiv($totalRows, 2);
                $frontRows = array_slice($recipientsData, 0, $half);
                $backRows = array_slice($recipientsData, $half);
            } else {
                $frontRows = $recipientsData;
            }

            // توليد front certificates
            foreach ($frontRows as $index => $recipient) {
                $dataRow = $documentRows[$index] ?? [];

                $renderedFields = [];
                foreach ($dataRow as $key => $value) {
                    $renderedFields[] = [
                        'text'  => $value,
                        'x'     => 100,
                        'y'     => 300,
                        'font'  => 'Arial',
                        'size'  => 16,
                        'color' => '#000000',
                    ];
                }
//                dd($renderedFields);



                $frontBackground = storage_path('app/public/' . $frontPath);
                $pdfFront = Pdf::loadView('pdf.template', [
                    'background' => $frontBackground,
                    'fields'     => $renderedFields,
                ]);


                return $pdfFront->stream('certificate-preview.pdf');
            }

            // توليد back certificates لو موجود back
            if ($backFile) {
                foreach ($backRows as $index => $recipient) {
                    // لاحظ: البيانات الخاصة بالباك يمكن تكون مختلفة أو فارغة
                    // لو عايز تربط بيانات الباك من $documentRows لازم تعدل هنا حسب بياناتك

                    $renderedFields = []; // حاليًا فاضية


                    $backBackground = storage_path('app/public/' . $backPath);
                    $pdfBack = Pdf::loadView('pdf.template', [
                        'background' => $backBackground,
                        'fields' => $renderedFields,
//                        '$file_type' => $file_type,
                    ]);
                }
            }

//        }); // نهاية DB::transaction

        return back()->with('success', 'تم تجهيز الشهادات بنجاح');
    }




    public function storeV0(Request $request)
    {
//        dd($request->all());
        set_time_limit(300);
        // Validate request data
        $request->validate([
            'event_title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'document_title' => 'required|string|max:255',
            'document_message' => 'required|string',
            'document_send_at' => 'required|date',
            'document_send_via' => 'required|array',
            'document_send_via.*' => 'in:email,sms,whatsapp',
            'recipient_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
            'template_data_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
            'document_template_file_path' => 'required|array',
            'document_template_file_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'template_sides' => 'required|array',
            'template_sides.*' => 'in:front,back',
            'document_validity' => 'required|in:permanent,temporary',
            'valid_from' => 'nullable|required_if:document_validity,temporary|date',
            'valid_until' => 'nullable|required_if:document_validity,temporary|date|after_or_equal:valid_from',
            'certificate_text_data' => 'required|json',
            // حقول الحضور
            'is_attendance_enabled' => 'nullable|boolean',
            'attendance_send_at' => 'nullable|required_if:is_attendance_enabled,true|date',
            'attendance_message' => 'nullable|required_if:is_attendance_enabled,true|string',
            'attendance_template_data_file_path' => 'nullable|required_if:is_attendance_enabled,true|file|mimes:xlsx,xls|max:2048',
            'attendance_template_file_path' => 'nullable|required_if:is_attendance_enabled,true|array',
            'attendance_template_file_path.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'attendance_template_sides' => 'nullable|required_if:is_attendance_enabled,true|array',
            'attendance_template_sides.*' => 'nullable|in:front,back',
            'attendance_validity' => 'nullable|required_if:is_attendance_enabled,true|in:permanent,temporary',
            'attendance_valid_from' => 'nullable|required_if:attendance_validity,temporary|date',
            'attendance_valid_until' => 'nullable|required_if:attendance_validity,temporary|date|after_or_equal:attendance_valid_from',
            'attendance_text_data' => 'nullable|required_if:is_attendance_enabled,true|json',
        ]);

        // Check user's subscription and balance
        $user = auth()->user();
        $subscription = $user->subscription;
        $recipientFile = $request->file('recipient_file_path');
        $recipientRows = Excel::toCollection(new RecipientsImport(), $recipientFile)->first()->count();

        if ($subscription->remaining < $recipientRows) {
            return back()->with('error', "عدد الشهادات المطلوبة ($recipientRows) أكبر من رصيدك الحالي ({$subscription->remaining}).");
        }

        DB::beginTransaction();

        try {
            // Create event and slug
            $event = Event::create([
                'title' => $request->event_title,
                'issuer' => $request->issuer,
                'start_date' => $request->event_start_date,
                'end_date' => $request->event_end_date,
                'user_id' => $user->id,
                'slug' => Str::slug($request->event_title) . '-' . uniqid(),
            ]);

            // Handle document template
            $documentTemplate = DocumentTemplate::create([
                'title' => $request->document_title,
                'message' => $request->document_message,
                'send_at' => $request->document_send_at,
                'send_via' => json_encode($request->document_send_via),
                'is_attendance_enabled' => $request->is_attendance_enabled ?? false,
                'event_id' => $event->id,
                'validity' => $request->document_validity,
            ]);

            if ($request->document_validity === 'temporary') {
                $documentTemplate->valid_from = $request->valid_from;
                $documentTemplate->valid_until = $request->valid_until;
                $documentTemplate->save();
            }


            // Store document template data file
            ExcelUpload::create([
                'file_path' => $request->template_data_file_path->store('uploads'),
                'upload_type' => 'document_data',
                'event_id' => $event->id,
            ]);

            // Process document template files
            $documentTemplateFiles = $request->file('document_template_file_path', []);
            $documentTemplateSides = $request->input('template_sides', []);
            foreach ($documentTemplateFiles as $index => $file) {
                $side = $documentTemplateSides[$index] ?? 'front';
                $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
                $documentTemplate->templateFiles()->create([
                    'file_path' => $file->store('templates', 'public'),
                    'file_type' => $fileType,
                    'side' => $side,
                ]);
            }

            // Save document field positions
            $certificateTextData = json_decode($request->certificate_text_data, true);
//            dd($certificateTextData);
            $firstCardId = array_key_first($certificateTextData);
            $canvasData = $certificateTextData[$firstCardId];

            $canvasWidth = $canvasData['canvasWidth'] ?? 900; // استخدم القيمة المرسلة أو 900 كافتراضي
            $canvasHeight = $canvasData['canvasHeight'] ?? 600; // استخدم القيمة المرسلة أو 600 كافتراضي
            $textsData = $canvasData['texts'] ?? []; // البيانات النصية الفعلية

            foreach ($textsData as $text) {
                DocumentField::create([
                    'field_key' => $text['text'],
                    'label' => $text['text'],
                    'position_x' => $text['left'],  // <--- تم إزالة * 0.75
                    'position_y' => $text['top'],   // <--- تم إزالة * 0.75
                    'width' => $text['width'] ?? null,
                    'height' => $text['height'] ?? null,
                    'font_family' => $text['fontFamily'],
                    'font_size' => $text['fontSize'],
                    'font_color' => $text['fill'],
                    'text_align' => $text['textAlign'] ?? 'left',
                    'font_weight' => $text['fontWeight'] ?? 'normal',
                    'rotation' => $text['angle'],
                    'z_index' => $text['zIndex'] ?? 1,
                    'document_template_id' => $documentTemplate->id,
                ]);
            }

            // Handle attendance if enabled
            $attendanceTemplate = null;
            if ($request->is_attendance_enabled) {
                $attendanceTemplate = AttendanceTemplate::create([
                    'message' => $request->attendance_message,
                    'send_at' => $request->attendance_send_at,
                    'send_via' => json_encode($request->attendance_send_via),
                    'event_id' => $event->id,
                    'validity' => $request->attendance_validity,
                ]);

                if ($request->attendance_validity === 'temporary') {
                    $attendanceTemplate->valid_from = $request->attendance_valid_from;
                    $attendanceTemplate->valid_until = $request->attendance_valid_until;
                    $attendanceTemplate->save();
                }

                // Store attendance template data file
                $attendanceTemplate->excelUploads()->create([
                    'file_path' => $request->attendance_template_data_file_path->store('uploads'),
                ]);

                // Process attendance template files
                $attendanceTemplateFiles = $request->file('attendance_template_file_path', []);
                $attendanceTemplateSides = $request->input('attendance_template_sides', []);
                foreach ($attendanceTemplateFiles as $index => $file) {
                    $side = $attendanceTemplateSides[$index] ?? 'front';
                    $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
                    $attendanceTemplate->templateFiles()->create([
                        'file_path' => $file->store('templates', 'public'),
                        'file_type' => $fileType,
                        'side' => $side,
                    ]);
                }

                // Save attendance field positions
                $attendanceTextData = json_decode($request->attendance_text_data, true);
                foreach ($attendanceTextData as $cardId => $texts) {
                    foreach ($texts as $text) {
                        AttendanceDocumentField::create([
                            'field_key' => $text['text'],
                            'label' => $text['text'],
                            'position_x' => $text['left'],
                            'position_y' => $text['top'],
                            'width' => $text['width'] ?? null,
                            'height' => $text['height'] ?? null,
                            'font_family' => $text['fontFamily'],
                            'font_size' => $text['fontSize'],
                            'font_color' => $text['fill'],
                            'text_align' => $text['textAlign'] ?? 'left',
                            'font_weight' => $text['fontWeight'] ?? 'normal',
                            'rotation' => $text['angle'],
                            'z_index' => $text['zIndex'] ?? 1,
                            'attendance_template_id' => $attendanceTemplate->id,
                        ]);
                    }
                }
            }

            // Process recipients and create users
            // Process recipients and create users
            $importRecipients = new RecipientsImport();
            Excel::import($importRecipients, $recipientFile);
            $recipientsData = collect($importRecipients->rows)->unique('email')->values(); // إزالة التكرارات بناءً على الإيميل

// جلب الإيميلات الموجودة في قاعدة البيانات
            $existingEmails = User::whereIn('email', $recipientsData->pluck('email'))->pluck('email')->toArray();
            $newUsers = [];
            $now = now();

            foreach ($recipientsData as $row) {
                if (!in_array($row['email'], $existingEmails)) {
                    $newUsers[] = [
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'password' => bcrypt('password123'),
                        'phone' => $row['phone_number'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($newUsers)) {
                try {
                    User::insert($newUsers);
                } catch (\Exception $e) {
                    Log::error('Failed to insert users: ' . $e->getMessage(), ['newUsers' => $newUsers]);
                    throw $e;
                }

                $roleId = Role::where('name', 'user')->value('id');
                $newUserIds = User::whereIn('email', collect($newUsers)->pluck('email'))->pluck('id');
                $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
                    return [
                        'role_id' => $roleId,
                        'model_type' => User::class,
                        'model_id' => $userId,
                    ];
                })->toArray();
                DB::table('model_has_roles')->insert($roleAssignments);
            }

            $recipients = [];
            foreach ($recipientsData as $row) {
                $user = User::where('email', $row['email'])->first();
                $recipient = Recipient::firstOrCreate([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                ]);
                $recipients[] = $recipient;
            }

            // Load template data
            $documentDataImport = new DocumentDataImport();
            Excel::import($documentDataImport, $request->template_data_file_path);
            $documentRows = $documentDataImport->rows;

            $attendanceDataRows = [];
            if ($request->is_attendance_enabled) {
                $attendanceDataImport = new DocumentDataImport();
                Excel::import($attendanceDataImport, $request->attendance_template_data_file_path);
                $attendanceDataRows = $attendanceDataImport->rows;
            }

            // Generate documents
            // Generate documents
            $frontDocumentTemplate = $documentTemplate->templateFiles()->where('side', 'front')->first();
            if (!$frontDocumentTemplate) {
                throw new \Exception('Front document template not found');
            }
            $documentBackgroundPath = storage_path('app/public/' . $frontDocumentTemplate->file_path);

// جلب أبعاد الصورة الأصلية
            $imageSize = getimagesize($documentBackgroundPath);
            $imageWidth = $imageSize[0]; // العرض بالبكسل
            $imageHeight = $imageSize[1]; // الطول بالبكسل

            $firstPdf = null;
            foreach ($recipients as $index => $recipient) {
                $dataRow = $documentRows[$index] ?? [];
                $fields = DocumentField::where('document_template_id', $documentTemplate->id)->get();
                $imageSize = getimagesize($documentBackgroundPath);
                $imageWidth = $imageSize[0];
                $imageHeight = $imageSize[1];
                $scaleX = $imageWidth / $canvasWidth;
                $scaleY = $imageHeight / $canvasHeight;

                $renderedFields = [];
                foreach ($fields as $field) {
                    $value = $dataRow[$field->field_key] ?? $field->field_key;
                    $x = $field->position_x; // <--- تم إزالة * 0.75
                    $y = $field->position_y; // <--- تم إزالة * 0.75
                    Log::info("Field: {$value}, Original X: {$field->position_x}, Converted X: {$x}, Original Y: {$field->position_y}, Converted Y: {$y}");
                    $renderedFields[] = [
                        'text' => $value,
                        'x' => $x,
                        'y' => $y,
                        'font' => $field->font_family,
                        'size' => $field->font_size,
                        'color' => $field->font_color,
                        'align' => $field->text_align,
                        'weight' => $field->font_weight,
                        'rotation' => $field->rotation,
                    ];
                }

                $pdf = Pdf::loadView('pdf.template', [
                    'background' => $documentBackgroundPath,
                    'fields' => $renderedFields,
                    'imageWidth' => $canvasWidth,    // <--- استخدم القيم المستلمة
                    'imageHeight' => $canvasHeight,  // <--- استخدم القيم المستلمة
                ]);
                    $pdf->setPaper([0, 0, $canvasWidth, $canvasHeight], 'custom'); // <--- تم تعديل الأبعاد
                    $pdf->set_option('isHtml5ParserEnabled', true);
                    $pdf->set_option('isRemoteEnabled', true);
                    $pdf->set_option('dpi', 96); // حافظ على 96 DPI

                return $pdf->stream('certificate-preview.pdf');

                // حفظ الشهادة
                $pdfPath = "certificates/{$user->id}/" . uniqid() . ".pdf";
                $pdfFullPath = storage_path("app/public/{$pdfPath}");
                Storage::disk('public')->makeDirectory("certificates/{$user->id}");
                $pdf->save($pdfFullPath);

                $uuid = Str::uuid();
                $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
                $qrPath = "qrcodes/{$uuid}.png";
                Storage::disk('public')->makeDirectory('qrcodes');
                Storage::disk('public')->put($qrPath, $qrCode);

                $document = Document::create([
                    'file_path' => $pdfPath,
                    'uuid' => $uuid,
                    'unique_code' => Str::random(10),
                    'qr_code_path' => $qrPath,
                    'status' => 'pending',
                    'document_template_id' => $documentTemplate->id,
                    'recipient_id' => $recipient->id,
                    'valid_from' => $documentTemplate->valid_from,
                    'valid_until' => $documentTemplate->valid_until,
                ]);

                SendCertificateJob::dispatch($document)->delay($documentTemplate->send_at);
            }



            // Generate attendance documents if enabled
            if ($request->is_attendance_enabled) {
                $frontAttendanceTemplate = $attendanceTemplate->templateFiles()->where('side', 'front')->first();
                if (!$frontAttendanceTemplate) {
                    throw new \Exception('Front attendance template not found');
                }
                $attendanceBackgroundPath = storage_path('app/public/' . $frontAttendanceTemplate->file_path);

                foreach ($recipients as $index => $recipient) {
                    $dataRow = $attendanceDataRows[$index] ?? [];
                    $fields = AttendanceDocumentField::where('attendance_template_id', $attendanceTemplate->id)->get();
                    $renderedFields = [];
                    foreach ($fields as $field) {
                        $value = $dataRow[$field->field_key] ?? '';
                        $renderedFields[] = [
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

                    $pdfPath = "attendance_certificates/{$user->id}/" . uniqid() . ".pdf";
                    $pdfFullPath = storage_path("app/public/{$pdfPath}");
                    // إنشاء المجلد لو مش موجود
                    Storage::disk('public')->makeDirectory("attendance_certificates/{$user->id}");

                    $pdf = Pdf::loadView('pdf.template', [
                        'background' => $attendanceBackgroundPath,
                        'fields' => $renderedFields,
                    ]);
                    $pdf->save($pdfFullPath);

                    $uuid = Str::uuid();
                    $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
                    $qrPath = "qrcodes/{$uuid}.png";
                    Storage::disk('public')->makeDirectory('qrcodes');
                    Storage::disk('public')->put($qrPath, $qrCode);

                    $attendanceDocument = AttendanceDocument::create([
                        'file_path' => $pdfPath,
                        'uuid' => $uuid,
                        'unique_code' => Str::random(10),
                        'qr_code_path' => $qrPath,
                        'status' => 'pending',
                        'attendance_template_id' => $attendanceTemplate->id,
                        'recipient_id' => $recipient->id,
                        'valid_from' => $attendanceTemplate->valid_from,
                        'valid_until' => $attendanceTemplate->valid_until,
                    ]);

                    SendCertificateJob::dispatch($attendanceDocument)->delay($attendanceTemplate->send_at);
                }
            }

            // Deduct balance
            $subscription->decrement('remaining', $recipientRows);

            DB::commit();
            return back()->with('success', 'تم إنشاء الحدث وتجهيز الشهادات بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating documents: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إنشاء الحدث: ' . $e->getMessage());
        }
    }



    public function storeV1(Request $request)
    {
        set_time_limit(300);

        // Validate request data
        $request->validate([
            'event_title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'document_title' => 'required|string|max:255',
            'document_message' => 'required|string',
            'document_send_at' => 'required|date',
            'document_send_via' => 'required|array',
            'document_send_via.*' => 'in:email,sms,whatsapp',
            'recipient_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
            'template_data_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
            'document_template_file_path' => 'required|array',
            'document_template_file_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'template_sides' => 'required|array',
            'template_sides.*' => 'in:front,back',
            'document_validity' => 'required|in:permanent,temporary',
            'valid_from' => 'nullable|required_if:document_validity,temporary|date',
            'valid_until' => 'nullable|required_if:document_validity,temporary|date|after_or_equal:valid_from',
            'certificate_text_data' => 'required|json',
            'is_attendance_enabled' => 'nullable|boolean',
            'attendance_send_at' => 'nullable|required_if:is_attendance_enabled,true|date',
            'attendance_message' => 'nullable|required_if:is_attendance_enabled,true|string',
            'attendance_template_data_file_path' => 'nullable|required_if:is_attendance_enabled,true|file|mimes:xlsx,xls|max:2048',
            'attendance_template_file_path' => 'nullable|required_if:is_attendance_enabled,true|array',
            'attendance_template_file_path.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'attendance_template_sides' => 'nullable|required_if:is_attendance_enabled,true|array',
            'attendance_template_sides.*' => 'nullable|in:front,back',
            'attendance_validity' => 'nullable|required_if:is_attendance_enabled,true|in:permanent,temporary',
            'attendance_valid_from' => 'nullable|required_if:attendance_validity,temporary|date',
            'attendance_valid_until' => 'nullable|required_if:attendance_validity,temporary|date|after_or_equal:attendance_valid_from',
            'attendance_text_data' => 'nullable|required_if:is_attendance_enabled,true|json',
        ]);

        // Check user's subscription and balance
        $user = auth()->user();
        $subscription = $user->subscription;
        $recipientFile = $request->file('recipient_file_path');
        $recipientRows = Excel::toCollection(new RecipientsImport(), $recipientFile)->first()->count();

        if ($subscription->remaining < $recipientRows) {
            return back()->with('error', "عدد الشهادات المطلوبة ($recipientRows) أكبر من رصيدك الحالي ({$subscription->remaining}).");
        }

        DB::beginTransaction();

        try {
            // Create event and slug
            $event = Event::create([
                'title' => $request->event_title,
                'issuer' => $request->issuer,
                'start_date' => $request->event_start_date,
                'end_date' => $request->event_end_date,
                'user_id' => $user->id,
                'slug' => Str::slug($request->event_title) . '-' . uniqid(),
            ]);

            // Handle document template
            $documentTemplate = DocumentTemplate::create([
                'title' => $request->document_title,
                'message' => $request->document_message,
                'send_at' => $request->document_send_at,
                'send_via' => json_encode($request->document_send_via),
                'is_attendance_enabled' => $request->is_attendance_enabled ?? false,
                'event_id' => $event->id,
                'validity' => $request->document_validity,
            ]);

            if ($request->document_validity === 'temporary') {
                $documentTemplate->valid_from = $request->valid_from;
                $documentTemplate->valid_until = $request->valid_until;
                $documentTemplate->save();
            }

            // Store document template data file
            ExcelUpload::create([
                'file_path' => $request->template_data_file_path->store('uploads'),
                'upload_type' => 'document_data',
                'event_id' => $event->id,
            ]);

            // Process document template files
            $documentTemplateFiles = $request->file('document_template_file_path', []);
            $documentTemplateSides = $request->input('template_sides', []);
            foreach ($documentTemplateFiles as $index => $file) {
                $side = $documentTemplateSides[$index] ?? 'front';
                $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
                $documentTemplate->templateFiles()->create([
                    'file_path' => $file->store('templates', 'public'),
                    'file_type' => $fileType,
                    'side' => $side,
                ]);
            }

            // Save document field positions
            $certificateTextData = json_decode($request->certificate_text_data, true);
            foreach ($certificateTextData as $cardId => $texts) {
                foreach ($texts as $text) {
                    DocumentField::create([
                        'field_key' => $text['text'],
                        'label' => $text['text'],
                        'position_x' => $text['left'],
                        'position_y' => $text['top'],
                        'width' => $text['width'] ?? null,
                        'height' => $text['height'] ?? null,
                        'font_family' => $text['fontFamily'],
                        'font_size' => $text['fontSize'],
                        'font_color' => $text['fill'],
                        'text_align' => $text['textAlign'] ?? 'left',
                        'font_weight' => $text['fontWeight'] ?? 'normal',
                        'rotation' => $text['angle'],
                        'z_index' => $text['zIndex'] ?? 1,
                        'document_template_id' => $documentTemplate->id,
                    ]);
                }
            }

            // Handle attendance if enabled
            $attendanceTemplate = null;
            if ($request->is_attendance_enabled) {
                $attendanceTemplate = AttendanceTemplate::create([
                    'message' => $request->attendance_message,
                    'send_at' => $request->attendance_send_at,
                    'send_via' => json_encode($request->document_send_via),
                    'event_id' => $event->id,
                    'validity' => $request->attendance_validity,
                ]);

                if ($request->attendance_validity === 'temporary') {
                    $attendanceTemplate->valid_from = $request->attendance_valid_from;
                    $attendanceTemplate->valid_until = $request->attendance_valid_until;
                    $attendanceTemplate->save();
                }

                // Store attendance template data file
                $attendanceTemplate->excelUploads()->create([
                    'file_path' => $request->attendance_template_data_file_path->store('uploads'),
                ]);

                // Process attendance template files
                $attendanceTemplateFiles = $request->file('attendance_template_file_path', []);
                $attendanceTemplateSides = $request->input('attendance_template_sides', []);
                foreach ($attendanceTemplateFiles as $index => $file) {
                    $side = $attendanceTemplateSides[$index] ?? 'front';
                    $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
                    $attendanceTemplate->templateFiles()->create([
                        'file_path' => $file->store('templates', 'public'),
                        'file_type' => $fileType,
                        'side' => $side,
                    ]);
                }

                // Save attendance field positions
                $attendanceTextData = json_decode($request->attendance_text_data, true);
                foreach ($attendanceTextData as $cardId => $texts) {
                    foreach ($texts as $text) {
                        AttendanceDocumentField::create([
                            'field_key' => $text['text'],
                            'label' => $text['text'],
                            'position_x' => $text['left'],
                            'position_y' => $text['top'],
                            'width' => $text['width'] ?? null,
                            'height' => $text['height'] ?? null,
                            'font_family' => $text['fontFamily'],
                            'font_size' => $text['fontSize'],
                            'font_color' => $text['fill'],
                            'text_align' => $text['textAlign'] ?? 'left',
                            'font_weight' => $text['fontWeight'] ?? 'normal',
                            'rotation' => $text['angle'],
                            'z_index' => $text['zIndex'] ?? 1,
                            'attendance_template_id' => $attendanceTemplate->id,
                        ]);
                    }
                }
            }

            // Process recipients and create users
            $importRecipients = new RecipientsImport();
            Excel::import($importRecipients, $recipientFile);
            $recipientsData = collect($importRecipients->rows)->unique('email')->values();

            $existingEmails = User::whereIn('email', $recipientsData->pluck('email'))->pluck('email')->toArray();
            $newUsers = [];
            $now = now();

            foreach ($recipientsData as $row) {
                if (!in_array($row['email'], $existingEmails)) {
                    $newUsers[] = [
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'password' => bcrypt('password123'),
                        'phone' => $row['phone_number'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($newUsers)) {
                try {
                    User::insert($newUsers);
                } catch (\Exception $e) {
                    Log::error('Failed to insert users: ' . $e->getMessage(), ['newUsers' => $newUsers]);
                    throw $e;
                }

                $roleId = Role::where('name', 'user')->value('id');
                $newUserIds = User::whereIn('email', collect($newUsers)->pluck('email'))->pluck('id');
                $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
                    return [
                        'role_id' => $roleId,
                        'model_type' => User::class,
                        'model_id' => $userId,
                    ];
                })->toArray();
                DB::table('model_has_roles')->insert($roleAssignments);
            }

            $recipients = [];
            foreach ($recipientsData as $row) {
                $user = User::where('email', $row['email'])->first();
                $recipient = Recipient::firstOrCreate([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                ]);
                $recipients[] = $recipient;
            }

            // Load template data
            $documentDataImport = new DocumentDataImport();
            Excel::import($documentDataImport, $request->template_data_file_path);
            $documentRows = $documentDataImport->rows;

            $attendanceDataRows = [];
            if ($request->is_attendance_enabled) {
                $attendanceDataImport = new DocumentDataImport();
                Excel::import($attendanceDataImport, $request->attendance_template_data_file_path);
                $attendanceDataRows = $attendanceDataImport->rows;
            }

            // Generate documents
            $frontDocumentTemplate = $documentTemplate->templateFiles()->where('side', 'front')->first();
            if (!$frontDocumentTemplate) {
                throw new \Exception('Front document template not found');
            }
            $documentBackgroundPath = storage_path('app/public/' . $frontDocumentTemplate->file_path);

            // تغيير حجم الصورة الخلفية لتتناسب مع حجم القماش (900x600)
            $resizedBackgroundPath = storage_path('app/public/templates/resized_' . basename($frontDocumentTemplate->file_path));
            Image::make($documentBackgroundPath)->fit(900, 600)->save($resizedBackgroundPath);

            $canvasWidth = 900; // حجم القماش في الواجهة الأمامية
            $canvasHeight = 600;

            $firstPdf = null;
            foreach ($recipients as $index => $recipient) {
                $dataRow = $documentRows[$index] ?? [];
                $fields = DocumentField::where('document_template_id', $documentTemplate->id)->get();

                $renderedFields = [];
                foreach ($fields as $field) {
                    $value = $dataRow[$field->field_key] ?? $field->field_key;
                    $renderedFields[] = [
                        'text' => $value,
                        'x' => $field->position_x, // استخدام الإحداثيات كما هي
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
                    'background' => $resizedBackgroundPath, // استخدام الصورة المعاد تحجيمها
                    'fields' => $renderedFields,
                    'imageWidth' => $canvasWidth,
                    'imageHeight' => $canvasHeight,
                ])->setPaper([$canvasWidth, $canvasHeight], 'px');

                // للمعاينة
                return $pdf->stream('certificate-preview.pdf');

                // حفظ الشهادة
                $pdfPath = "certificates/{$user->id}/" . uniqid() . ".pdf";
                $pdfFullPath = storage_path("app/public/{$pdfPath}");
                Storage::disk('public')->makeDirectory("certificates/{$user->id}");
                $pdf->save($pdfFullPath);

                $uuid = Str::uuid();
                $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
                $qrPath = "qrcodes/{$uuid}.png";
                Storage::disk('public')->makeDirectory('qrcodes');
                Storage::disk('public')->put($qrPath, $qrCode);

                $document = Document::create([
                    'file_path' => $pdfPath,
                    'uuid' => $uuid,
                    'unique_code' => Str::random(10),
                    'qr_code_path' => $qrPath,
                    'status' => 'pending',
                    'document_template_id' => $documentTemplate->id,
                    'recipient_id' => $recipient->id,
                    'valid_from' => $documentTemplate->valid_from,
                    'valid_until' => $documentTemplate->valid_until,
                ]);

                SendCertificateJob::dispatch($document)->delay($documentTemplate->send_at);
            }

            // Generate attendance documents if enabled
            if ($request->is_attendance_enabled) {
                $frontAttendanceTemplate = $attendanceTemplate->templateFiles()->where('side', 'front')->first();
                if (!$frontAttendanceTemplate) {
                    throw new \Exception('Front attendance template not found');
                }
                $attendanceBackgroundPath = storage_path('app/public/' . $frontAttendanceTemplate->file_path);

                // تغيير حجم الصورة الخلفية لتتناسب مع حجم القماش
                $resizedAttendanceBackgroundPath = storage_path('app/public/templates/resized_' . basename($frontAttendanceTemplate->file_path));
                Image::make($attendanceBackgroundPath)->fit(900, 600)->save($resizedAttendanceBackgroundPath);

                foreach ($recipients as $index => $recipient) {
                    $dataRow = $attendanceDataRows[$index] ?? [];
                    $fields = AttendanceDocumentField::where('attendance_template_id', $attendanceTemplate->id)->get();
                    $renderedFields = [];
                    foreach ($fields as $field) {
                        $value = $dataRow[$field->field_key] ?? '';
                        $renderedFields[] = [
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

                    $pdfPath = "attendance_certificates/{$user->id}/" . uniqid() . ".pdf";
                    $pdfFullPath = storage_path("app/public/{$pdfPath}");
                    Storage::disk('public')->makeDirectory("attendance_certificates/{$user->id}");

                    $pdf = Pdf::loadView('pdf.template', [
                        'background' => $resizedAttendanceBackgroundPath,
                        'fields' => $renderedFields,
                        'imageWidth' => $canvasWidth,
                        'imageHeight' => $canvasHeight,
                    ])->setPaper([$canvasWidth, $canvasHeight], 'px');

                    $pdf->save($pdfFullPath);

                    $uuid = Str::uuid();
                    $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
                    $qrPath = "qrcodes/{$uuid}.png";
                    Storage::disk('public')->makeDirectory('qrcodes');
                    Storage::disk('public')->put($qrPath, $qrCode);

                    $attendanceDocument = AttendanceDocument::create([
                        'file_path' => $pdfPath,
                        'uuid' => $uuid,
                        'unique_code' => Str::random(10),
                        'qr_code_path' => $qrPath,
                        'status' => 'pending',
                        'attendance_template_id' => $attendanceTemplate->id,
                        'recipient_id' => $recipient->id,
                        'valid_from' => $attendanceTemplate->valid_from,
                        'valid_until' => $attendanceTemplate->valid_until,
                    ]);

                    SendCertificateJob::dispatch($attendanceDocument)->delay($attendanceTemplate->send_at);
                }
            }

            // Deduct balance
            $subscription->decrement('remaining', $recipientRows);

            DB::commit();
            return back()->with('success', 'تم إنشاء الحدث وتجهيز الشهادات بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating documents: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إنشاء الحدث: ' . $e->getMessage());
        }
    }



    public function storeV2(Request $request)
    {
//        dd(json_decode($_POST['certificate_text_data'], true)['document_template_file_path[]-front'] ?? null);
//        dd($request->all());
        set_time_limit(300);

        // Validate request data
        $request->validate([
            'event_title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'document_title' => 'required|string|max:255',
            'document_message' => 'required|string',
            'document_send_at' => 'required|date',
            'document_send_via' => 'required|array',
            'document_send_via.*' => 'in:email,sms,whatsapp',
            'recipient_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
            'template_data_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
            'document_template_file_path' => 'required|array',
            'document_template_file_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'template_sides' => 'required|array',
            'template_sides.*' => 'in:front,back',
            'document_validity' => 'required|in:permanent,temporary',
            'valid_from' => 'nullable|required_if:document_validity,temporary|date',
            'valid_until' => 'nullable|required_if:document_validity,temporary|date|after_or_equal:valid_from',
            'certificate_text_data' => 'required|json',
            'is_attendance_enabled' => 'nullable|boolean',
            'attendance_send_at' => 'nullable|required_if:is_attendance_enabled,true|date',
            'attendance_message' => 'nullable|required_if:is_attendance_enabled,true|string',
            'attendance_template_data_file_path' => 'nullable|required_if:is_attendance_enabled,true|file|mimes:xlsx,xls|max:2048',
            'attendance_template_file_path' => 'nullable|required_if:is_attendance_enabled,true|array',
            'attendance_template_file_path.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'attendance_template_sides' => 'nullable|required_if:is_attendance_enabled,true|array',
            'attendance_template_sides.*' => 'nullable|in:front,back',
            'attendance_validity' => 'nullable|required_if:is_attendance_enabled,true|in:permanent,temporary',
            'attendance_valid_from' => 'nullable|required_if:attendance_validity,temporary|date',
            'attendance_valid_until' => 'nullable|required_if:attendance_validity,temporary|date|after_or_equal:attendance_valid_from',
            'attendance_text_data' => 'nullable|required_if:is_attendance_enabled,true|json',
        ]);

        // Check user's subscription and balance
        $user = auth()->user();
        $subscription = $user->subscription;
        $recipientFile = $request->file('recipient_file_path');
        $recipientRows = Excel::toCollection(new RecipientsImport(), $recipientFile)->first()->count();

        if ($subscription->remaining < $recipientRows) {
            return back()->with('error', "عدد الشهادات المطلوبة ($recipientRows) أكبر من رصيدك الحالي ({$subscription->remaining}).");
        }

        DB::beginTransaction();

        try {
            // Create event and slug
            $event = Event::create([
                'title' => $request->event_title,
                'issuer' => $request->issuer,
                'start_date' => $request->event_start_date,
                'end_date' => $request->event_end_date,
                'user_id' => $user->id,
                'slug' => Str::slug($request->event_title) . '-' . uniqid(),
            ]);

            // Handle document template
            $documentTemplate = DocumentTemplate::create([
                'title' => $request->document_title,
                'message' => $request->document_message,
                'send_at' => $request->document_send_at,
                'send_via' => json_encode($request->document_send_via),
                'is_attendance_enabled' => $request->is_attendance_enabled ?? false,
                'event_id' => $event->id,
                'validity' => $request->document_validity,
            ]);

            if ($request->document_validity === 'temporary') {
                $documentTemplate->valid_from = $request->valid_from;
                $documentTemplate->valid_until = $request->valid_until;
                $documentTemplate->save();
            }

            // Store document template data file
            ExcelUpload::create([
                'file_path' => $request->template_data_file_path->store('uploads'),
                'upload_type' => 'document_data',
                'event_id' => $event->id,
            ]);

            // Process document template files
            $documentTemplateFiles = $request->file('document_template_file_path', []);
            $documentTemplateSides = $request->input('template_sides', []);
            foreach ($documentTemplateFiles as $index => $file) {
                $side = $documentTemplateSides[$index] ?? 'front';
                $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
                $documentTemplate->templateFiles()->create([
                    'file_path' => $file->store('templates', 'public'),
                    'file_type' => $fileType,
                    'side' => $side,
                ]);
            }

            // Save document field positions
            // الجديد: التصحيح هنا
            $certificateTextDataDecoded = json_decode($request->certificate_text_data, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($certificateTextDataDecoded) || empty($certificateTextDataDecoded)) {
                throw new \Exception('Failed to decode certificate_text_data JSON or data is malformed.');
            }

            $firstCardId = array_key_first($certificateTextDataDecoded);
            $documentCanvasData = $certificateTextDataDecoded[$firstCardId];

            if (!is_array($documentCanvasData)) {
                throw new \Exception('Document canvas data is not in expected format.');
            }

            $documentCanvasWidth = $documentCanvasData['canvasWidth'] ?? 900;
            $documentCanvasHeight = $documentCanvasData['canvasHeight'] ?? 600;
            $documentTextsData = $documentCanvasData['texts'] ?? []; // هذه هي مصفوفة النصوص التي تريد التكرار عليها

            if (!is_array($documentTextsData)) {
                throw new \Exception('Document texts data is not in expected format.');
            }

// الآن كرر فقط على النصوص الفعلية
            foreach ($documentTextsData as $text) {
                if (!is_array($text)) {
                    throw new \Exception('Individual document text item is malformed.');
                }
                DocumentField::create([
                    'field_key' => $text['text'],
                    'label' => $text['text'],
                    'position_x' => $text['left'],
                    'position_y' => $text['top'],
                    'width' => $text['width'] ?? null,
                    'height' => $text['height'] ?? null,
                    'font_family' => $text['fontFamily'],
                    'font_size' => $text['fontSize'],
                    'font_color' => $text['fill'],
                    'text_align' => $text['textAlign'] ?? 'left',
                    'font_weight' => $text['fontWeight'] ?? 'normal',
                    'rotation' => $text['angle'],
                    'z_index' => $text['zIndex'] ?? 1,
                    'document_template_id' => $documentTemplate->id,
                ]);
            }
            // Handle attendance if enabled
            $attendanceTemplate = null;
            if ($request->is_attendance_enabled) {
                $attendanceTemplate = AttendanceTemplate::create([
                    'message' => $request->attendance_message,
                    'send_at' => $request->attendance_send_at,
                    'send_via' => json_encode($request->document_send_via),
                    'event_id' => $event->id,
                    'validity' => $request->attendance_validity,
                ]);

                if ($request->attendance_validity === 'temporary') {
                    $attendanceTemplate->valid_from = $request->attendance_valid_from;
                    $attendanceTemplate->valid_until = $request->attendance_valid_until;
                    $attendanceTemplate->save();
                }

                // Store attendance template data file
                $attendanceTemplate->excelUploads()->create([
                    'file_path' => $request->attendance_template_data_file_path->store('uploads'),
                ]);

                // Process attendance template files
                $attendanceTemplateFiles = $request->file('attendance_template_file_path', []);
                $attendanceTemplateSides = $request->input('attendance_template_sides', []);
                foreach ($attendanceTemplateFiles as $index => $file) {
                    $side = $attendanceTemplateSides[$index] ?? 'front';
                    $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
                    $attendanceTemplate->templateFiles()->create([
                        'file_path' => $file->store('templates', 'public'),
                        'file_type' => $fileType,
                        'side' => $side,
                    ]);
                }

                // Save attendance field positions
                // الجديد: التصحيح هنا
                $attendanceTextDataDecoded = json_decode($request->attendance_text_data, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($attendanceTextDataDecoded) || empty($attendanceTextDataDecoded)) {
                    throw new \Exception('Failed to decode attendance_text_data JSON or data is malformed.');
                }

                $firstAttendanceCardId = array_key_first($attendanceTextDataDecoded);
                $attendanceCanvasData = $attendanceTextDataDecoded[$firstAttendanceCardId];

                if (!is_array($attendanceCanvasData)) {
                    throw new \Exception('Attendance canvas data is not in expected format.');
                }

                $attendanceCanvasWidth = $attendanceCanvasData['canvasWidth'] ?? 900;
                $attendanceCanvasHeight = $attendanceCanvasData['canvasHeight'] ?? 600;
                $attendanceTextsData = $attendanceCanvasData['texts'] ?? []; // هذه هي مصفوفة النصوص التي تريد التكرار عليها

                if (!is_array($attendanceTextsData)) {
                    throw new \Exception('Attendance texts data is not in expected format.');
                }

// الآن كرر فقط على النصوص الفعلية
                foreach ($attendanceTextsData as $text) {
                    if (!is_array($text)) {
                        throw new \Exception('Individual attendance text item is malformed.');
                    }
                    AttendanceDocumentField::create([
                        'field_key' => $text['text'],
                        'label' => $text['text'],
                        'position_x' => $text['left'],
                        'position_y' => $text['top'],
                        'width' => $text['width'] ?? null,
                        'height' => $text['height'] ?? null,
                        'font_family' => $text['fontFamily'],
                        'font_size' => $text['fontSize'],
                        'font_color' => $text['fill'],
                        'text_align' => $text['textAlign'] ?? 'left',
                        'font_weight' => $text['fontWeight'] ?? 'normal',
                        'rotation' => $text['angle'],
                        'z_index' => $text['zIndex'] ?? 1,
                        'attendance_template_id' => $attendanceTemplate->id,
                    ]);
                }
            }

            // Process recipients and create users
            $importRecipients = new RecipientsImport();
            Excel::import($importRecipients, $recipientFile);
            $recipientsData = collect($importRecipients->rows)->unique('email')->values();

            $existingEmails = User::whereIn('email', $recipientsData->pluck('email'))->pluck('email')->toArray();
            $newUsers = [];
            $now = now();

            foreach ($recipientsData as $row) {
                if (!in_array($row['email'], $existingEmails)) {
                    $newUsers[] = [
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'password' => bcrypt('password123'),
                        'phone' => $row['phone_number'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($newUsers)) {
                try {
                    User::insert($newUsers);
                } catch (\Exception $e) {
                    Log::error('Failed to insert users: ' . $e->getMessage(), ['newUsers' => $newUsers]);
                    throw $e;
                }

                $roleId = Role::where('name', 'user')->value('id');
                $newUserIds = User::whereIn('email', collect($newUsers)->pluck('email'))->pluck('id');
                $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
                    return [
                        'role_id' => $roleId,
                        'model_type' => User::class,
                        'model_id' => $userId,
                    ];
                })->toArray();
                DB::table('model_has_roles')->insert($roleAssignments);
            }

            $recipients = [];
            foreach ($recipientsData as $row) {
                $user = User::where('email', $row['email'])->first();
                $recipient = Recipient::firstOrCreate([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                ]);
                $recipients[] = $recipient;
            }

            // Load template data
            $documentDataImport = new DocumentDataImport();
            Excel::import($documentDataImport, $request->template_data_file_path);
            $documentRows = $documentDataImport->rows;

            $attendanceDataRows = [];
            if ($request->is_attendance_enabled) {
                $attendanceDataImport = new DocumentDataImport();
                Excel::import($attendanceDataImport, $request->attendance_template_data_file_path);
                $attendanceDataRows = $attendanceDataImport->rows;
            }

            // Generate documents
            $frontDocumentTemplate = $documentTemplate->templateFiles()->where('side', 'front')->first();
            if (!$frontDocumentTemplate) {
                throw new \Exception('Front document template not found');
            }
            $documentBackgroundPath = storage_path('app/public/' . $frontDocumentTemplate->file_path);

            $image = Image::read($documentBackgroundPath)->resize(900, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($documentBackgroundPath);

            $canvasWidth = 900; // حجم القماش
            $canvasHeight = 600;

            $firstPdf = null;
            foreach ($recipients as $index => $recipient) {
                $dataRow = $documentRows[$index] ?? [];
                $fields = DocumentField::where('document_template_id', $documentTemplate->id)->get();

                $renderedFields = [];
                foreach ($fields as $field) {
                    $value = $dataRow[$field->field_key] ?? $field->field_key;
                    $x = $field->position_x;
                    $y = $field->position_y;
                    Log::info("Field: {$value}, Original X: {$field->position_x}, Converted X: {$x}, Original Y: {$field->position_y}, Converted Y: {$y}");
                    $renderedFields[] = [
                        'text' => $value,
                        'x' => $x,
                        'y' => $y,
                        'font' => $field->font_family,
                        'size' => $field->font_size,
                        'color' => $field->font_color,
                        'align' => $field->text_align,
                        'weight' => $field->font_weight,
                        'rotation' => $field->rotation,
                    ];
                }

                $pdf = Pdf::loadView('pdf.template', [
                    'background' => $documentBackgroundPath,
                    'fields' => $renderedFields,
                    'canvasWidth' => $documentCanvasWidth,   // <--- تم تغيير الاسم هنا
                    'canvasHeight' => $documentCanvasHeight, // <--- تم تغيير الاسم هنا
                ]);
                // استخدم نفس القيم لتعيين حجم ورقة الـ PDF
                $pdf->setPaper([0, 0, $documentCanvasWidth, $documentCanvasHeight], 'custom');

                // للمعاينة
                return $pdf->stream('certificate-preview.pdf');

                // حفظ الشهادة
                $pdfPath = "certificates/{$user->id}/" . uniqid() . ".pdf";
                $pdfFullPath = storage_path("app/public/{$pdfPath}");
                Storage::disk('public')->makeDirectory("certificates/{$user->id}");
                $pdf->save($pdfFullPath);

                $uuid = Str::uuid();
                $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
                $qrPath = "qrcodes/{$uuid}.png";
                Storage::disk('public')->makeDirectory('qrcodes');
                Storage::disk('public')->put($qrPath, $qrCode);

                $document = Document::create([
                    'file_path' => $pdfPath,
                    'uuid' => $uuid,
                    'unique_code' => Str::random(10),
                    'qr_code_path' => $qrPath,
                    'status' => 'pending',
                    'document_template_id' => $documentTemplate->id,
                    'recipient_id' => $recipient->id,
                    'valid_from' => $documentTemplate->valid_from,
                    'valid_until' => $documentTemplate->valid_until,
                ]);

                SendCertificateJob::dispatch($document)->delay($documentTemplate->send_at);
            }

            // Generate attendance documents if enabled
            if ($request->is_attendance_enabled) {
                $frontAttendanceTemplate = $attendanceTemplate->templateFiles()->where('side', 'front')->first();
                if (!$frontAttendanceTemplate) {
                    throw new \Exception('Front attendance template not found');
                }
                $attendanceBackgroundPath = storage_path('app/public/' . $frontAttendanceTemplate->file_path);

                foreach ($recipients as $index => $recipient) {
                    $dataRow = $attendanceDataRows[$index] ?? [];
                    $fields = AttendanceDocumentField::where('attendance_template_id', $attendanceTemplate->id)->get();
                    $renderedFields = [];
                    foreach ($fields as $field) {
                        $value = $dataRow[$field->field_key] ?? '';
                        $renderedFields[] = [
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

                    $pdfPath = "attendance_certificates/{$user->id}/" . uniqid() . ".pdf";
                    $pdfFullPath = storage_path("app/public/{$pdfPath}");
                    Storage::disk('public')->makeDirectory("attendance_certificates/{$user->id}");

                    $pdf = Pdf::loadView('pdf.template', [
                        'background' => $attendanceBackgroundPath,
                        'fields' => $renderedFields,
                        'imageWidth' => $canvasWidth,
                        'imageHeight' => $canvasHeight,
                    ])->setPaper([$canvasWidth, $canvasHeight], 'px');

                    $pdf->save($pdfFullPath);

                    $uuid = Str::uuid();
                    $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
                    $qrPath = "qrcodes/{$uuid}.png";
                    Storage::disk('public')->makeDirectory('qrcodes');
                    Storage::disk('public')->put($qrPath, $qrCode);

                    $attendanceDocument = AttendanceDocument::create([
                        'file_path' => $pdfPath,
                        'uuid' => $uuid,
                        'unique_code' => Str::random(10),
                        'qr_code_path' => $qrPath,
                        'status' => 'pending',
                        'attendance_template_id' => $attendanceTemplate->id,
                        'recipient_id' => $recipient->id,
                        'valid_from' => $attendanceTemplate->valid_from,
                        'valid_until' => $attendanceTemplate->valid_until,
                    ]);

                    SendCertificateJob::dispatch($attendanceDocument)->delay($attendanceTemplate->send_at);
                }
            }

            // Deduct balance
            $subscription->decrement('remaining', $recipientRows);

            DB::commit();
            return back()->with('success', 'تم إنشاء الحدث وتجهيز الشهادات بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating documents: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إنشاء الحدث: ' . $e->getMessage());
        }
    }


//    public function store(Request $request)
//    {
//        dd($request->all());
//        set_time_limit(300); // 5 دقائق كحد أقصى
//
//        // Validate request data
//        $request->validate([
//            'event_title' => 'required|string|max:255',
//            'issuer' => 'required|string|max:255',
//            'event_start_date' => 'required|date',
//            'event_end_date' => 'required|date|after_or_equal:event_start_date',
//            'document_title' => 'required|string|max:255',
//            'document_message' => 'required|string',
//            'document_send_at' => 'required|date',
//            'document_send_via' => 'required|array',
//            'document_send_via.*' => 'in:email,sms,whatsapp',
//            'recipient_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
//            'template_data_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
//            'document_template_file_path' => 'required|array',
//            'document_template_file_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
//            'template_sides' => 'required|array',
//            'template_sides.*' => 'in:front,back',
//            'document_validity' => 'required|in:permanent,temporary',
//            'valid_from' => 'nullable|required_if:document_validity,temporary|date',
//            'valid_until' => 'nullable|required_if:document_validity,temporary|date|after_or_equal:valid_from',
//            'certificate_text_data' => 'required|json',
//            'is_attendance_enabled' => 'nullable|boolean',
//            'attendance_send_at' => 'nullable|required_if:is_attendance_enabled,true|date',
//            'attendance_message' => 'nullable|required_if:is_attendance_enabled,true|string',
//            'attendance_template_data_file_path' => 'nullable|required_if:is_attendance_enabled,true|file|mimes:xlsx,xls|max:2048',
//            'attendance_template_file_path' => 'nullable|required_if:is_attendance_enabled,true|array',
//            'attendance_template_file_path.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
//            'attendance_template_sides' => 'nullable|required_if:is_attendance_enabled,true|array',
//            'attendance_template_sides.*' => 'nullable|in:front,back',
//            'attendance_validity' => 'nullable|required_if:is_attendance_enabled,true|in:permanent,temporary',
//            'attendance_valid_from' => 'nullable|required_if:attendance_validity,temporary|date',
//            'attendance_valid_until' => 'nullable|required_if:attendance_validity,temporary|date|after_or_equal:attendance_valid_from',
//            'attendance_text_data' => 'nullable|required_if:is_attendance_enabled,true|json',
//        ]);
//
//        // Check user's subscription and balance
//        $user = auth()->user();
//        $subscription = $user->subscription;
//        $recipientFile = $request->file('recipient_file_path');
//        $recipientRows = Excel::toCollection(new RecipientsImport(), $recipientFile)->first()->count();
//
//        if ($subscription->remaining < $recipientRows) {
//            return back()->with('error', "عدد الشهادات المطلوبة ($recipientRows) أكبر من رصيدك الحالي ({$subscription->remaining}).");
//        }
//
//        DB::beginTransaction();
//
//        try {
//            // Create event and slug
//            $event = Event::create([
//                'title' => $request->event_title,
//                'issuer' => $request->issuer,
//                'start_date' => $request->event_start_date,
//                'end_date' => $request->event_end_date,
//                'user_id' => $user->id,
//                'slug' => Str::slug($request->event_title) . '-' . uniqid(),
//            ]);
//
//            // Handle document template
//            $documentTemplate = DocumentTemplate::create([
//                'title' => $request->document_title,
//                'message' => $request->document_message,
//                'send_at' => $request->document_send_at,
//                'send_via' => json_encode($request->document_send_via),
//                'is_attendance_enabled' => $request->is_attendance_enabled ?? false,
//                'event_id' => $event->id,
//                'validity' => $request->document_validity,
//            ]);
//
//            if ($request->document_validity === 'temporary') {
//                $documentTemplate->valid_from = $request->valid_from;
//                $documentTemplate->valid_until = $request->valid_until;
//                $documentTemplate->save();
//            }
//
//            // Store document template data file
//            ExcelUpload::create([
//                'file_path' => $request->template_data_file_path->store('uploads'),
//                'upload_type' => 'document_data',
//                'event_id' => $event->id,
//            ]);
//
//            // Process document template files (front and back)
//            $documentTemplateFiles = $request->file('document_template_file_path', []);
//            $documentTemplateSides = $request->input('template_sides', []);
//
//            foreach ($documentTemplateFiles as $index => $file) {
//                $side = $documentTemplateSides[$index] ?? 'front';
//                $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
//                $documentTemplate->templateFiles()->create([
//                    'file_path' => $file->store('templates', 'public'), // المسار ده هو اللي هتحتاجه
//                    'file_type' => $fileType,
//                    'side' => $side,
//                ]);
//            }
//
//            // Save document field positions (for all sides)
//            $certificateTextDataDecoded = json_decode($request->certificate_text_data, true);
//
//            if (json_last_error() !== JSON_ERROR_NONE || !is_array($certificateTextDataDecoded) || empty($certificateTextDataDecoded)) {
//                throw new \Exception('Failed to decode certificate_text_data JSON or data is malformed.');
//            }
//
//            // Get canvas dimensions from the front side for PDF rendering
//            // تأكد أن هذا المفتاح يتطابق مع الـ cardId اللي بيتبعت من الـ Frontend للوش الأمامي للشهادة
//            $documentFrontCardId = 'document_template_file_path[]-front'; // الافتراضي
//            $documentCanvasWidth = $certificateTextDataDecoded[$documentFrontCardId]['canvasWidth'] ?? 900;
//            $documentCanvasHeight = $certificateTextDataDecoded[$documentFrontCardId]['canvasHeight'] ?? 600;
//
//            // Iterate over each side's data (front and back)
//            foreach ($certificateTextDataDecoded as $cardId => $canvasData) {
//                if (!is_array($canvasData) || !isset($canvasData['texts'])) {
//                    continue; // تخطي البيانات غير الصالحة
//                }
//                $documentTextsData = $canvasData['texts'];
//
//                // استخراج السايد من الـ cardId
//                // مثال: document_template_file_path[]-front -> front
//                $side = Str::contains($cardId, '-back') ? 'back' : 'front';
//
//                foreach ($documentTextsData as $text) {
//                    if (!is_array($text)) {
//                        continue; // تخطي العنصر النصي غير الصالح
//                    }
//                    DocumentField::create([
//                        'field_key' => $text['text'], // هذا هو النص الذي كتبه المستخدم
//                        'label' => $text['text'],
//                        'position_x' => $text['left'],
//                        'position_y' => $text['top'],
//                        'width' => $text['width'] ?? null,
//                        'height' => $text['height'] ?? null,
//                        'font_family' => $text['fontFamily'],
//                        'font_size' => $text['fontSize'],
//                        'font_color' => $text['fill'],
//                        'text_align' => $text['textAlign'] ?? 'left',
//                        'font_weight' => $text['fontWeight'] ?? 'normal',
//                        'rotation' => $text['angle'],
//                        'z_index' => $text['zIndex'] ?? 1,
//                        'document_template_id' => $documentTemplate->id,
//                        'side' => $side, // حفظ السايد الذي ينتمي إليه الحقل
//                    ]);
//                }
//            }
//
//
//            // Handle attendance if enabled
//            $attendanceTemplate = null;
//            $attendanceCanvasWidth = 900;
//            $attendanceCanvasHeight = 600;
//
//            if ($request->is_attendance_enabled) {
//                $attendanceTemplate = AttendanceTemplate::create([
//                    'message' => $request->attendance_message,
//                    'send_at' => $request->attendance_send_at,
//                    'send_via' => json_encode($request->document_send_via), // يمكن أن تكون مختلفة عن الشهادة
//                    'event_id' => $event->id,
//                    'validity' => $request->attendance_validity,
//                ]);
//
//                if ($request->attendance_validity === 'temporary') {
//                    $attendanceTemplate->valid_from = $request->attendance_valid_from;
//                    $attendanceTemplate->valid_until = $request->attendance_valid_until;
//                    $attendanceTemplate->save();
//                }
//
//                // Store attendance template data file (Excel)
//                $attendanceTemplate->excelUploads()->create([
//                    'file_path' => $request->attendance_template_data_file_path->store('uploads'),
//                ]);
//
//                // Process attendance template files (front and back)
//                $attendanceTemplateFiles = $request->file('attendance_template_file_path', []);
//                $attendanceTemplateSides = $request->input('attendance_template_sides', []);
//
//                foreach ($attendanceTemplateFiles as $index => $file) {
//                    $side = $attendanceTemplateSides[$index] ?? 'front';
//                    $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
//                    $attendanceTemplate->templateFiles()->create([
//                        'file_path' => $file->store('templates', 'public'),
//                        'file_type' => $fileType,
//                        'side' => $side,
//                    ]);
//                }
//
//                // Save attendance field positions (for all sides)
//                $attendanceTextDataDecoded = json_decode($request->attendance_text_data, true);
//
//                if (json_last_error() !== JSON_ERROR_NONE || !is_array($attendanceTextDataDecoded) || empty($attendanceTextDataDecoded)) {
//                    throw new \Exception('Failed to decode attendance_text_data JSON or data is malformed.');
//                }
//
//                // Get canvas dimensions from the front side for PDF rendering
//                // تأكد أن هذا المفتاح يتطابق مع الـ cardId اللي بيتبعت من الـ Frontend للوش الأمامي للحضور
//                $attendanceFrontCardId = 'attendance_template_data_file_path-front'; // الافتراضي
//                $attendanceCanvasWidth = $attendanceTextDataDecoded[$attendanceFrontCardId]['canvasWidth'] ?? 900;
//                $attendanceCanvasHeight = $attendanceTextDataDecoded[$attendanceFrontCardId]['canvasHeight'] ?? 600;
//
//                // Iterate over each side's data (front and back)
//                foreach ($attendanceTextDataDecoded as $cardId => $canvasData) {
//                    if (!is_array($canvasData) || !isset($canvasData['texts'])) {
//                        continue; // تخطي البيانات غير الصالحة
//                    }
//                    $attendanceTextsData = $canvasData['texts'];
//
//                    // استخراج السايد من الـ cardId
//                    // مثال: attendance_template_data_file_path-front -> front
//                    $side = Str::contains($cardId, '-back') ? 'back' : 'front';
//
//                    foreach ($attendanceTextsData as $text) {
//                        if (!is_array($text)) {
//                            continue; // تخطي العنصر النصي غير الصالح
//                        }
//                        AttendanceDocumentField::create([
//                            'field_key' => $text['text'],
//                            'label' => $text['text'],
//                            'position_x' => $text['left'],
//                            'position_y' => $text['top'],
//                            'width' => $text['width'] ?? null,
//                            'height' => $text['height'] ?? null,
//                            'font_family' => $text['fontFamily'],
//                            'font_size' => $text['fontSize'],
//                            'font_color' => $text['fill'],
//                            'text_align' => $text['textAlign'] ?? 'left',
//                            'font_weight' => $text['fontWeight'] ?? 'normal',
//                            'rotation' => $text['angle'],
//                            'z_index' => $text['zIndex'] ?? 1,
//                            'attendance_template_id' => $attendanceTemplate->id,
//                            'side' => $side, // حفظ السايد الذي ينتمي إليه الحقل
//                        ]);
//                    }
//                }
//            }
//
//
//            // Process recipients and create users
//            $importRecipients = new RecipientsImport();
//            Excel::import($importRecipients, $recipientFile);
//            $recipientsData = collect($importRecipients->rows)->unique('email')->values();
//
//            $existingEmails = User::whereIn('email', $recipientsData->pluck('email'))->pluck('email')->toArray();
//            $newUsers = [];
//            $now = now();
//
//            foreach ($recipientsData as $row) {
//                if (!in_array($row['email'], $existingEmails)) {
//                    $newUsers[] = [
//                        'name' => $row['name'],
//                        'email' => $row['email'],
//                        'password' => bcrypt('password123'),
//                        'phone' => $row['phone_number'] ?? null,
//                        'created_at' => $now,
//                        'updated_at' => $now,
//                    ];
//                }
//            }
//
//            if (!empty($newUsers)) {
//                try {
//                    User::insert($newUsers);
//                } catch (\Exception $e) {
//                    Log::error('Failed to insert users: ' . $e->getMessage(), ['newUsers' => $newUsers]);
//                    throw $e;
//                }
//
//                $roleId = Role::where('name', 'user')->value('id');
//                $newUserIds = User::whereIn('email', collect($newUsers)->pluck('email'))->pluck('id');
//                $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
//                    return [
//                        'role_id' => $roleId,
//                        'model_type' => User::class,
//                        'model_id' => $userId,
//                    ];
//                })->toArray();
//                DB::table('model_has_roles')->insert($roleAssignments);
//            }
//
//            $recipients = [];
//            foreach ($recipientsData as $row) {
//                $user = User::where('email', $row['email'])->first();
//                $recipient = Recipient::firstOrCreate([
//                    'event_id' => $event->id,
//                    'user_id' => $user->id,
//                ]);
//                $recipients[] = $recipient;
//            }
//
//            // Load template data
//            $documentDataImport = new DocumentDataImport();
//            Excel::import($documentDataImport, $request->template_data_file_path);
//            $documentRows = $documentDataImport->rows;
//
//            $attendanceDataRows = [];
//            if ($request->is_attendance_enabled) {
//                $attendanceDataImport = new DocumentDataImport();
//                Excel::import($attendanceDataImport, $request->attendance_template_data_file_path);
//                $attendanceDataRows = $attendanceDataImport->rows;
//            }
//
//
//            // --- Logic for Document Generation (Certificates) ---
//            $documentTemplateFiles = $documentTemplate->templateFiles; // جلب كل ملفات القالب (وش و ضهر)
//            $documentFrontTemplate = $documentTemplateFiles->where('side', 'front')->first();
//            $documentBackTemplate = $documentTemplateFiles->where('side', 'back')->first();
//
//            if (!$documentFrontTemplate) {
//                throw new \Exception('Front document template not found.');
//            }
//
//            // تحميل صورة الخلفية للوجه الأمامي لمرة واحدة
//            $documentBackgroundPathFront = storage_path('app/public/' . $documentFrontTemplate->file_path);
//            $imgFront = Image::read($documentBackgroundPathFront);
//            // لاحظ: لا تقم بحفظ الصورة هنا إلا إذا كنت تريد تغيير حجمها بشكل دائم.
//            // إذا كان الـ HTML/CSS هو من سيتحكم في الحجم في PDF، فهذا ليس ضرورياً هنا.
//            // $imgFront->resize(900, 600)->save($documentBackgroundPathFront); // لو عايز تصغر الحجم للـ PDF
//            // $documentCanvasWidth و $documentCanvasHeight هتكون القيم اللي جاية من الـ Frontend
//            // دي هي أبعاد الكانفاس الأصلي اللي المفروض نولد الـ PDF عليها
//            $documentPdfWidth = $documentCanvasWidth;
//            $documentPdfHeight = $documentCanvasHeight;
//
//
//            $documentBackBackgroundPath = null;
//            if ($documentBackTemplate) {
//                $documentBackBackgroundPath = storage_path('app/public/' . $documentBackTemplate->file_path);
//                // لا نحتاج لتحجيم الصورة الخلفية للوجه الخلفي هنا، فقط مسارها
//            }
//
//            foreach ($recipients as $index => $recipient) {
//                $dataRow = $documentRows[$index] ?? [];
//                $uuid = Str::uuid();
//
//                // Generate QR Code once per document
//                $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
//                $qrPath = "qrcodes/{$uuid}.png";
//                Storage::disk('public')->makeDirectory('qrcodes');
//                Storage::disk('public')->put($qrPath, $qrCode);
//
//                // --- Generate Front Side PDF ---
//                $frontFields = DocumentField::where('document_template_id', $documentTemplate->id)
//                    ->where('side', 'front')
//                    ->get();
//
//                $renderedFrontFields = [];
//                foreach ($frontFields as $field) {
//                    $value = $dataRow[$field->field_key] ?? $field->field_key;
//                    // هنا لو عايز تضيف QR Code لمكان معين في الشهادة الأمامية
//                    if ($field->field_key === 'qrcode_placeholder') { // افترض أن لديك حقل نصي اسمه 'qrcode_placeholder' في الـ Frontend
//                        $value = '<img src="' . Storage::disk('public')->url($qrPath) . '" style="width: ' . ($field->width ?? 200) . 'px; height: ' . ($field->height ?? 200) . 'px;">';
//                    }
//                    $renderedFrontFields[] = [
//                        'text' => $value,
//                        'x' => $field->position_x,
//                        'y' => $field->position_y,
//                        'font' => $field->font_family,
//                        'size' => $field->font_size,
//                        'color' => $field->font_color,
//                        'align' => $field->text_align,
//                        'weight' => $field->font_weight,
//                        'rotation' => $field->rotation,
//                    ];
//                }
//
//                $pdfFront = Pdf::loadView('pdf.template', [
//                    'background' => Storage::disk('public')->url($documentFrontTemplate->file_path), // استخدم مسار يمكن الوصول إليه من الـ public
//                    'fields' => $renderedFrontFields,
//                    'canvasWidth' => $documentPdfWidth,
//                    'canvasHeight' => $documentPdfHeight,
//                ]);
//                $pdfFront->setPaper([0, 0, $documentPdfWidth, $documentPdfHeight], 'custom');
//
//                // --- Generate Back Side PDF (if exists) ---
//                $pdfBack = null;
//                if ($documentBackTemplate) {
//                    $backFields = DocumentField::where('document_template_id', $documentTemplate->id)
//                        ->where('side', 'back')
//                        ->get();
//
//                    $renderedBackFields = [];
//                    foreach ($backFields as $field) {
//                        $value = $dataRow[$field->field_key] ?? $field->field_key;
//                        $renderedBackFields[] = [
//                            'text' => $value,
//                            'x' => $field->position_x,
//                            'y' => $field->position_y,
//                            'font' => $field->font_family,
//                            'size' => $field->font_size,
//                            'color' => $field->font_color,
//                            'align' => $field->text_align,
//                            'weight' => $field->font_weight,
//                            'rotation' => $field->rotation,
//                        ];
//                    }
//
//                    $pdfBack = Pdf::loadView('pdf.template', [
//                        'background' => Storage::disk('public')->url($documentBackTemplate->file_path),
//                        'fields' => $renderedBackFields,
//                        'canvasWidth' => $documentPdfWidth, // استخدم نفس أبعاد الكانفاس للوجه الخلفي
//                        'canvasHeight' => $documentPdfHeight,
//                    ]);
//                    $pdfBack->setPaper([0, 0, $documentPdfWidth, $documentPdfHeight], 'custom');
//                }
//
//                // --- Combine Front and Back (if applicable) and save PDF ---
//                $pdfMerger = new \Webklex\PDFMerger\PDFMerger();
//                $pdfMerger->addPDF($pdfFront->output());
//                if ($pdfBack) {
//                    $pdfMerger->addPDF($pdfBack->output());
//                }
//
//                $pdfPath = "certificates/{$user->id}/" . $uuid . ".pdf"; // استخدم الـ UUID لتسمية الملف
//                Storage::disk('public')->makeDirectory("certificates/{$user->id}");
//                $pdfMerger->merge('file', storage_path("app/public/{$pdfPath}"));
//
//
//                // --- Generate JPEG Image for Certificate (Optional) ---
//                // لو محتاج صورة من الـ PDF، ممكن تستخدم Imagick لو كانت مثبتة على السيرفر
//                // أو تقوم بتوليد صورة مباشرة من الـ HTML/Canvas لو كان الـ frontend بيرسل صورة جاهزة
//                $imagePath = null;
//                // مثال باستخدام Imagick (يتطلب تثبيت Imagick PHP extension والخادم)
//                /*
//                if (class_exists('Imagick')) {
//                    try {
//                        $imagick = new \Imagick();
//                        $imagick->readImage(storage_path("app/public/{$pdfPath}"));
//                        $imagick->setResolution(150, 150); // دقة الصورة
//                        $imagick->setImageFormat('jpeg');
//                        $imageFileName = str_replace('.pdf', '.jpeg', basename($pdfPath));
//                        $imagePath = "certificates_images/{$user->id}/{$imageFileName}";
//                        Storage::disk('public')->makeDirectory("certificates_images/{$user->id}");
//                        $imagick->writeImage(storage_path("app/public/{$imagePath}"));
//                        $imagick->clear();
//                        $imagick->destroy();
//                    } catch (\Exception $e) {
//                        Log::warning("Failed to generate certificate image for {$pdfPath}: " . $e->getMessage());
//                        $imagePath = null; // فشل توليد الصورة
//                    }
//                }
//                */
//
//                $document = Document::create([
//                    'file_path' => $pdfPath,
//                    'uuid' => $uuid,
//                    'unique_code' => Str::random(10), // ده ممكن يكون هو الـ UUID نفسه لو عايز
//                    'qr_code_path' => $qrPath,
//                    'status' => 'pending',
//                    'document_template_id' => $documentTemplate->id,
//                    'recipient_id' => $recipient->id,
//                    'valid_from' => $documentTemplate->valid_from,
//                    'valid_until' => $documentTemplate->valid_until,
//                    // 'image_path' => $imagePath, // حفظ مسار الصورة لو تم توليدها
//                ]);
//
//                // SendCertificateJob::dispatch($document)->delay($documentTemplate->send_at); // شغل الجوب بعد الانتهاء
//            }
//
//            // --- Logic for Attendance Document Generation (if enabled) ---
//            if ($request->is_attendance_enabled) {
//                $attendanceTemplateFiles = $attendanceTemplate->templateFiles; // جلب كل ملفات القالب (وش و ضهر)
//                $attendanceFrontTemplate = $attendanceTemplateFiles->where('side', 'front')->first();
//                $attendanceBackTemplate = $attendanceTemplateFiles->where('side', 'back')->first();
//
//                if (!$attendanceFrontTemplate) {
//                    throw new \Exception('Front attendance template not found.');
//                }
//
//                // تحميل صورة الخلفية للوجه الأمامي للحضور لمرة واحدة
//                $attendanceBackgroundPathFront = storage_path('app/public/' . $attendanceFrontTemplate->file_path);
//                $imgFrontAttendance = Image::read($attendanceBackgroundPathFront);
//                // نفس الملاحظات على التحجيم هنا
//
//                $attendancePdfWidth = $attendanceCanvasWidth; // اللي جاي من الـ Frontend
//                $attendancePdfHeight = $attendanceCanvasHeight;
//
//
//                $attendanceBackBackgroundPath = null;
//                if ($attendanceBackTemplate) {
//                    $attendanceBackBackgroundPath = storage_path('app/public/' . $attendanceBackTemplate->file_path);
//                }
//
//                foreach ($recipients as $index => $recipient) {
//                    $dataRow = $attendanceDataRows[$index] ?? [];
//                    $uuid = Str::uuid(); // UUID جديد لكل مستند حضور
//
//                    // لا تحتاج QR Code جديد هنا إلا إذا كان مختلفًا عن الشهادة
//                    $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid)); // أو route للـ Attendance
//                    $qrPath = "qrcodes_attendance/{$uuid}.png"; // مسار مختلف لـ QR الحضور
//                    Storage::disk('public')->makeDirectory('qrcodes_attendance');
//                    Storage::disk('public')->put($qrPath, $qrCode);
//
//                    // --- Generate Front Side Attendance PDF ---
//                    $frontAttendanceFields = AttendanceDocumentField::where('attendance_template_id', $attendanceTemplate->id)
//                        ->where('side', 'front')
//                        ->get();
//
//                    $renderedFrontAttendanceFields = [];
//                    foreach ($frontAttendanceFields as $field) {
//                        $value = $dataRow[$field->field_key] ?? $field->field_key;
//                        if ($field->field_key === 'qrcode_placeholder_attendance') { // placeholder مختلف للحضور
//                            $value = '<img src="' . Storage::disk('public')->url($qrPath) . '" style="width: ' . ($field->width ?? 200) . 'px; height: ' . ($field->height ?? 200) . 'px;">';
//                        }
//                        $renderedFrontAttendanceFields[] = [
//                            'text' => $value,
//                            'x' => $field->position_x,
//                            'y' => $field->position_y,
//                            'font' => $field->font_family,
//                            'size' => $field->font_size,
//                            'color' => $field->font_color,
//                            'align' => $field->text_align,
//                            'weight' => $field->font_weight,
//                            'rotation' => $field->rotation,
//                        ];
//                    }
//
//                    $pdfAttendanceFront = Pdf::loadView('pdf.template', [
//                        'background' => Storage::disk('public')->url($attendanceFrontTemplate->file_path),
//                        'fields' => $renderedFrontAttendanceFields,
//                        'canvasWidth' => $attendancePdfWidth,
//                        'canvasHeight' => $attendancePdfHeight,
//                    ]);
//                    $pdfAttendanceFront->setPaper([0, 0, $attendancePdfWidth, $attendancePdfHeight], 'custom');
//
//                    // --- Generate Back Side Attendance PDF (if exists) ---
//                    $pdfAttendanceBack = null;
//                    if ($attendanceBackTemplate) {
//                        $backAttendanceFields = AttendanceDocumentField::where('attendance_template_id', $attendanceTemplate->id)
//                            ->where('side', 'back')
//                            ->get();
//
//                        $renderedBackAttendanceFields = [];
//                        foreach ($backAttendanceFields as $field) {
//                            $value = $dataRow[$field->field_key] ?? $field->field_key;
//                            $renderedBackAttendanceFields[] = [
//                                'text' => $value,
//                                'x' => $field->position_x,
//                                'y' => $field->position_y,
//                                'font' => $field->font_family,
//                                'size' => $field->font_size,
//                                'color' => $field->font_color,
//                                'align' => $field->text_align,
//                                'weight' => $field->font_weight,
//                                'rotation' => $field->rotation,
//                            ];
//                        }
//
//                        $pdfAttendanceBack = Pdf::loadView('pdf.template', [
//                            'background' => Storage::disk('public')->url($attendanceBackTemplate->file_path),
//                            'fields' => $renderedBackAttendanceFields,
//                            'canvasWidth' => $attendancePdfWidth,
//                            'canvasHeight' => $attendancePdfHeight,
//                        ]);
//                        $pdfAttendanceBack->setPaper([0, 0, $attendancePdfWidth, $attendancePdfHeight], 'custom');
//                    }
//
//                    // --- Combine Front and Back (if applicable) and save PDF ---
//                    $pdfMergerAttendance = new \Webklex\PDFMerger\PDFMerger();
//                    $pdfMergerAttendance->addPDF($pdfAttendanceFront->output());
//                    if ($pdfAttendanceBack) {
//                        $pdfMergerAttendance->addPDF($pdfAttendanceBack->output());
//                    }
//
//                    $pdfPathAttendance = "attendance_certificates/{$user->id}/" . $uuid . ".pdf";
//                    Storage::disk('public')->makeDirectory("attendance_certificates/{$user->id}");
//                    $pdfMergerAttendance->merge('file', storage_path("app/public/{$pdfPathAttendance}"));
//
//                    // --- Generate JPEG Image for Attendance (Optional) ---
//                    $imagePathAttendance = null;
//                    /*
//                    if (class_exists('Imagick')) {
//                        try {
//                            $imagickAttendance = new \Imagick();
//                            $imagickAttendance->readImage(storage_path("app/public/{$pdfPathAttendance}"));
//                            $imagickAttendance->setResolution(150, 150);
//                            $imagickAttendance->setImageFormat('jpeg');
//                            $imageFileNameAttendance = str_replace('.pdf', '.jpeg', basename($pdfPathAttendance));
//                            $imagePathAttendance = "attendance_certificates_images/{$user->id}/{$imageFileNameAttendance}";
//                            Storage::disk('public')->makeDirectory("attendance_certificates_images/{$user->id}");
//                            $imagickAttendance->writeImage(storage_path("app/public/{$imagePathAttendance}"));
//                            $imagickAttendance->clear();
//                            $imagickAttendance->destroy();
//                        } catch (\Exception $e) {
//                            Log::warning("Failed to generate attendance image for {$pdfPathAttendance}: " . $e->getMessage());
//                            $imagePathAttendance = null;
//                        }
//                    }
//                    */
//
//                    $attendanceDocument = AttendanceDocument::create([
//                        'file_path' => $pdfPathAttendance,
//                        'uuid' => $uuid,
//                        'unique_code' => Str::random(10),
//                        'qr_code_path' => $qrPath, // استخدم نفس مسار الـ QR code الخاص بالحضور
//                        'status' => 'pending',
//                        'attendance_template_id' => $attendanceTemplate->id,
//                        'recipient_id' => $recipient->id,
//                        'valid_from' => $attendanceTemplate->valid_from,
//                        'valid_until' => $attendanceTemplate->valid_until,
//                        // 'image_path' => $imagePathAttendance,
//                    ]);
//
//                    // SendCertificateJob::dispatch($attendanceDocument)->delay($attendanceTemplate->send_at);
//                }
//            }
//
//            // Deduct balance
//            $subscription->decrement('remaining', $recipientRows);
//
//            DB::commit();
//            return back()->with('success', 'تم إنشاء الحدث وتجهيز الشهادات بنجاح.');
//        } catch (\Exception $e) {
//            DB::rollBack();
//            Log::error('Error generating documents: ' . $e->getMessage());
//            return back()->with('error', 'حدث خطأ أثناء إنشاء الحدث: ' . $e->getMessage());
//        }
//
//    }


//    public function store(Request $request)
//    {
//        set_time_limit(300);
//
//        // Validate request data
//        $request->validate([
//            'event_title' => 'required|string|max:255',
//            'issuer' => 'required|string|max:255',
//            'event_start_date' => 'required|date',
//            'event_end_date' => 'required|date|after_or_equal:event_start_date',
//            'document_title' => 'required|string|max:255',
//            'document_message' => 'required|string',
//            'document_send_at' => 'required|date',
//            'document_send_via' => 'required|array',
//            'document_send_via.*' => 'in:email,sms,whatsapp',
//            'recipient_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
//            'template_data_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
//            'document_template_file_path' => 'required|array',
//            'document_template_file_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
//            'template_sides' => 'required|array',
//            'template_sides.*' => 'in:front,back',
//            'document_validity' => 'required|in:permanent,temporary',
//            'valid_from' => 'nullable|required_if:document_validity,temporary|date',
//            'valid_until' => 'nullable|required_if:document_validity,temporary|date|after_or_equal:valid_from',
//            'certificate_text_data' => 'required|json',
//            'is_attendance_enabled' => 'nullable|boolean',
//            'attendance_send_at' => 'nullable|required_if:is_attendance_enabled,true|date',
//            'attendance_message' => 'nullable|required_if:is_attendance_enabled,true|string',
//            'attendance_template_data_file_path' => 'nullable|required_if:is_attendance_enabled,true|file|mimes:xlsx,xls|max:2048',
//            'attendance_template_file_path' => 'nullable|required_if:is_attendance_enabled,true|array',
//            'attendance_template_file_path.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
//            'attendance_template_sides' => 'nullable|required_if:is_attendance_enabled,true|array',
//            'attendance_template_sides.*' => 'nullable|in:front,back',
//            'attendance_validity' => 'nullable|required_if:is_attendance_enabled,true|in:permanent,temporary',
//            'attendance_valid_from' => 'nullable|required_if:attendance_validity,temporary|date',
//            'attendance_valid_until' => 'nullable|required_if:attendance_validity,temporary|date|after_or_equal:attendance_valid_from',
//            'attendance_text_data' => 'nullable|required_if:is_attendance_enabled,true|json',
//        ]);
//
//        // Check user's subscription and balance
//        $user = auth()->user();
//        $subscription = $user->subscription;
//        $recipientFile = $request->file('recipient_file_path');
//        $recipientRows = Excel::toCollection(new RecipientsImport(), $recipientFile)->first()->count();
//
//        if ($subscription->remaining < $recipientRows) {
//            return back()->with('error', "عدد الشهادات المطلوبة ($recipientRows) أكبر من رصيدك الحالي ({$subscription->remaining}).");
//        }
//
//        DB::beginTransaction();
//
//        try {
//            // Create event and slug
//            $event = Event::create([
//                'title' => $request->event_title,
//                'issuer' => $request->issuer,
//                'start_date' => $request->event_start_date,
//                'end_date' => $request->event_end_date,
//                'user_id' => $user->id,
//                'slug' => Str::slug($request->event_title) . '-' . uniqid(),
//            ]);
//
//            // Handle document template
//            $documentTemplate = DocumentTemplate::create([
//                'title' => $request->document_title,
//                'message' => $request->document_message,
//                'send_at' => $request->document_send_at,
//                'send_via' => json_encode($request->document_send_via),
//                'is_attendance_enabled' => $request->is_attendance_enabled ?? false,
//                'eventiyama_id' => $event->id,
//                'validity' => $request->document_validity,
//            ]);
//
//            if ($request->document_validity === 'temporary') {
//                $documentTemplate->valid_from = $request->valid_from;
//                $documentTemplate->valid_until = $request->valid_until;
//                $documentTemplate->save();
//            }
//
//            // Store document template data file
//            ExcelUpload::create([
//                'file_path' => $request->template_data_file_path->store('uploads'),
//                'upload_type' => 'document_data',
//                'event_id' => $event->id,
//            ]);
//
//            // Process document template files
//            $documentTemplateFiles = $request->file('document_template_file_path', []);
//            $documentTemplateSides = $request->input('template_sides', []);
//            foreach ($documentTemplateFiles as $index => $file) {
//                $side = $documentTemplateSides[$index] ?? 'front';
//                $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
//                $documentTemplate->templateFiles()->create([
//                    'file_path' => $file->store('templates', 'public'),
//                    'file_type' => $fileType,
//                    'side' => $side,
//                ]);
//            }
//
//            // Save document field positions
//            $certificateTextData = json_decode($request->certificate_text_data, true);
//            foreach ($certificateTextData as $cardId => $texts) {
//                foreach ($texts as $text) {
//                    DocumentField::create([
//                        'field_key' => $text['text'],
//                        'label' => $text['text'],
//                        'position_x' => $text['left'],
//                        'position_y' => $text['top'],
//                        'width' => $text['width'] ?? null,
//                        'height' => $text['height'] ?? null,
//                        'font_family' => $text['fontFamily'],
//                        'font_size' => $text['fontSize'],
//                        'font_color' => $text['fill'],
//                        'text_align' => $text['textAlign'] ?? 'left',
//                        'font_weight' => $text['fontWeight'] ?? 'normal',
//                        'rotation' => $text['angle'],
//                        'z_index' => $text['zIndex'] ?? 1,
//                        'document_template_id' => $documentTemplate->id,
//                    ]);
//                }
//            }
//
//            // Handle attendance if enabled
//            $attendanceTemplate = null;
//            if ($request->is_attendance_enabled) {
//                $attendanceTemplate = AttendanceTemplate::create([
//                    'message' => $request->attendance_message,
//                    'send_at' => $request->attendance_send_at,
//                    'send_via' => json_encode($request->document_send_via),
//                    'event_id' => $event->id,
//                    'validity' => $request->attendance_validity,
//                ]);
//
//                if ($request->attendance_validity === 'temporary') {
//                    $attendanceTemplate->valid_from = $request->attendance_valid_from;
//                    $attendanceTemplate->valid_until = $request->attendance_valid_until;
//                    $attendanceTemplate->save();
//                }
//
//                // Store attendance template data file
//                $attendanceTemplate->excelUploads()->create([
//                    'file_path' => $request->attendance_template_data_file_path->store('uploads'),
//                ]);
//
//                // Process attendance template files
//                $attendanceTemplateFiles = $request->file('attendance_template_file_path', []);
//                $attendanceTemplateSides = $request->input('attendance_template_sides', []);
//                foreach ($attendanceTemplateFiles as $index => $file) {
//                    $side = $attendanceTemplateSides[$index] ?? 'front';
//                    $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
//                    $attendanceTemplate->templateFiles()->create([
//                        'file_path' => $file->store('templates', 'public'),
//                        'file_type' => $fileType,
//                        'side' => $side,
//                    ]);
//                }
//
//                // Save attendance field positions
//                $attendanceTextData = json_decode($request->attendance_text_data, true);
//                foreach ($attendanceTextData as $cardId => $texts) {
//                    foreach ($texts as $text) {
//                        AttendanceDocumentField::create([
//                            'field_key' => $text['text'],
//                            'label' => $text['text'],
//                            'position_x' => $text['left'],
//                            'position_y' => $text['top'],
//                            'width' => $text['width'] ?? null,
//                            'height' => $text['height'] ?? null,
//                            'font_family' => $text['fontFamily'],
//                            'font_size' => $text['fontSize'],
//                            'font_color' => $text['fill'],
//                            'text_align' => $text['textAlign'] ?? 'left',
//                            'font_weight' => $text['fontWeight'] ?? 'normal',
//                            'rotation' => $text['angle'],
//                            'z_index' => $text['zIndex'] ?? 1,
//                            'attendance_template_id' => $attendanceTemplate->id,
//                        ]);
//                    }
//                }
//            }
//
//            // Process recipients and create users
//            $importRecipients = new RecipientsImport();
//            Excel::import($importRecipients, $recipientFile);
//            $recipientsData = collect($importRecipients->rows)->unique('email')->values();
//
//            $existingEmails = User::whereIn('email', $recipientsData->pluck('email'))->pluck('email')->toArray();
//            $newUsers = [];
//            $now = now();
//
//            foreach ($recipientsData as $row) {
//                if (!in_array($row['email'], $existingEmails)) {
//                    $newUsers[] = [
//                        'name' => $row['name'],
//                        'email' => $row['email'],
//                        'password' => bcrypt('password123'),
//                        'phone' => $row['phone_number'] ?? null,
//                        'created_at' => $now,
//                        'updated_at' => $now,
//                    ];
//                }
//            }
//
//            if (!empty($newUsers)) {
//                try {
//                    User::insert($newUsers);
//                } catch (\Exception $e) {
//                    Log::error('Failed to insert users: ' . $e->getMessage(), ['newUsers' => $newUsers]);
//                    throw $e;
//                }
//
//                $roleId = Role::where('name', 'user')->value('id');
//                $newUserIds = User::whereIn('email', collect($newUsers)->pluck('email'))->pluck('id');
//                $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
//                    return [
//                        'role_id' => $roleId,
//                        'model_type' => User::class,
//                        'model_id' => $userId,
//                    ];
//                })->toArray();
//                DB::table('model_has_roles')->insert($roleAssignments);
//            }
//
//            $recipients = [];
//            foreach ($recipientsData as $row) {
//                $user = User::where('email', $row['email'])->first();
//                $recipient = Recipient::firstOrCreate([
//                    'event_id' => $event->id,
//                    'user_id' => $user->id,
//                ]);
//                $recipients[] = $recipient;
//            }
//
//            // Load template data
//            $documentDataImport = new DocumentDataImport();
//            Excel::import($documentDataImport, $request->template_data_file_path);
//            $documentRows = $documentDataImport->rows;
//
//            $attendanceDataRows = [];
//            if ($request->is_attendance_enabled) {
//                $attendanceDataImport = new DocumentDataImport();
//                Excel::import($attendanceDataImport, $request->attendance_template_data_file_path);
//                $attendanceDataRows = $attendanceDataImport->rows;
//            }
//
//            // Generate documents
//            $frontDocumentTemplate = $documentTemplate->templateFiles()->where('side', 'front')->first();
//            $backDocumentTemplate = $documentTemplate->templateFiles()->where('side', 'back')->first();
//
//            if (!$frontDocumentTemplate) {
//                throw new \Exception('Front document template not found');
//            }
//
//            $documentBackgroundPathFront = storage_path('app/public/' . $frontDocumentTemplate->file_path);
//            $resizedBackgroundPathFront = storage_path('app/public/templates/resized_' . basename($frontDocumentTemplate->file_path));
//            Image::make($documentBackgroundPathFront)->fit(900, 600)->save($resizedBackgroundPathFront);
//
//            $documentBackgroundPathBack = null;
//            $resizedBackgroundPathBack = null;
//            if ($backDocumentTemplate) {
//                $documentBackgroundPathBack = storage_path('app/public/' . $backDocumentTemplate->file_path);
//                $resizedBackgroundPathBack = storage_path('app/public/templates/resized_' . basename($backDocumentTemplate->file_path));
//                Image::make($documentBackgroundPathBack)->fit(900, 600)->save($resizedBackgroundPathBack);
//            }
//
//            $canvasWidth = 900;
//            $canvasHeight = 600;
//            $totalHeight = $backDocumentTemplate ? $canvasHeight * 2 : $canvasHeight; // 1200 لو فيه وجهين، 600 لو وجه واحد
//
//            foreach ($recipients as $index => $recipient) {
//                $dataRow = $documentRows[$index] ?? [];
//                $fields = DocumentField::where('document_template_id', $documentTemplate->id)->get();
//
//                $renderedFields = [];
//                foreach ($fields as $field) {
//                    $value = $dataRow[$field->field_key] ?? $field->field_key;
//                    $renderedFields[] = [
//                        'text' => $value,
//                        'x' => $field->position_x,
//                        'y' => $field->position_y,
//                        'font' => $field->font_family,
//                        'size' => $field->font_size,
//                        'color' => $field->font_color,
//                        'align' => $field->text_align,
//                        'weight' => $field->font_weight,
//                        'rotation' => $field->rotation,
//                    ];
//                }
//
//                $pdf = Pdf::loadView('pdf.template', [
//                    'backgroundFront' => $resizedBackgroundPathFront,
//                    'backgroundBack' => $resizedBackgroundPathBack,
//                    'fields' => $renderedFields,
//                    'canvasWidth' => $canvasWidth,
//                    'canvasHeight' => $canvasHeight,
//                    'hasBack' => $backDocumentTemplate ? true : false,
//                ])->setPaper([0, 0, $canvasWidth, $totalHeight], 'px');
//
//                // للمعاينة
//                return $pdf->stream('certificate-preview.pdf');
//
//                // حفظ الشهادة
//                $pdfPath = "certificates/{$user->id}/" . uniqid() . ".pdf";
//                $pdfFullPath = storage_path("app/public/{$pdfPath}");
//                Storage::disk('public')->makeDirectory("certificates/{$user->id}");
//                $pdf->save($pdfFullPath);
//
//                $uuid = Str::uuid();
//                $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
//                $qrPath = "qrcodes/{$uuid}.png";
//                Storage::disk('public')->makeDirectory('qrcodes');
//                Storage::disk('public')->put($qrPath, $qrCode);
//
//                $document = Document::create([
//                    'file_path' => $pdfPath,
//                    'uuid' => $uuid,
//                    'unique_code' => Str::random(10),
//                    'qr_code_path' => $qrPath,
//                    'status' => 'pending',
//                    'document_template_id' => $documentTemplate->id,
//                    'recipient_id' => $recipient->id,
//                    'valid_from' => $documentTemplate->valid_from,
//                    'valid_until' => $documentTemplate->valid_until,
//                ]);
//
//                SendCertificateJob::dispatch($document)->delay($documentTemplate->send_at);
//            }
//
//            // Generate attendance documents if enabled
//            if ($request->is_attendance_enabled) {
//                $frontAttendanceTemplate = $attendanceTemplate->templateFiles()->where('side', 'front')->first();
//                $backAttendanceTemplate = $attendanceTemplate->templateFiles()->where('side', 'back')->first();
//
//                if (!$frontAttendanceTemplate) {
//                    throw new \Exception('Front attendance template not found');
//                }
//
//                $attendanceBackgroundPathFront = storage_path('app/public/' . $frontAttendanceTemplate->file_path);
//                $resizedAttendanceBackgroundPathFront = storage_path('app/public/templates/resized_' . basename($frontAttendanceTemplate->file_path));
//                Image::make($attendanceBackgroundPathFront)->fit(900, 600)->save($resizedAttendanceBackgroundPathFront);
//
//                $attendanceBackgroundPathBack = null;
//                $resizedAttendanceBackgroundPathBack = null;
//                if ($backAttendanceTemplate) {
//                    $attendanceBackgroundPathBack = storage_path('app/public/' . $backAttendanceTemplate->file_path);
//                    $resizedAttendanceBackgroundPathBack  = storage_path('app/public/templates/resized_' . basename($backAttendanceTemplate->file_path));
//                    Image::make($attendanceBackgroundPathBack)->fit(900, 600)->save($resizedAttendanceBackgroundPathBack);
//                }
//
//                $totalHeight = $backAttendanceTemplate ? $canvasHeight * 2 : $canvasHeight;
//
//                foreach ($recipients as $index => $recipient) {
//                    $dataRow = $attendanceDataRows[$index] ?? [];
//                    $fields = AttendanceDocumentField::where('attendance_template_id', $attendanceTemplate->id)->get();
//                    $renderedFields = [];
//                    foreach ($fields as $field) {
//                        $value = $dataRow[$field->field_key] ?? '';
//                        $renderedFields[] = [
//                            'text' => $value,
//                            'x' => $field->position_x,
//                            'y' => $field->position_y,
//                            'font' => $field->font_family,
//                            'size' => $field->font_size,
//                            'color' => $field->font_color,
//                            'align' => $field->text_align,
//                            'weight' => $field->font_weight,
//                            'rotation' => $field->rotation,
//                        ];
//                    }
//
//                    $pdfPath = "attendance_certificates/{$user->id}/" . uniqid() . ".pdf";
//                    $pdfFullPath = storage_path("app/public/{$pdfPath}");
//                    Storage::disk('public')->makeDirectory("attendance_certificates/{$user->id}");
//
//                    $pdf = Pdf::loadView('pdf.template', [
//                        'backgroundFront' => $resizedAttendanceBackgroundPathFront,
//                        'backgroundBack' => $resizedAttendanceBackgroundPathBack,
//                        'fields' => $renderedFields,
//                        'canvasWidth' => $canvasWidth,
//                        'canvasHeight' => $canvasHeight,
//                        'hasBack' => $backAttendanceTemplate ? true : false,
//                    ])->setPaper([0, 0, $canvasWidth, $totalHeight], 'px');
//
//                    $pdf->save($pdfFullPath);
//
//                    $uuid = Str::uuid();
//                    $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
//                    $qrPath = "qrcodes/{$uuid}.png";
//                    Storage::disk('public')->makeDirectory('qrcodes');
//                    Storage::disk('public')->put($qrPath, $qrCode);
//
//                    $attendanceDocument = AttendanceDocument::create([
//                        'file_path' => $pdfPath,
//                        'uuid' => $uuid,
//                        'unique_code' => Str::random(10),
//                        'qr_code_path' => $qrPath,
//                        'status' => 'pending',
//                        'attendance_template_id' => $attendanceTemplate->id,
//                        'recipient_id' => $recipient->id,
//                        'valid_from' => $attendanceTemplate->valid_from,
//                        'valid_until' => $attendanceTemplate->valid_until,
//                    ]);
//
//                    SendCertificateJob::dispatch($attendanceDocument)->delay($attendanceTemplate->send_at);
//                }
//            }
//
//            // Deduct balance
//            $subscription->decrement('remaining', $recipientRows);
//
//            DB::commit();
//            return back()->with('success', 'تم إنشاء الحدث وتجهيز الشهادات بنجاح.');
//        } catch (\Exception $e) {
//            DB::rollBack();
//            Log::error('Error generating documents: ' . $e->getMessage());
//            return back()->with('error', 'حدث خطأ أثناء إنشاء الحدث: ' . $e->getMessage());
//        }
//    }


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
            $this->recipientService->createRecipients($request->file('recipient_file_path'), $event->id);


            $recipients = Recipient::where('event_id', $event->id)->get()->toArray();

            // جلب ابعاد الصورة الخاصة ب الشهادات من الريكوست
            $certificateData = json_decode($request->input('certificate_text_data'), true);
            $canvasWidth = $certificateData[array_key_first($certificateData)]['canvasWidth'] ?? 900;
            $canvasHeight = $certificateData[array_key_first($certificateData)]['canvasHeight'] ?? 600;

            $this->documentGenerationService->generateDocuments(
                $documentTemplate, $recipients,
                $request->file('template_data_file_path'),
                $canvasWidth, $canvasHeight
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
