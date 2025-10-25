<?php

namespace App\Jobs;

use App\Imports\RecipientsImport;
use App\Models\Recipient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CreateRecipientsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $recipientFilePath, public int $eventId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle(): array
    {
        $importRecipients = new RecipientsImport;
        Excel::import($importRecipients, $this->recipientFilePath);
        $recipientsData = collect($importRecipients->rows)->unique('email')->values();

        $existingEmails = User::whereIn('email', $recipientsData->pluck('email'))->pluck('email')->toArray();
        $newUsers = [];
        $now = now();

        $lastSlug = User::withTrashed()->max('slug') ?: 999;

        foreach ($recipientsData as $row) {
            if (! in_array($row['email'], $existingEmails)) {
                $lastSlug++;

                $newUsers[] = [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => bcrypt('password123'),
                    'phone' => $row['phone'] ?? null,
                    'slug' => $lastSlug, // إضافة حقل الـ slug هنا
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                    Log::info('SMS sent to '.$row['phone']);
            }
        }

        if (! empty($newUsers)) {
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
                'event_id' => $this->eventId,
                'user_id' => $user->id,
            ]);
            $recipients[] = $recipient;
        }

        return $recipients;
    }
}
