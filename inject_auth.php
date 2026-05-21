<?php

$controllers = [
    'CategoryController' => ['model' => 'App\Models\Category', 'var' => '$category'],
    'CurrencyController' => ['model' => 'App\Models\Currency', 'var' => '$currency'],
    'CustomerController' => ['model' => 'App\Models\Customer', 'var' => '$customer'],
    'MenuItemController' => ['model' => 'App\Models\MenuItem', 'var' => '$menuItem'],
    'OrderController'    => ['model' => 'App\Models\Order', 'var' => '$order'],
    'PaymentController'  => ['model' => 'App\Models\Payment', 'var' => '$payment'],
    'RoleController'     => ['model' => 'App\Models\Role', 'var' => '$role'],
    'SettingController'  => ['model' => 'App\Models\Setting', 'var' => '$setting'],
    'TableController'    => ['model' => 'App\Models\Table', 'var' => '$table'],
    'TranslationController' => ['model' => 'App\Models\Translation', 'var' => '$translation'],
];

foreach ($controllers as $name => $meta) {
    $path = "d:/Laravel-Tutorial/ROS/app/Http/Controllers/admin/{$name}.php";
    if (!file_exists($path)) continue;

    $content = file_get_contents($path);
    $modelClass = basename(str_replace('\\', '/', $meta['model'])) . '::class';
    $var = $meta['var'];

    // Inject index
    $content = preg_replace('/(public function index\([^)]*\)\s*\{)(?!\s*\$this->authorize)/', "$1\n        \$this->authorize('viewAny', {$modelClass});", $content);
    // Inject create
    $content = preg_replace('/(public function create\([^)]*\)\s*\{)(?!\s*\$this->authorize)/', "$1\n        \$this->authorize('create', {$modelClass});", $content);
    // Inject store
    $content = preg_replace('/(public function store\([^)]*\)\s*\{)(?!\s*\$this->authorize)/', "$1\n        \$this->authorize('create', {$modelClass});", $content);
    // Inject show
    $content = preg_replace('/(public function show\([^)]*\)\s*\{)(?!\s*\$this->authorize)/', "$1\n        \$this->authorize('view', {$var});", $content);
    // Inject edit
    $content = preg_replace('/(public function edit\([^)]*\)\s*\{)(?!\s*\$this->authorize)/', "$1\n        \$this->authorize('update', {$var});", $content);
    // Inject update
    $content = preg_replace('/(public function update\([^)]*\)\s*\{)(?!\s*\$this->authorize)/', "$1\n        \$this->authorize('update', {$var});", $content);
    // Inject destroy
    $content = preg_replace('/(public function destroy\([^)]*\)\s*\{)(?!\s*\$this->authorize)/', "$1\n        \$this->authorize('delete', {$var});", $content);

    file_put_contents($path, $content);
}

// Now the controllers without specific model classes (Gate::authorize)
$gates = [
    'BackupController' => ['manage-backups'],
    'ReportController' => ['view-reports'],
    'POSSearchController' => ['create-orders'],
    'KitchenController' => ['view-orders'],
];

foreach ($gates as $name => $perm) {
    $path = "d:/Laravel-Tutorial/ROS/app/Http/Controllers/admin/{$name}.php";
    if (!file_exists($path)) continue;
    $content = file_get_contents($path);

    // Make sure Gate is imported
    if (strpos($content, 'use Illuminate\Support\Facades\Gate;') === false) {
        $content = str_replace("use App\Http\Controllers\Controller;", "use App\Http\Controllers\Controller;\nuse Illuminate\Support\Facades\Gate;", $content);
    }

    $methods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'download', 'search'];
    foreach ($methods as $method) {
        $content = preg_replace('/(public function ' . $method . '\([^)]*\)\s*\{)(?!\s*Gate::authorize)/', "$1\n        Gate::authorize('{$perm[0]}');", $content);
    }
    file_put_contents($path, $content);
}

echo 'Controllers updated!';
