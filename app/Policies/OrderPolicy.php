<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view-orders'); }
    public function view(User $user, Order $model): bool { return $user->hasPermissionTo('view-orders'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create-orders'); }
    public function update(User $user, Order $model): bool { return $user->hasPermissionTo('edit-orders'); }
    public function delete(User $user, Order $model): bool { return $user->hasPermissionTo('delete-orders'); }
}
