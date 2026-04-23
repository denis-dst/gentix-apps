<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketViewController extends Controller
{
    /**
     * Display the e-voucher for the visitor.
     */
    public function show($code)
    {
        $ticket = Ticket::with(['event', 'category'])
            ->where('ticket_code', $code)
            ->firstOrFail();

        return view('tickets.view', compact('ticket'));
    }
}
