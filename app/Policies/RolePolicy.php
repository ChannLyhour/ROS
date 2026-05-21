<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view-roles'); }
    public function view(User $user, Role $model): bool { return $user->hasPermissionTo('view-roles'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create-roles'); }
    public function update(User $user, Role $model): bool { return $user->hasPermissionTo('edit-roles'); }
    public function delete(User $user, Role $model): bool { return $user->hasPermissionTo('delete-roles'); }
}
