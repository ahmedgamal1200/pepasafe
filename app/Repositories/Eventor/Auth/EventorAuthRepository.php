<?php

namespace App\Repositories\Eventor\Auth;

use App\Http\Requests\Eventor\Auth\EventorRegisterRequest;
use App\Models\PaymentReceipt;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EventorAuthRepository
{
    public function registerEventor(EventorRegisterRequest $request)
    {
        $plan = Plan::query()->findOrFail($request->input('plan'));

        $user = User::query()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'category_id' => $request->input('category'),
            'max_users' => $plan->max_users
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


        if ($plan->price > 0 && $request->hasFile("payment_receipt.{$plan->id}")) {
            $file = $request->file("payment_receipt.{$plan->id}");
            $receiptPath = $file->store('receipts', 'public');
        }

        PaymentReceipt::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'image_path' => $receiptPath ?? null,
        ]);

        return $user;
    }
}
