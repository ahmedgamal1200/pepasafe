<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RegisteredRepository
{

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
            'guard_name' => 'web'
        ]);

        $user->assignRole($role);

        return $user;
    }

}
