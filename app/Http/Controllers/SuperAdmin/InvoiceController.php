<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['tenant', 'issuer'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(15)->withQueryString();
        $tenants  = Tenant::orderBy('name')->get();

        return view('superadmin.invoices.index', compact('invoices', 'tenants'));
    }

    public function create()
    {
        $tenants = Tenant::where('status', 'active')->orderBy('name')->get();
        return view('superadmin.invoices.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id'   => 'required|exists:tenants,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'issued_date' => 'required|date',
            'due_date'    => 'required|date|after_or_equal:issued_date',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'notes'       => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $taxPercent = $validated['tax_percent'] ?? 0;
        $subtotal   = 0;

        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $taxAmount   = $subtotal * ($taxPercent / 100);
        $totalAmount = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'tenant_id'      => $validated['tenant_id'],
            'issued_by'      => Auth::id(),
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'issued_date'    => $validated['issued_date'],
            'due_date'       => $validated['due_date'],
            'subtotal'       => $subtotal,
            'tax_percent'    => $taxPercent,
            'tax_amount'     => $taxAmount,
            'total_amount'   => $totalAmount,
            'status'         => $request->action === 'send' ? 'sent' : 'draft',
            'notes'          => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'subtotal'    => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $message = $request->action === 'send'
            ? 'Invoice berhasil diterbitkan dan dikirim ke tenant.'
            : 'Invoice berhasil disimpan sebagai draft.';

        return redirect()->route('superadmin.invoices.show', $invoice)
            ->with('success', $message);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['tenant', 'issuer', 'items']);
        return view('superadmin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('superadmin.invoices.show', $invoice)
                ->with('error', 'Hanya invoice berstatus draft yang dapat diedit.');
        }

        $tenants = Tenant::where('status', 'active')->orderBy('name')->get();
        $invoice->load('items');
        return view('superadmin.invoices.edit', compact('invoice', 'tenants'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('superadmin.invoices.show', $invoice)
                ->with('error', 'Hanya invoice berstatus draft yang dapat diedit.');
        }

        $validated = $request->validate([
            'tenant_id'   => 'required|exists:tenants,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'issued_date' => 'required|date',
            'due_date'    => 'required|date|after_or_equal:issued_date',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'notes'       => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $taxPercent = $validated['tax_percent'] ?? 0;
        $subtotal   = 0;

        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $taxAmount   = $subtotal * ($taxPercent / 100);
        $totalAmount = $subtotal + $taxAmount;

        $invoice->update([
            'tenant_id'    => $validated['tenant_id'],
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'issued_date'  => $validated['issued_date'],
            'due_date'     => $validated['due_date'],
            'subtotal'     => $subtotal,
            'tax_percent'  => $taxPercent,
            'tax_amount'   => $taxAmount,
            'total_amount' => $totalAmount,
            'status'       => $request->action === 'send' ? 'sent' : 'draft',
            'notes'        => $validated['notes'] ?? null,
        ]);

        // Replace all items
        $invoice->items()->delete();
        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'subtotal'    => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $message = $request->action === 'send'
            ? 'Invoice berhasil diperbarui dan dikirim ke tenant.'
            : 'Invoice berhasil diperbarui.';

        return redirect()->route('superadmin.invoices.show', $invoice)
            ->with('success', $message);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('superadmin.invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    /**
     * Ubah status draft → sent (terbitkan & kirim ke tenant)
     */
    public function send(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Invoice sudah diterbitkan sebelumnya.');
        }

        $invoice->update(['status' => 'sent']);

        return back()->with('success', 'Invoice berhasil diterbitkan dan dapat dilihat oleh tenant.');
    }

    /**
     * Superadmin konfirmasi pembayaran → status jadi paid
     */
    public function confirmPayment(Invoice $invoice)
    {
        if ($invoice->status !== 'sent') {
            return back()->with('error', 'Invoice tidak dalam status yang dapat dikonfirmasi.');
        }

        $invoice->update([
            'status'                 => 'paid',
            'payment_confirmed_at'   => now(),
        ]);

        return back()->with('success', 'Pembayaran telah dikonfirmasi. Invoice ditandai sebagai Lunas.');
    }

    /**
     * Download PDF invoice
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['tenant', 'issuer', 'items']);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('superadmin.invoices.pdf', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Tampilkan bukti bayar dari tenant
     */
    public function viewProof(Invoice $invoice)
    {
        if (! $invoice->payment_proof) {
            return back()->with('error', 'Bukti pembayaran belum diunggah.');
        }

        return response()->file(Storage::disk('public')->path($invoice->payment_proof));
    }
}
