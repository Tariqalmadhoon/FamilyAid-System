<?php

use App\Http\Controllers\Admin\AidProgramController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\HouseholdController;
use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\AccountSecurityController;
use App\Http\Controllers\Citizen\DashboardController;
use App\Http\Controllers\Citizen\MemberController;
use App\Http\Controllers\Citizen\OnboardingController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Language switch
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

// Public landing page
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard - redirect based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->hasRole('citizen')) {
        return redirect()->route('citizen.dashboard');
    }
    
    if ($user->hasAnyRole(['admin', 'data_entry', 'auditor', 'distributor'])) {
        return redirect()->route('admin.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Account security
Route::middleware(['auth'])->group(function () {
    Route::get('/account/security', [AccountSecurityController::class, 'edit'])
        ->name('account.security.edit');
});

// Citizen routes
Route::middleware(['auth', 'role:citizen'])->prefix('citizen')->name('citizen.')->group(function () {
    // Onboarding wizard
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Household management
    Route::get('/household/edit', [DashboardController::class, 'edit'])->name('household.edit');
    Route::put('/household', [DashboardController::class, 'update'])->name('household.update');
    
    // Members management
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
});

// Admin routes
Route::middleware(['auth', 'role:admin|data_entry|auditor|distributor'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Households
    Route::post('/households/bulk-destroy', [HouseholdController::class, 'bulkDestroy'])->name('households.bulk-destroy');
    Route::resource('households', HouseholdController::class);
    Route::post('/households/{household}/verify', [HouseholdController::class, 'verify'])->name('households.verify');
    
    // Aid Programs
    Route::resource('programs', AidProgramController::class);
    
    // Distributions
    Route::get('/distributions/search-household', [DistributionController::class, 'searchHousehold'])->name('distributions.search-household');
    Route::get('/distributions/check-eligibility', [DistributionController::class, 'checkEligibility'])->name('distributions.check-eligibility');
    Route::resource('distributions', DistributionController::class)->except(['edit', 'update']);
    
    // Import/Export
    Route::get('/import-export', [ImportExportController::class, 'index'])->name('import-export.index');
    Route::get('/import-export/template', [ImportExportController::class, 'downloadTemplate'])->name('import-export.template');
    Route::post('/import-export/import', [ImportExportController::class, 'import'])->name('import-export.import');
    Route::get('/import-export/export-households', [ImportExportController::class, 'exportHouseholds'])->name('import-export.export-households');
    Route::get('/import-export/export-distributions', [ImportExportController::class, 'exportDistributions'])->name('import-export.export-distributions');
    
    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});

require __DIR__.'/auth.php';
