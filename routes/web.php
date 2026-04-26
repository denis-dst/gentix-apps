<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Models\Event;
use App\Models\Setting;
use App\Http\Controllers\PublicEventController;

Route::get('/', function () {
    $events = Event::where('status', 'published')
        ->orderBy('event_start_date', 'asc')
        ->take(6)
        ->get();
    
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

    return view('welcome', compact('events', 'settings'));
});

Route::get('/event/{slug}', [PublicEventController::class, 'show'])->name('events.show');
Route::get('/promo/validate', [PublicEventController::class, 'validatePromo'])->name('promo.validate');
Route::post('/event/{slug}/checkout', [PublicEventController::class, 'checkout'])->name('checkout.process');
Route::post('/midtrans/notification', [PublicEventController::class, 'handleNotification'])->name('midtrans.notification');
Route::get('/checkout/success/{reference}', [App\Http\Controllers\PublicEventController::class, 'success'])->name('checkout.success');

Route::get('/tickets/view/{code}', [App\Http\Controllers\TicketViewController::class, 'show'])->name('tickets.view');


Route::middleware(['auth', 'role:superadmin|Superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    
    // Tenants Trash & Resource
    Route::get('tenants/trash', [App\Http\Controllers\SuperAdmin\TenantController::class, 'trash'])->name('tenants.trash');
    Route::post('tenants/{id}/restore', [App\Http\Controllers\SuperAdmin\TenantController::class, 'restore'])->name('tenants.restore');
    Route::delete('tenants/{id}/force-delete', [App\Http\Controllers\SuperAdmin\TenantController::class, 'forceDelete'])->name('tenants.force-delete');
    Route::resource('tenants', App\Http\Controllers\SuperAdmin\TenantController::class);
    
    // Events Trash & Resource
    Route::get('events/trash', [App\Http\Controllers\SuperAdmin\EventController::class, 'trash'])->name('events.trash');
    Route::post('events/{id}/restore', [App\Http\Controllers\SuperAdmin\EventController::class, 'restore'])->name('events.restore');
    Route::delete('events/{id}/force-delete', [App\Http\Controllers\SuperAdmin\EventController::class, 'forceDelete'])->name('events.force-delete');
    Route::resource('events', App\Http\Controllers\SuperAdmin\EventController::class);
    
    Route::resource('transactions', App\Http\Controllers\SuperAdmin\TransactionController::class);
    Route::get('settings', [App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.update');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole(['superadmin', 'Superadmin'])) {
        return redirect()->route('superadmin.dashboard');
    }
    if ($user->hasRole(['organizer', 'Penyedia Event'])) {
        return redirect()->route('organizer.dashboard');
    }
    if ($user->hasRole(['Petugas Loket', 'loket', 'Petugas Gate', 'gate'])) {
        return redirect()->route('organizer.redeem.index');
    }
    // Fallback for other roles or unassigned
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:organizer|Penyedia Event|superadmin|Superadmin|Petugas Loket|Petugas Gate|loket|gate', 'tenant.status'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Organizer\DashboardController::class, 'index'])->name('dashboard');
    
    // Event Management
    Route::resource('events', App\Http\Controllers\Organizer\EventController::class);
    Route::resource('events.categories', App\Http\Controllers\Organizer\TicketCategoryController::class);
    
    // Voucher/Promo Management
    Route::resource('vouchers', App\Http\Controllers\Organizer\PromoCodeController::class);
    
    // Reports & Operations
    Route::get('reports', [App\Http\Controllers\Organizer\ReportController::class, 'index'])->name('reports.index');
    Route::get('checkin', [App\Http\Controllers\Organizer\CheckinController::class, 'index'])->name('checkin.index');
    Route::post('checkin/{id}/redeem', [App\Http\Controllers\Organizer\CheckinController::class, 'redeem'])->name('checkin.redeem');
    
    // Finance
    Route::get('finance', [App\Http\Controllers\Organizer\FinanceController::class, 'index'])->name('finance.index');
    
    // Crew Management
    Route::resource('crews', App\Http\Controllers\Organizer\CrewController::class);

    // Redeem System
    Route::get('redeem', [App\Http\Controllers\Organizer\RedeemController::class, 'index'])->name('redeem.index');
    Route::get('redeem/{event}/verify', [App\Http\Controllers\Organizer\RedeemController::class, 'verifyForm'])->name('redeem.verify');
    Route::post('redeem/{event}/verify', [App\Http\Controllers\Organizer\RedeemController::class, 'verify'])->name('redeem.verify.post');
    Route::get('redeem/{event}/scan', [App\Http\Controllers\Organizer\RedeemController::class, 'scan'])->name('redeem.scan');
    Route::get('redeem/{event}/download', [App\Http\Controllers\Organizer\RedeemController::class, 'downloadData'])->name('redeem.download');
    Route::post('redeem/process', [App\Http\Controllers\Organizer\RedeemController::class, 'process'])->name('redeem.process');

    // Tenant Settings (T&C)
    Route::get('settings/terms', [App\Http\Controllers\Organizer\TenantSettingsController::class, 'editTerms'])->name('settings.terms');
    Route::post('settings/terms', [App\Http\Controllers\Organizer\TenantSettingsController::class, 'updateTerms'])->name('settings.terms.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
