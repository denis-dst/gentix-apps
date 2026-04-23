<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Models\Event;
use App\Models\Setting;

Route::get('/', function () {
    $events = Event::where('status', 'published')
        ->orderBy('event_start_date', 'asc')
        ->take(6)
        ->get();
    
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

    return view('welcome', compact('events', 'settings'));
});

Route::get('/event/{slug}', [App\Http\Controllers\PublicEventController::class, 'show'])->name('events.show');
Route::post('/event/{slug}/checkout', [App\Http\Controllers\PublicEventController::class, 'checkout'])->name('checkout.process');
Route::get('/checkout/success/{reference}', [App\Http\Controllers\PublicEventController::class, 'success'])->name('checkout.success');

Route::get('/tickets/view/{code}', [App\Http\Controllers\TicketViewController::class, 'show'])->name('tickets.view');


Route::middleware(['auth', 'role:Superadmin|superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
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
    if (auth()->user()->hasRole(['Superadmin'])) {
        return redirect()->route('superadmin.dashboard');
    }
    if (auth()->user()->hasRole(['Penyedia Event'])) {
        return redirect()->route('organizer.dashboard');
    }
    return redirect('/'); // Fallback for other roles or unassigned
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:Penyedia Event|Superadmin', 'tenant.status'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Organizer\DashboardController::class, 'index'])->name('dashboard');
    
    // Event Management
    Route::resource('events', App\Http\Controllers\Organizer\EventController::class)->only(['edit', 'update']);
    Route::resource('events.categories', App\Http\Controllers\Organizer\TicketCategoryController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
