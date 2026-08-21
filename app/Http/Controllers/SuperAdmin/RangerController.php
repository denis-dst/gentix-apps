<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Ranger;
use App\Models\RangerGateQuota;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RangerController extends Controller
{
    /**
     * Display listing of rangers, gate quota configuration, and generation controls.
     */
    public function index(Request $request)
    {
        $quotas = RangerGateQuota::orderBy('id', 'asc')->get();

        $query = Ranger::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('assigned_gate', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('role_filter')) {
            if ($request->role_filter === 'spv') {
                $query->where('is_spv', true);
            } elseif ($request->role_filter === 'offday') {
                $query->where('is_offday', true);
            } elseif ($request->role_filter === 'active') {
                $query->where('is_offday', false)->where('is_spv', false);
            }
        }

        if ($request->filled('gate')) {
            if ($request->gate === 'unassigned') {
                $query->whereNull('assigned_gate');
            } else {
                $query->where('assigned_gate', $request->gate);
            }
        }

        $rangers = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total' => Ranger::count(),
            'male' => Ranger::male()->count(),
            'female' => Ranger::female()->count(),
            'spv' => Ranger::where('is_spv', true)->count(),
            'offday' => Ranger::where('is_offday', true)->count(),
            'assigned' => Ranger::assigned()->count(),
            'unassigned' => Ranger::unassigned()->count(),
            'active_plotting_pool' => Ranger::availableForPlotting()->count(),
        ];

        return view('superadmin.rangers.index', compact('quotas', 'rangers', 'stats'));
    }

    /**
     * Update gate requirement quotas (Male & Female counts).
     */
    public function updateQuotas(Request $request)
    {
        $request->validate([
            'quotas' => 'required|array',
            'quotas.*.id' => 'required|exists:ranger_gate_quotas,id',
            'quotas.*.male_quota' => 'required|integer|min:0',
            'quotas.*.female_quota' => 'required|integer|min:0',
        ]);

        foreach ($request->quotas as $data) {
            RangerGateQuota::where('id', $data['id'])->update([
                'male_quota' => (int) $data['male_quota'],
                'female_quota' => (int) $data['female_quota'],
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan quota gate berhasil diperbarui.');
    }

    /**
     * Generate Crew: Plot rangers into designated gates according to defined gender quotas.
     * EXCLUDES any ranger marked as is_offday or is_spv.
     */
    public function generateCrew(Request $request)
    {
        $quotas = RangerGateQuota::orderBy('id', 'asc')->get();

        // Check if there are quotas configured
        $totalRequiredMale = $quotas->sum('male_quota');
        $totalRequiredFemale = $quotas->sum('female_quota');

        if ($totalRequiredMale == 0 && $totalRequiredFemale == 0) {
            return redirect()->back()->with('error', 'Silakan tentukan kebutuhan (quota) Pria & Wanita untuk setiap gate terlebih dahulu.');
        }

        // Reset existing plotting assignments for clean distribution
        Ranger::query()->update([
            'assigned_gate' => null,
            'assigned_at' => null,
        ]);

        // ONLY take rangers who are NOT offday and NOT SPV
        $availableMales = Ranger::male()->availableForPlotting()->inRandomOrder()->get();
        $availableFemales = Ranger::female()->availableForPlotting()->inRandomOrder()->get();

        $assignedMaleCount = 0;
        $assignedFemaleCount = 0;
        $now = now();

        foreach ($quotas as $quota) {
            // Plot Male rangers
            if ($quota->male_quota > 0) {
                $malesToAssign = $availableMales->splice(0, $quota->male_quota);
                foreach ($malesToAssign as $ranger) {
                    $ranger->update([
                        'assigned_gate' => $quota->gate_name,
                        'assigned_at' => $now,
                    ]);
                    $assignedMaleCount++;
                }
            }

            // Plot Female rangers
            if ($quota->female_quota > 0) {
                $femalesToAssign = $availableFemales->splice(0, $quota->female_quota);
                foreach ($femalesToAssign as $ranger) {
                    $ranger->update([
                        'assigned_gate' => $quota->gate_name,
                        'assigned_at' => $now,
                    ]);
                    $assignedFemaleCount++;
                }
            }
        }

        $message = "Generate Crew Berhasil! {$assignedMaleCount} Laki-laki dan {$assignedFemaleCount} Perempuan berhasil ditempatkan di Gate & Redemption (Ranger Offday & SPV tidak dimasukkan ke dalam plotting).";

        if ($assignedMaleCount < $totalRequiredMale || $assignedFemaleCount < $totalRequiredFemale) {
            $shortageMale = max(0, $totalRequiredMale - $assignedMaleCount);
            $shortageFemale = max(0, $totalRequiredFemale - $assignedFemaleCount);
            $message .= " (Catatan: Kekurangan ranger: {$shortageMale} Laki-laki, {$shortageFemale} Perempuan).";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle Offday status for a ranger.
     */
    public function toggleOffday(Request $request, Ranger $ranger)
    {
        $newStatus = !$ranger->is_offday;
        $updates = ['is_offday' => $newStatus];

        // If marked as Offday, remove gate plotting
        if ($newStatus) {
            $updates['assigned_gate'] = null;
            $updates['assigned_at'] = null;
        }

        $ranger->update($updates);

        $msg = $newStatus
            ? "Status Offday untuk {$ranger->name} telah DIAKTIFKAN (dikeluarkan dari plotting gate)."
            : "Status Offday untuk {$ranger->name} telah DINONAKTIFKAN (siap untuk plotting gate).";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_offday' => $newStatus,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Toggle SPV status for a ranger.
     */
    public function toggleSpv(Request $request, Ranger $ranger)
    {
        $newStatus = !$ranger->is_spv;
        $updates = ['is_spv' => $newStatus];

        // If marked as SPV, remove regular gate plotting
        if ($newStatus) {
            $updates['assigned_gate'] = null;
            $updates['assigned_at'] = null;
        }

        $ranger->update($updates);

        $msg = $newStatus
            ? "{$ranger->name} berhasil ditandai sebagai ⭐ SPV (dikeluarkan dari plotting gate reguler)."
            : "Status SPV untuk {$ranger->name} telah dinonaktifkan.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_spv' => $newStatus,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Update gate assignment for an individual ranger manually.
     */
    public function updateAssignment(Request $request, Ranger $ranger)
    {
        $request->validate([
            'assigned_gate' => 'nullable|string|max:255',
        ]);

        $gate = $request->assigned_gate ?: null;

        $ranger->update([
            'assigned_gate' => $gate,
            'assigned_at' => $gate ? now() : null,
        ]);

        return redirect()->back()->with('success', "Plotting pos/gate untuk {$ranger->name} berhasil diperbarui.");
    }

    /**
     * Reset all ranger gate assignments.
     */
    public function resetAssignments()
    {
        Ranger::query()->update([
            'assigned_gate' => null,
            'assigned_at' => null,
        ]);

        return redirect()->back()->with('success', 'Seluruh plotting gate ranger telah di-reset.');
    }

    /**
     * Delete a ranger record.
     */
    public function destroy(Ranger $ranger)
    {
        $ranger->delete();

        return redirect()->back()->with('success', 'Data Ranger berhasil dihapus.');
    }

    /**
     * Export Ranger List & Plotting to CSV file.
     */
    public function export()
    {
        $filename = 'Ranger_Bhayangkara_FC_' . date('Y-m-d_H-i-s') . '.csv';

        $rangers = Ranger::orderBy('is_spv', 'desc')->orderBy('is_offday', 'asc')->orderBy('assigned_gate', 'asc')->orderBy('name', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rangers) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Column Headers
            fputcsv($file, [
                'ID',
                'Nama Lengkap',
                'Peran / Role',
                'Status Kehadiran',
                'Nomor WhatsApp',
                'Gender',
                'Nama Bank / E-Wallet',
                'Nomor Rekening / E-Wallet',
                'Gate / Posisi Plotting',
                'Tanggal Registrasi',
            ]);

            foreach ($rangers as $ranger) {
                $role = $ranger->is_spv ? 'SPV (Supervisor)' : 'Ranger Reguler';
                $status = $ranger->is_offday ? 'Offday' : 'Aktif';

                fputcsv($file, [
                    $ranger->id,
                    $ranger->name,
                    $role,
                    $status,
                    $ranger->whatsapp,
                    $ranger->gender === 'male' ? 'Laki-laki (Male)' : 'Perempuan (Female)',
                    $ranger->bank_name,
                    $ranger->account_number,
                    $ranger->assigned_gate ?? 'Belum Di-plot',
                    $ranger->created_at ? $ranger->created_at->format('Y-m-d H:i:s') : '-',
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
