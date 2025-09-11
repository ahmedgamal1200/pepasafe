<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserQrCodeService
{
    public function generateQrCodeForUser(User $user): string
    {
        // مسار اللوجو داخل مجلد storage/app/public/logos
        $logoPath = public_path('assets/logo.jpg');

        $qrImage = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->color(52, 120, 246) // إضافة هذا السطر لتغيير اللون للأزرق
            ->merge($logoPath, 0.2, true)
            ->generate(route('showProfileToGuest', $user->slug));

        $fileName = 'qr_codes/user_'.$user->id.'.png';
        Storage::disk('public')->put($fileName, $qrImage);

        $user->update(['qr_code' => $fileName]);

        return $fileName;
    }
}
