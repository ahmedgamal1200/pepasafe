<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use App\Models\Document;

class DocumentVerificationController extends Controller
{
    public function verify($uuid)
    {
        //        echo 'hi';
        $document = Document::where('uuid', $uuid)->first();

        if (! $document) {
            return view('certificates.invalid'); // صفحة تقول الشهادة غير صالحة
        }

        return view('certificates.verified', [
            'document' => $document,
            'recipient' => $document->recipient,
            'template' => $document->template,
        ]);
    }
}
