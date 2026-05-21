<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\admin\MenuItemController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\TableController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\PaymentController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\KitchenController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\ProfileController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\CurrencyController;
use App\Http\Controllers\admin\TranslationController;
use App\Http\Controllers\admin\SearchController;
use App\Http\Controllers\admin\BackupController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return redirect('/home');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'kh'])) {
        session()->put('locale', $locale);
        session()->save();

        $msg = $locale == 'en' ? 'Language switched to English' : 'ភាសាត្រូវបានប្តូរទៅជាភាសាខ្មែរ';
        session()->flash('success', $msg);
    }
    return redirect()->back();
})->name('lang.switch');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('qr-code', function () {
        return view('admin.qr');
    })->name('qr.code');

    // Profile Management (All Users)
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin only
    // Role middleware removed; authorization is handled by Controller Policies
    Route::resource('menu', MenuItemController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('currencies', CurrencyController::class);
    Route::resource('translations', TranslationController::class);

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    // Income Reports
    Route::get('reports/income', [ReportController::class, 'income'])->name('reports.income');
    Route::get('reports/income/pdf', [ReportController::class, 'exportPdf'])->name('reports.income.pdf');
    Route::get('reports/income/excel', [ReportController::class, 'exportExcel'])->name('reports.income.excel');

    // Dynamic Search
    Route::get('search', [SearchController::class, 'search'])->name('admin.search');

    // Backups
    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [BackupController::class, 'create'])->name('backups.create');
    Route::get('backups/download', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('backups', [BackupController::class, 'destroy'])->name('backups.destroy');

    // Kitchen KDS
    Route::get('kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('kitchen/order/{kitchenOrder}/note', [KitchenController::class, 'updateNote'])->name('kitchen.update-note');
    Route::patch('kitchen/order/{kitchenOrder}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.update-status');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // POS, Orders, Tables, etc.
    Route::get('pos/checkout', [OrderController::class, 'checkout'])->name('pos.checkout');
    Route::get('pos', [OrderController::class, 'create'])->name('pos.index');
    Route::resource('orders', OrderController::class);
    Route::resource('tables', TableController::class);
    Route::resource('payments', PaymentController::class)->only(['index', 'show']);
    Route::post('orders/{order}/pay', [PaymentController::class, 'process'])->name('orders.pay');
    Route::get('orders/{order}/receipt', [PaymentController::class, 'receipt'])->name('orders.receipt');

    // Global Search (Unified)
    Route::get('global-search', [SearchController::class, 'global'])->name('global-search');
    
    // POS Focused Search
    Route::get('pos/search', [\App\Http\Controllers\admin\POSSearchController::class, 'search'])->name('pos.search');
});
