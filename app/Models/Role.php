<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // ده الحل السحري 👇
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }
}
