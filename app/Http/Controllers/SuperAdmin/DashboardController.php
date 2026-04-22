<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tenant;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'total_events' => Event::count(),
            'total_transactions' => Transaction::where('payment_status', 'paid')->count(),
            'total_revenue' => Transaction::where('payment_status', 'paid')->sum('total_amount'),
            'total_tickets' => Ticket::count(),
            'total_users' => User::count(),
        ];

        $recent_transactions = Transaction::with(['event', 'buyer'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $active_events = Event::where('status', 'published')
            ->with('tenant')
            ->orderBy('event_start_date', 'asc')
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recent_transactions', 'active_events'));
    }
}
