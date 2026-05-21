<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Translation;

class TranslationPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('manage-translations'); }
    public function view(User $user, Translation $model): bool { return $user->hasPermissionTo('manage-translations'); }
    public function create(User $user): bool { return $user->hasPermissionTo('manage-translations'); }
    public function update(User $user, Translation $model): bool { return $user->hasPermissionTo('manage-translations'); }
    public function delete(User $user, Translation $model): bool { return $user->hasPermissionTo('manage-translations'); }
}
