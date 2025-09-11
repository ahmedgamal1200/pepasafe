<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\UserQrCodeService;
use Spatie\Permission\Models\Role;

class RegisteredRepository
{
    public function __construct(protected UserQrCodeService $qrCodeService)
    {
        //
    }

    public function register(array $data)
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'category_id' => $data['category'] ?? null,
        ]);

        $role = Role::query()->firstOrCreate([
            'name' => $data['role'],
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);

        // ✅ توليد الـ QR Code
        $this->qrCodeService->generateQrCodeForUser($user);

        return $user;
    }
}
