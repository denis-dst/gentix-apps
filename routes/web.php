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
Route::post('/doku/notification', [PublicEventController::class, 'handleDokuNotification'])->name('doku.notification');
Route::get('/checkout/success/{reference}', [App\Http\Controllers\PublicEventController::class, 'success'])->name('checkout.success');
Route::get('/evoucher/{reference}', [App\Http\Controllers\PublicEventController::class, 'evoucher'])->name('evoucher.public');

Route::get('/tickets/view/{code}', [App\Http\Controllers\TicketViewController::class, 'show'])->name('tickets.view');

Route::get('/lang/{locale}', [App\Http\Controllers\LanguageController::class, 'switchLang'])->name('lang.switch');

Route::get('/p/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('pages.show');

Route::get('/portofolio', function () {
    return view('portofolio');
})->name('portofolio');

Route::get('/portfolio', function () {
    return redirect()->route('portofolio');
});

Route::middleware(['auth', 'role:Superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
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
    Route::post('transactions/{transaction}/mark-as-paid', [App\Http\Controllers\SuperAdmin\TransactionController::class, 'markAsPaid'])->name('transactions.mark-as-paid');
    Route::post('transactions/{transaction}/resend-evoucher', [App\Http\Controllers\SuperAdmin\TransactionController::class, 'resendEvoucher'])->name('transactions.resend-evoucher');
    Route::get('transactions/{transaction}/print-evoucher', [App\Http\Controllers\SuperAdmin\TransactionController::class, 'printEvoucher'])->name('transactions.print-evoucher');
    Route::post('tickets/{ticket}/cancel', [App\Http\Controllers\SuperAdmin\TransactionController::class, 'cancelTicket'])->name('tickets.cancel');
    Route::post('transactions/{transaction}/cancel', [App\Http\Controllers\SuperAdmin\TransactionController::class, 'cancelTransaction'])->name('transactions.cancel');
    Route::post('transactions/{transaction}/cancel-tickets', [App\Http\Controllers\SuperAdmin\TransactionController::class, 'cancelTickets'])->name('transactions.cancel-tickets');
    Route::get('reports', [App\Http\Controllers\SuperAdmin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-excel', [App\Http\Controllers\SuperAdmin\ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('settings', [App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.update');

    // Invoice Management
    Route::post('invoices/{invoice}/send', [App\Http\Controllers\SuperAdmin\InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/{invoice}/confirm-payment', [App\Http\Controllers\SuperAdmin\InvoiceController::class, 'confirmPayment'])->name('invoices.confirm-payment');
    Route::get('invoices/{invoice}/download-pdf', [App\Http\Controllers\SuperAdmin\InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
    Route::get('invoices/{invoice}/view-proof', [App\Http\Controllers\SuperAdmin\InvoiceController::class, 'viewProof'])->name('invoices.view-proof');
    Route::resource('invoices', App\Http\Controllers\SuperAdmin\InvoiceController::class);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->hasRole('Superadmin')) {
        return redirect()->route('superadmin.dashboard');
    }
    
    if ($user->hasRole('Penyedia Event')) {
        return redirect()->route('organizer.dashboard');
    }

    if ($user->hasRole('Petugas Loket')) {
        return redirect()->route('organizer.redeem.index');
    }

    if ($user->hasRole('Petugas Gate')) {
        return redirect()->route('organizer.gate.index');
    }
    // Fallback for other roles or unassigned
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:Superadmin|Penyedia Event|Petugas Loket|Petugas Gate', 'tenant.status'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Organizer\DashboardController::class, 'index'])->name('dashboard');
    
    // Event Management
    Route::resource('events', App\Http\Controllers\Organizer\EventController::class);
    Route::resource('events.categories', App\Http\Controllers\Organizer\TicketCategoryController::class);
    Route::get('categories/{category}/print-wristbands', [App\Http\Controllers\WristbandPrintController::class, 'print'])->name('categories.print-wristbands');
    
    // Voucher/Promo Management
    Route::resource('vouchers', App\Http\Controllers\Organizer\PromoCodeController::class);
    
    // Reports & Operations
    Route::get('reports', [App\Http\Controllers\Organizer\ReportController::class, 'index'])->middleware('role:Penyedia Event')->name('reports.index');
    Route::get('reports/duplicates', [App\Http\Controllers\Organizer\ReportController::class, 'duplicates'])->middleware('role:Penyedia Event')->name('reports.duplicates');
    Route::get('reports/export-excel', [App\Http\Controllers\Organizer\ReportController::class, 'exportExcel'])->middleware('role:Penyedia Event')->name('reports.export-excel');
    Route::get('checkin', [App\Http\Controllers\Organizer\CheckinController::class, 'index'])->name('checkin.index');
    Route::post('checkin/{id}/redeem', [App\Http\Controllers\Organizer\CheckinController::class, 'redeem'])->name('checkin.redeem');
    
    // Finance
    Route::get('finance', [App\Http\Controllers\Organizer\FinanceController::class, 'index'])->name('finance.index');
    
    // Crew Management
    Route::resource('crews', App\Http\Controllers\Organizer\CrewController::class);

    // Sales Transactions & E-Voucher
    Route::middleware('role:Penyedia Event|Petugas Loket')->group(function () {
        Route::get('pos', [App\Http\Controllers\Organizer\POSController::class, 'index'])->name('pos.index');
        Route::get('pos/events/{event}', [App\Http\Controllers\Organizer\POSController::class, 'create'])->name('pos.create');
        Route::post('pos/events/{event}', [App\Http\Controllers\Organizer\POSController::class, 'store'])->name('pos.store');
        Route::get('pos/transactions/{transaction}/print', [App\Http\Controllers\Organizer\POSController::class, 'print'])->name('pos.print');
    });
    Route::get('transactions', [App\Http\Controllers\Organizer\TransactionController::class, 'index'])->name('transactions.index');
    Route::post('transactions/{transaction}/mark-as-paid', [App\Http\Controllers\Organizer\TransactionController::class, 'markAsPaid'])->name('transactions.mark-as-paid');
    Route::post('transactions/{transaction}/resend-evoucher', [App\Http\Controllers\Organizer\TransactionController::class, 'resendEvoucher'])->name('transactions.resend-evoucher');
    Route::get('transactions/{transaction}/print-evoucher', [App\Http\Controllers\Organizer\TransactionController::class, 'printEvoucher'])->name('transactions.print-evoucher');
    Route::post('transactions/{transaction}/send-whatsapp', [App\Http\Controllers\Organizer\TransactionController::class, 'sendWhatsApp'])->name('transactions.send-whatsapp');
    Route::post('tickets/{ticket}/cancel', [App\Http\Controllers\Organizer\TransactionController::class, 'cancelTicket'])->name('tickets.cancel');
    Route::post('transactions/{transaction}/cancel', [App\Http\Controllers\Organizer\TransactionController::class, 'cancelTransaction'])->name('transactions.cancel');
    Route::post('transactions/{transaction}/cancel-tickets', [App\Http\Controllers\Organizer\TransactionController::class, 'cancelTickets'])->name('transactions.cancel-tickets');

    // Redeem System
    Route::get('redeem', [App\Http\Controllers\Organizer\RedeemController::class, 'index'])->name('redeem.index');
    Route::get('redeem/{event}/verify', [App\Http\Controllers\Organizer\RedeemController::class, 'verifyForm'])->name('redeem.verify');
    Route::post('redeem/{event}/verify', [App\Http\Controllers\Organizer\RedeemController::class, 'verify'])->name('redeem.verify.post');
    Route::get('redeem/{event}/scan', [App\Http\Controllers\Organizer\RedeemController::class, 'scan'])->name('redeem.scan');
    Route::post('redeem/check', [App\Http\Controllers\Organizer\RedeemController::class, 'check'])->name('redeem.check');
    Route::get('redeem/{event}/download', [App\Http\Controllers\Organizer\RedeemController::class, 'downloadData'])->name('redeem.download');
    Route::post('redeem/process', [App\Http\Controllers\Organizer\RedeemController::class, 'process'])->name('redeem.process');

    // Gate System (Automatic Scan)
    Route::resource('events.gates', App\Http\Controllers\Organizer\GateManagementController::class);
    Route::get('gate', [App\Http\Controllers\Organizer\GateController::class, 'index'])->name('gate.index');
    Route::get('gate/{event}/verify', [App\Http\Controllers\Organizer\GateController::class, 'verifyForm'])->name('gate.verify');
    Route::post('gate/{event}/verify', [App\Http\Controllers\Organizer\GateController::class, 'verify'])->name('gate.verify.post');
    Route::get('gate/{event}/setup', [App\Http\Controllers\Organizer\GateController::class, 'setupForm'])->name('gate.setup');
    Route::post('gate/{event}/setup', [App\Http\Controllers\Organizer\GateController::class, 'setup'])->name('gate.setup.post');
    Route::get('gate/{event}/scan', [App\Http\Controllers\Organizer\GateController::class, 'scan'])->name('gate.scan');
    Route::post('gate/process', [App\Http\Controllers\Organizer\GateController::class, 'process'])->name('gate.process');
    Route::post('gate/{event}/bulk-checkin', [App\Http\Controllers\Organizer\GateController::class, 'bulkCheckin'])->name('gate.bulk-checkin');

    // Tenant Settings (T&C)
    Route::get('settings/terms', [App\Http\Controllers\Organizer\TenantSettingsController::class, 'editTerms'])->name('settings.terms');
    Route::post('settings/terms', [App\Http\Controllers\Organizer\TenantSettingsController::class, 'updateTerms'])->name('settings.terms.update');

    // Invoice (Tenant View)
    Route::get('invoices', [App\Http\Controllers\Organizer\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [App\Http\Controllers\Organizer\InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('invoices/{invoice}/upload-proof', [App\Http\Controllers\Organizer\InvoiceController::class, 'uploadProof'])->name('invoices.upload-proof');
    Route::get('invoices/{invoice}/download-pdf', [App\Http\Controllers\Organizer\InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
    Route::post('invoices/dismiss-modal', [App\Http\Controllers\Organizer\InvoiceController::class, 'dismissModal'])->name('invoices.dismiss-modal');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
