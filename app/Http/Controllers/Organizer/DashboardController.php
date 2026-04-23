<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        
        $events = Event::withCount(['tickets', 'ticketCategories'])
            ->where('tenant_id', $tenantId)
            ->orderBy('event_start_date', 'desc')
            ->get();

        return view('organizer.dashboard', compact('events'));
    }
}
