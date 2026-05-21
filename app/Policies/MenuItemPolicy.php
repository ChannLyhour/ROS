<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MenuItem;

class MenuItemPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view-menu'); }
    public function view(User $user, MenuItem $model): bool { return $user->hasPermissionTo('view-menu'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create-menu'); }
    public function update(User $user, MenuItem $model): bool { return $user->hasPermissionTo('edit-menu'); }
    public function delete(User $user, MenuItem $model): bool { return $user->hasPermissionTo('delete-menu'); }
}
