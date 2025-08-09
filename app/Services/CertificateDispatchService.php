<?php

namespace App\Services;

class CertificateDispatchService
{
    public function dispatch($certificate, $sendAt): void
    {
        \App\Jobs\SendCertificateJob::dispatch($certificate)->delay($sendAt);
    }
}
