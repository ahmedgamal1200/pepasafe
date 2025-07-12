<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Imports\DocumentDataImport;
use App\Imports\RecipientsImport;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Event;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;


class DocumentGenerationController extends Controller
{

    public function store(Request $request)
    {
        return redirect()->route('pry');

        set_time_limit(300);
        $user = auth()->user();
        $subscription = $user->subscription;

        // 1. تحقق من وجود رصيد
        if ($subscription->remaining <= 0) {
            return back()->with('error', 'رصيدك الحالي لا يسمح لأجراء اي وثيقة. برجاء شحن المحفظة ثم اعد المحاولة');
        }

        // 2. استيراد بيانات المشاركين من ملف Excel
        $importRecipients = new RecipientsImport();
        Excel::import($importRecipients, $request->recipient_file_path);
        $recipientsData = $importRecipients->rows;
        $neededRows = count($recipientsData);

        // 3. التحقق من أن الرصيد يكفي
        if ($subscription->remaining < $neededRows) {
            return back()->with('error', "عدد الشهادات المطلوبة ($neededRows) أكبر من رصيدك الحالي ({$subscription->remaining}).");
        }

        // 4. إنشاء الحدث
        $event = Event::create([
            'title'      => $request->event_title,
            'issuer'     => $request->issuer,
            'start_date' => $request->event_start_date,
            'end_date'   => $request->event_end_date,
            'user_id'    => $user->id,
        ]);

        // 5. إنشاء التيمبلت المرتبط بالحدث
        $template = DocumentTemplate::create([
            'title'                 => $request->document_title,
            'message'               => $request->document_message,
            'send_at'               => $request->document_send_at,
            'send_via'              => $request->document_send_via,
            'is_attendance_enabled' => $request->is_attendance_enabled ?? false,
            'event_id'              => $event->id,
            'is_temporary'          => $request->document_validity === 'temporary',
        ]);

        // 6. حفظ ملفات Excel في قاعدة البيانات
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

// 1. جهّز الإيميلات وفلتر الموجودين
        $emails = collect($recipientsData)->pluck('email')->toArray();
        $existingEmails = User::whereIn('email', $emails)->pluck('email')->toArray();

        $newUsers = [];
        $now = now();

        foreach ($recipientsData as $row) {
            if (!in_array($row['email'], $existingEmails)) {
                $newUsers[] = [
                    'name'       => $row['name'],
                    'email'      => $row['email'],
                    'password'   => bcrypt('password123'), // مهم تشفر الباسورد
                    'phone'      => $row['phone_number'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

// 2. إدخال المستخدمين دفعة واحدة
        User::insert($newUsers);

// 3. جلب الـ IDs بتوع المستخدمين الجدد
        $newUserEmails = collect($newUsers)->pluck('email')->toArray();
        $newUserIds = User::whereIn('email', $newUserEmails)->pluck('id');

// 4. جلب الـ role_id الخاص بـ "user"
        $roleId = Role::where('name', 'user')->value('id');

// 5. تجهيز بيانات الربط بجدول model_has_roles
        $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
            return [
                'role_id'    => $roleId,
                'model_type' => User::class,
                'model_id'   => $userId,
            ];
        })->toArray();

// 6. إدخال الـ roles دفعة واحدة
        DB::table('model_has_roles')->insert($roleAssignments);

        // 8. ربط كل مستخدم بمشارك في الحدث
        $recipients = [];
        foreach ($recipientsData as $row) {
            $participant = User::where('email', $row['email'])->first();
            $recipients[] = Recipient::firstOrCreate([
                'event_id' => $event->id,
                'user_id'  => $participant->id,
            ]);
        }

        // 9. خصم عدد الصفوف من رصيد الباقة
        $subscription->decrement('remaining', $neededRows);

        // 10. استيراد داتا التيمبلت من الملف
        $documentRows = [];
        if ($request->hasFile('template_data_file_path')) {
            $importData = new DocumentDataImport();
            Excel::import($importData, $request->template_data_file_path);
            $documentRows = $importData->rows;
        }

        // 11. حفظ ملفات التيمبلت (front & back)
        $templateFiles = $request->file('document_template_file_path', []);
        $templateSides = $request->input('template_sides', []);
        $frontPath = null;
        $backPath  = null;

        foreach ($templateFiles as $index => $file) {
            $side = $templateSides[$index] ?? null;
            $path = $file->store('templates', 'public');
            if ($side === 'front') $frontPath = $path;
            if ($side === 'back')  $backPath  = $path;
        }

        if (!$frontPath) {
            return back()->with('error', 'يجب رفع تيمبلت الوجه الأمامي للشهادة.');
        }

        // 12. توليد الشهادات وربطها بالمشاركين
        foreach ($recipients as $index => $recipient) {
            $dataRow = $documentRows[$index] ?? [];

            $fields = [];
            foreach ($dataRow as $key => $value) {
                $fields[] = [
                    'text'  => $value,
                    'x'     => 100, // Placeholder position
                    'y'     => 200,
                    'font'  => 'Arial',
                    'size'  => 16,
                    'color' => '#000000',
                ];
            }

            $pdfPath = "certificates/{$user->id}/" . uniqid() . ".pdf";
            $pdfFullPath = storage_path("app/public/{$pdfPath}");

            $pdf = Pdf::loadView('pdf.template', [
                'background' => storage_path("app/public/{$frontPath}"),
                'fields'     => $fields,
            ]);

            $pdf->save($pdfFullPath);

            // حفظ الشهادة
            Certificate::create([
                'user_id'       => $recipient->user_id,
                'recipient_id'  => $recipient->id,
                'template_id'   => $template->id,
                'file_path'     => $pdfPath,
                'sent_at'       => $template->send_at,
                'send_via'      => $template->send_via,
                'is_temporary'  => $template->is_temporary,
            ]);
        }

        return back()->with('success', 'تم تجهيز الشهادات وإرفاقها بالمشاركين بنجاح.');
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





}
