<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view-menu'); }
    public function view(User $user, Category $model): bool { return $user->hasPermissionTo('view-menu'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create-menu'); }
    public function update(User $user, Category $model): bool { return $user->hasPermissionTo('edit-menu'); }
    public function delete(User $user, Category $model): bool { return $user->hasPermissionTo('delete-menu'); }
}
