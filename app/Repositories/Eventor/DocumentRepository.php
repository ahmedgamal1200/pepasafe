<?php

namespace App\Repositories\Eventor;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentRepository
{
    public function create(array $data, string $uuid): array
    {
        // الـ uuid سيتم تمريره بالفعل، فلا حاجة لإنشائه هنا مرة أخرى
        // $uuid = Str::uuid();

        // تأكد من أن الـ route() تستخدم الـ uuid الذي تم تمريره
        $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
        $qrPath = "qrcodes/{$uuid}.png"; // استخدم الـ uuid هنا أيضًا
        Storage::disk('public')->makeDirectory('qrcodes');
        Storage::disk('public')->put($qrPath, $qrCode);

        $document = Document::query()->create([
            'file_path' => $data['file_path'],
            'uuid' => $uuid, // استخدم الـ uuid الذي تم تمريره
            'unique_code' => Str::random(10),
            'qr_code_path' => $qrPath,
            'status' => 'pending',
            'document_template_id' => $data['document_template_id'],
            'recipient_id' => $data['recipient_id'],
            'valid_from' => $data['valid_from'],
            'valid_until' => $data['valid_until'],
        ]);

        return [
            'document' => $document,
            'qrPath' => $qrPath,
        ];
    }
}
