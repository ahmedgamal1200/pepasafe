<?php

namespace App\Repositories\Eventor;

use App\Imports\RecipientsImport;
use App\Jobs\CreateRecipientsJob;
use App\Models\Recipient;
use App\Models\Role;
use App\Models\User;
use App\Services\UserQrCodeService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RecipientRepository
{
    //    public function createRecipients($recipientFile, int $eventId): void
    //    {
    //        // تخزين الملف مؤقتًا
    //        $filePath = $recipientFile->store('temp');
    //
    //        // توزيع الجوب
    //        CreateRecipientsJob::dispatch(storage_path('app/' . $filePath), $eventId);
    //    }

    public function __construct(protected UserQrCodeService $qrCodeService)
    {
        //
    }

    public function createRecipients($recipientFile, int $eventId): array
    {
        $importRecipients = new RecipientsImport;
        Excel::import($importRecipients, $recipientFile);
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
                    'password' => bcrypt('123456789'),
                    'phone' => $row['phone'] ?? null,
                    'slug' => $lastSlug, // إضافة حقل الـ slug هنا
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($newUsers)) {
            User::insert($newUsers);
            $roleId = Role::where('name', 'user')->value('id');
            $newUserEmails = collect($newUsers)->pluck('email');
            $newUserIds = User::whereIn('email', $newUserEmails)->pluck('id');
            $roleAssignments = $newUserIds->map(function ($userId) use ($roleId) {
                return [
                    'role_id' => $roleId,
                    'model_type' => User::class,
                    'model_id' => $userId,
                ];
            })->toArray();
            DB::table('model_has_roles')->insert($roleAssignments);

            // 3. يتم استرجاع المستخدمين الجدد كـ objects
            $createdUsers = User::whereIn('email', $newUserEmails)->get();

            // 4. يتم عمل loop على المستخدمين الجدد لإنشاء QR code لكل واحد
            foreach ($createdUsers as $user) {
                $this->qrCodeService->generateQrCodeForUser($user);
            }
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
        return Excel::toCollection(new RecipientsImport, $recipientFile)
            ->first()->count();
    }
}
