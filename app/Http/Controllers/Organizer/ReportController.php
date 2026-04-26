<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('tenant_id', auth()->user()->tenant_id)
            ->with(['event', 'tickets'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('organizer.reports.index', compact('transactions'));
    }
}
