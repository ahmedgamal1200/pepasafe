<?php

namespace App\Services;

use App\Jobs\SendCertificateJob;

class CertificateDispatchService
{
    public function dispatch($certificate, $sendAt): void
    {
        SendCertificateJob::dispatch($certificate)->delay($sendAt);
    }
}
