<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Setting;

class SettingPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('manage-settings'); }
    public function view(User $user, Setting $model): bool { return $user->hasPermissionTo('manage-settings'); }
    public function create(User $user): bool { return $user->hasPermissionTo('manage-settings'); }
    public function update(User $user, Setting $model): bool { return $user->hasPermissionTo('manage-settings'); }
    public function delete(User $user, Setting $model): bool { return $user->hasPermissionTo('manage-settings'); }
}
