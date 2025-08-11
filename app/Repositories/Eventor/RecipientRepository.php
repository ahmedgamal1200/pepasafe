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
    public function createRecipients($recipientFile, int $eventId): array
    {
        // نخزن الملف في storage
//        $path = $recipientFile->store('recipients_temp');
        $path = $recipientFile->storeAs('recipients_temp', uniqid().'.'.$recipientFile->getClientOriginalExtension(), 'local');

        CreateRecipientsJob::dispatch($path, $eventId);

        return ['message' => 'Recipients import job has been dispatched.'];

    }

    public function getRecipientCount($recipientFile): int
    {
        return Excel::toCollection(new RecipientsImport(), $recipientFile)
            ->first()->count();
    }
}
