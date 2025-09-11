<?php

namespace App\Repositories\Eventor\Auth;

use App\Http\Requests\Eventor\Auth\EventorRegisterRequest;
use App\Models\PaymentReceipt;
use App\Models\Plan;
use App\Models\User;
use App\Services\UserQrCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Twilio\Rest\Client;

class EventorAuthRepository
{
    /**
     * @throws \Exception
     */
    public function __construct(protected UserQrCodeService $qrCodeService)
    {
        //
    }

    public function registerEventor(EventorRegisterRequest $request)
    {
        DB::beginTransaction();

        try {

            $plan = Plan::query()->findOrFail($request->input('plan'));

            $user = User::query()->create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'phone' => $request->input('phone'),
                'category_id' => $request->input('category'),
                'max_users' => $plan->max_users,
            ]);

            $role = Role::query()->firstOrCreate([
                'name' => $request->input('role'),
                'guard_name' => 'web',
            ]);

            $permission = Permission::firstOrCreate([
                'name' => 'full access to events',
                'guard_name' => 'web',
            ]);

            // لو الرول لسه جديد ومعندوش بيرمشنز، اربطه بيهم
            if ($role->permissions->isEmpty()) {
                $role->syncPermissions([$permission->name]);
            }

            $user->assignRole($role);

            // ✅ توليد الـ QR Code
            $this->qrCodeService->generateQrCodeForUser($user);

            if ($plan->price > 0 && $request->hasFile("payment_receipt.{$plan->id}")) {
                $file = $request->file("payment_receipt.{$plan->id}");
                $receiptPath = $file->store('receipts', 'public');
            }

            PaymentReceipt::query()->create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'image_path' => $receiptPath ?? null,
            ]);

            //        $this->sendOtpPhone($user->phone, $otp);
            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack(); // إذا حدث أي خطأ، يتم التراجع عن المعاملة
            throw $e; // يمكن تسجيل أو التعامل مع الاستثناء كما تشاء
        }
    }

    /**
     * @throws \Exception
     */
    public function sendOtpPhone($phone, $otp): void
    {
        // استرجاع إعدادات API SMS من قاعدة البيانات
        $smsApiKey = DB::table('api_configs')->where('key', 'sms_api_key')->value('value');
        $smsSenderId = DB::table('api_configs')->where('key', 'sms_sender_id')->value('value');

        // تحقق إذا كانت الإعدادات موجودة
        if (! $smsApiKey || ! $smsSenderId) {
            throw new \Exception('SMS API key or sender ID is not configured.');
        }

        // بناء الرسالة
        $message = 'Your OTP Code is: '.$otp;

        // إرسال الرسالة عبر الـ API
        $client = new Client($smsApiKey, null);  // نحن لا نحتاج توكين مع بعض الخدمات مثل Nexmo أو Twilio عند التكوين هذا
        $client->messages->create(
            $phone, // رقم الهاتف الذي سيتم إرسال OTP إليه
            [
                'from' => $smsSenderId, // ID المرسل
                'body' => $message,
            ]
        );
    }
}
