<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Table;

class TablePolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view-tables'); }
    public function view(User $user, Table $model): bool { return $user->hasPermissionTo('view-tables'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create-tables'); }
    public function update(User $user, Table $model): bool { return $user->hasPermissionTo('edit-tables'); }
    public function delete(User $user, Table $model): bool { return $user->hasPermissionTo('delete-tables'); }
}
