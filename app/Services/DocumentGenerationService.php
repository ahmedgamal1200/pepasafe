<?php

namespace App\Services;

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
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentGenerationService
{
    public function execute(array $data): void
    {
        DB::transaction(function () use ($data) {
            $event = $this->createEvent($data);
            $template = $this->createTemplate($event, $data);

            $this->storeExcelUploads($event, $data);

            $recipients = $this->importRecipients($event, $data['recipient_file_path']);
            $documentRows = $this->readDocumentRows($data['template_data_file_path'] ?? null);

            foreach ($recipients as $index => $recipient) {
                $rowData = $documentRows[$index] ?? [];

                $document = $this->createDocument($template, $recipient, $rowData);

                foreach ((array) $template->send_via as $channel) {
                    SendDocumentJob::dispatch($document, $channel);
                }
            }
        });
    }

    private function createEvent(array $data)
    {
        return Event::query()->create([
            'title' => $data['event_title'],
            'issuer' => $data['issuer'],
            'start_date' => $data['event_start_date'],
            'end_date' => $data['event_end_date'],
            'user_id' => $data['user_id'],
        ]);
    }

    private function createTemplate(Event $event, array $data)
    {
        return DocumentTemplate::query()->create([
            'title' => $data['document_title'],
            'message' => $data['document_message'],
            'send_at' => $data['document_send_at'],
            'send_via' => $data['document_send_via'],
            'is_attendance_enabled' => $data['is_attendance_enabled'] ?? false,
            'event_id' => $event->id,
            ]);
    }

    private function storeExcelUploads(Event $event, array $data): void
    {
        $event->excelUploads()->create([
            'file_path' => $data['recipient_file_path']->store('uploads'),
            'upload_type' => 'recipients',
        ]);

        if (isset($data['template_data_file_path'])) {
            $event->excelUploads()->create([
                'file_path' => $data['template_data_file_path']->store('uploads'),
                'upload_type' => 'document_data',
            ]);
        }
    }

    private function importRecipients(Event $event, $file): array
    {
        $import = new RecipientsImport();

        Excel::import($import, $file);

        $rows = $import->rows;

        $recipients = [];

        foreach ($rows as $row) {
            $user = User::query()->firstOrCreate(
                ['email' => $row['email']],
                [
                    'name'     => $row['name'],
                    'password' => bcrypt('password123'),
                    'phone'    => $row['phone'] ?? null,
                ]
            );

            $recipient = Recipient::query()->firstOrCreate([
                'event_id' => $event->id,
                'user_id'  => $user->id,
            ]);

            $recipients[] = $recipient;
        }

        return $recipients;
    }

    private function readDocumentRows($file): array
    {
        if (!$file) return [];

        $import = new DocumentDataImport();
        Excel::import($import, $file);

        return $import->rows;
    }

    private function createDocument(DocumentTemplate $template, Recipient $recipient, array $row): Document
    {
        $uuid = Str::uuid()->toString();
        $code = strtoupper(Str::random(10));
        $qrPath = "qr/{$code}.png";
        $docPath = "documents/{$code}.pdf";

        // توليد ال QR Code
        QrCode::format('png')->size(300)
            ->generate(route('documents.verify', $uuid), storage_path("app/public/{$qrPath}"));


        // إعداد البيانات لدمجها داخل التصميم
        $fields = $template->fields;
        $renderedFields = [];

        foreach ($fields as $field) {
            $text = $row[$field->field_key] ?? '';

            $renderedFields[] = [
                'text' => $text,
                'x' => $field->position_x,
                'y' => $field->position_y,
                'font' => $field->font_family,
                'size' => $field->font_size,
                'color' => $field->font_color,
            ];
        }

        // تحميل ملف التيمبلت (صورة أو PDF)
        $templateFile = $template->templateFiles()->where('side', 'front')->first();
        $backgroundPath = storage_path('app/public/' . $templateFile->file_path);

        // توليد PDF من التيمبلت
        $pdf = Pdf::loadView('pdf.template', [
            'background' => $backgroundPath,
            'fields' => $renderedFields,
        ]);

        Storage::disk('public')->put($docPath, $pdf->output());

        // إنشاء سجل الشهادة
        return $recipient->documents()->create([
            'file_path'            => $docPath,
            'uuid'                 => $uuid,
            'unique_code'          => $code,
            'qr_code_path'         => $qrPath,
            'status'               => 'pending',
            'document_template_id' => $template->id,
        ]);

    }


}
