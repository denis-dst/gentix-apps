<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::where('tenant_id', auth()->user()->tenant_id)
            ->with(['event', 'category', 'transaction'])
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where('ticket_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('transaction', function($q) use ($request) {
                      $q->where('customer_name', 'like', '%' . $request->search . '%');
                  });
        }

        $tickets = $query->paginate(20);
        
        return view('organizer.checkin.index', compact('tickets'));
    }

    public function redeem($id)
    {
        $ticket = Ticket::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        if ($ticket->status === 'redeemed') {
            return back()->with('error', 'Tiket ini sudah pernah digunakan (Redeemed).');
        }

        if ($ticket->status !== 'sold') {
            return back()->with('error', 'Tiket ini tidak valid untuk digunakan.');
        }

        $ticket->update([
            'status' => 'redeemed',
            'redeemed_at' => now(),
            'redeemed_by' => auth()->id()
        ]);

        return back()->with('success', 'Redeem (Penukaran) berhasil! Tiket kini aktif.');
    }
}
