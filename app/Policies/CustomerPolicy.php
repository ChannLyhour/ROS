<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view-customers'); }
    public function view(User $user, Customer $model): bool { return $user->hasPermissionTo('view-customers'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create-customers'); }
    public function update(User $user, Customer $model): bool { return $user->hasPermissionTo('edit-customers'); }
    public function delete(User $user, Customer $model): bool { return $user->hasPermissionTo('delete-customers'); }
}
