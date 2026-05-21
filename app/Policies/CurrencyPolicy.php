<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Currency;

class CurrencyPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('manage-settings'); }
    public function view(User $user, Currency $model): bool { return $user->hasPermissionTo('manage-settings'); }
    public function create(User $user): bool { return $user->hasPermissionTo('manage-settings'); }
    public function update(User $user, Currency $model): bool { return $user->hasPermissionTo('manage-settings'); }
    public function delete(User $user, Currency $model): bool { return $user->hasPermissionTo('manage-settings'); }
}
