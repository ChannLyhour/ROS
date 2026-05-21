<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;

class PaymentPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view-payments'); }
    public function view(User $user, Payment $model): bool { return $user->hasPermissionTo('view-payments'); }
    public function create(User $user): bool { return $user->hasPermissionTo('refund-payments'); }
    public function update(User $user, Payment $model): bool { return $user->hasPermissionTo('refund-payments'); }
    public function delete(User $user, Payment $model): bool { return $user->hasPermissionTo('refund-payments'); }
}
