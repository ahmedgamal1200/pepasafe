<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Filament\Actions;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected ?int $roleId = null;
    protected array $permissions = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // خزن الـ role_id و permissions مؤقتًا
        $this->roleId = $data['role_id'] ?? null;
        $this->permissions = $data['permissions'] ?? [];

        // شيلهم من البيانات قبل الحفظ لأنها مش أعمدة في جدول users
        unset($data['role_id'], $data['permissions']);

        return $data;
    }

    protected function afterSave(): void
    {

        if ($this->record && $this->roleId) {
            $role = Role::findById($this->roleId); // ← ID → Object
            $this->record->syncRoles([$role->name]); // ← Object → name ✅
        }

        if ($this->record && !empty($this->permissions)) {
            $permissionNames = Permission::whereIn('id', $this->permissions)->pluck('name')->toArray();
            $this->record->syncPermissions($permissionNames);
        }
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

