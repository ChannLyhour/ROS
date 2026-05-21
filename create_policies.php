<?php

function createPolicy($name, $model, $pView, $pCreate, $pUpdate, $pDelete) {
    $content = "<?php\nnamespace App\Policies;\nuse App\Models\User;\nuse App\Models\\$model;\nclass $name\n{\n";
    $content .= "    public function viewAny(User $user): bool { return $user->hasPermissionTo('$pView'); }\n";
    $content .= "    public function view(User $user, $model $m): bool { return $user->hasPermissionTo('$pView'); }\n";
    $content .= "    public function create(User $user): bool { return $user->hasPermissionTo('$pCreate'); }\n";
    $content .= "    public function update(User $user, $model $m): bool { return $user->hasPermissionTo('$pUpdate'); }\n";
    $content .= "    public function delete(User $user, $model $m): bool { return $user->hasPermissionTo('$pDelete'); }\n";
    $content .= "}\n";
    file_put_contents("d:/Laravel-Tutorial/ROS/app/Policies/$name.php", $content);
}

createPolicy('CategoryPolicy', 'Category', 'view-menu', 'create-menu', 'edit-menu', 'delete-menu');
createPolicy('MenuItemPolicy', 'MenuItem', 'view-menu', 'create-menu', 'edit-menu', 'delete-menu');
createPolicy('CustomerPolicy', 'Customer', 'view-customers', 'create-customers', 'edit-customers', 'delete-customers');
createPolicy('OrderPolicy', 'Order', 'view-orders', 'create-orders', 'edit-orders', 'delete-orders');
createPolicy('TablePolicy', 'Table', 'view-tables', 'create-tables', 'edit-tables', 'delete-tables');
createPolicy('RolePolicy', 'Role', 'view-roles', 'create-roles', 'edit-roles', 'delete-roles');
createPolicy('SettingPolicy', 'Setting', 'manage-settings', 'manage-settings', 'manage-settings', 'manage-settings');
createPolicy('CurrencyPolicy', 'Currency', 'manage-settings', 'manage-settings', 'manage-settings', 'manage-settings');
createPolicy('TranslationPolicy', 'Translation', 'manage-translations', 'manage-translations', 'manage-translations', 'manage-translations');
createPolicy('PaymentPolicy', 'Payment', 'view-payments', 'refund-payments', 'refund-payments', 'refund-payments');

echo 'Policies created!';
