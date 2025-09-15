<?php

namespace App\Repositories\Eventor;

use App\Models\AttendanceDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceDocumentRepository
{
    public function create(array $data, string $uuid): array
    {
//        $uuid = Str::uuid();
        $qrCode = QrCode::format('png')->size(200)->generate(route('documents.verify', $uuid));
        $qrPath = "qrcodes/{$uuid}.png";
        Storage::disk('public')->makeDirectory('qrcodes');
        Storage::disk('public')->put($qrPath, $qrCode);

        $attendanceDocument =  AttendanceDocument::create([
            'file_path' => $data['file_path'],
            'uuid' => $uuid,
            'unique_code' => Str::random(10),
            'qr_code_path' => $qrPath,
            'status' => 'pending',
            'attendance_template_id' => $data['attendance_template_id'],
            'recipient_id' => $data['recipient_id'],
            'valid_from' => $data['valid_from'],
            'valid_until' => $data['valid_until'],
        ]);

        return [
            'attendanceDocument' => $attendanceDocument,
            'qrPath' => $qrPath,
        ];
    }
}
