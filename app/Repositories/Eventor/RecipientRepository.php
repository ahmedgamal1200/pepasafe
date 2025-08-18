<?php

namespace App\Repositories\Eventor;
use App\Imports\RecipientsImport;
use App\Jobs\CreateRecipientsJob;
use App\Models\Recipient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RecipientRepository
{
    public function createRecipients($recipientFile, int $eventId): void
    {
        // تخزين الملف مؤقتًا
        $filePath = $recipientFile->store('temp');

        // توزيع الجوب
        CreateRecipientsJob::dispatch(storage_path('app/' . $filePath), $eventId);
    }

    public function getRecipientCount($recipientFile): int
    {
        return Excel::toCollection(new RecipientsImport(), $recipientFile)
            ->first()->count();
    }
}
