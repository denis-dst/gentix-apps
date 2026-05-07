<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Http\Request;

class WristbandPrintController extends Controller
{
    /**
     * Display a printable view of wristbands for a specific category.
     */
    public function print(Request $request, TicketCategory $category)
    {
        // Check authorization
        $this->authorizeAccess($category->event);

        $status = $request->get('status', 'sold'); // Default to printing sold tickets
        
        $query = Ticket::where('ticket_category_id', $category->id)
            ->with(['transaction', 'category', 'event']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $tickets = $query->orderBy('ticket_code', 'asc')->get();

        if ($tickets->isEmpty()) {
            return back()->with('error', 'Tidak ada tiket untuk dicetak dengan filter ini.');
        }

        return view('wristbands.print', [
            'tickets' => $tickets,
            'category' => $category,
            'event' => $category->event
        ]);
    }

    private function authorizeAccess(Event $event)
    {
        if (auth()->user()->hasRole('Superadmin')) {
            return;
        }

        if ($event->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
