<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Permission;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?int $roleId = null;
    protected array $permissions = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // خزن الرول والصلاحيات مؤقتًا
        $this->roleId = $data['role_id'] ?? null;
        $this->permissions = $data['permissions'] ?? [];

        // شيلهم من البيانات قبل الحفظ لأنهم مش أعمدة في الجدول
        unset($data['role_id'], $data['permissions']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record && $this->roleId) {
            $this->record->syncRoles([$this->roleId]);
        }

        if ($this->record && !empty($this->permissions)) {
            $permissionNames = Permission::whereIn('id', $this->permissions)->pluck('name')->toArray();
            $this->record->syncPermissions($permissionNames);
        }
    }
}
