<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    private function getTenantId(): int
    {
        return Auth::user()->tenant_id;
    }

    public function index()
    {
        $invoices = Invoice::where('tenant_id', $this->getTenantId())
            ->with('issuer')
            ->whereIn('status', ['sent', 'paid', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('organizer.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        // Pastikan invoice milik tenant yang sedang login
        abort_if($invoice->tenant_id !== $this->getTenantId(), 403);

        $invoice->load(['issuer', 'items']);
        return view('organizer.invoices.show', compact('invoice'));
    }

    /**
     * Tenant upload bukti pembayaran
     */
    public function uploadProof(Request $request, Invoice $invoice)
    {
        abort_if($invoice->tenant_id !== $this->getTenantId(), 403);
        abort_if($invoice->status !== 'sent', 403, 'Invoice tidak dalam status yang tepat.');

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Hapus file lama jika ada
        if ($invoice->payment_proof) {
            Storage::disk('public')->delete($invoice->payment_proof);
        }

        $path = $request->file('payment_proof')->store('invoices/proofs', 'public');

        $invoice->update([
            'payment_proof'              => $path,
            'payment_proof_uploaded_at'  => now(),
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi dari admin.');
    }

    /**
     * Tenant download PDF invoice
     */
    public function downloadPdf(Invoice $invoice)
    {
        abort_if($invoice->tenant_id !== $this->getTenantId(), 403);

        $invoice->load(['tenant', 'issuer', 'items']);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('superadmin.invoices.pdf', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Dismiss the invoice notification modal for today
     */
    public function dismissModal()
    {
        $key = 'invoice_modal_dismissed_' . Auth::id();
        session([$key => now()->toDateTimeString()]);
        return back();
    }
}
