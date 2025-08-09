<?php

namespace App\Repositories\Eventor;
use App\Imports\RecipientsImport;
use App\Models\Recipient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RecipientRepository
{
    public function createRecipients($recipientFile, int $eventId): array
    {
        $importRecipients = new RecipientsImport();
        Excel::import($importRecipients, $recipientFile);
        $recipientsData = collect($importRecipients->rows)->unique('email')->values();

        $existingEmails = User::whereIn('email', $recipientsData->pluck('email'))->pluck('email')->toArray();
        $newUsers = [];
        $now = now();

        foreach ($recipientsData as $row) {
            if (!in_array($row['email'], $existingEmails)) {
                $newUsers[] = [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => bcrypt('password123'),
                    'phone' => $row['phone_number'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($newUsers)) {
            User::insert($newUsers);
            $roleId = Role::where('name', 'user')->value('id');
            $newUserIds = User::whereIn('email', collect($newUsers)->pluck('email'))->pluck('id');
            $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
                return [
                    'role_id' => $roleId,
                    'model_type' => User::class,
                    'model_id' => $userId,
                ];
            })->toArray();
            DB::table('model_has_roles')->insert($roleAssignments);
        }

        $recipients = [];
        foreach ($recipientsData as $row) {
            $user = User::where('email', $row['email'])->first();
            $recipient = Recipient::firstOrCreate([
                'event_id' => $eventId,
                'user_id' => $user->id,
            ]);
            $recipients[] = $recipient;
        }

        return $recipients;
    }

    public function getRecipientCount($recipientFile): int
    {
        return Excel::toCollection(new RecipientsImport(), $recipientFile)
            ->first()->count();
    }
}
