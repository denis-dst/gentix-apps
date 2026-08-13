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
            'assigned' => Ranger::assigned()->count(),
            'unassigned' => Ranger::unassigned()->count(),
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

        $availableMales = Ranger::male()->inRandomOrder()->get();
        $availableFemales = Ranger::female()->inRandomOrder()->get();

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

        $message = "Generate Crew Berhasil! {$assignedMaleCount} Laki-laki dan {$assignedFemaleCount} Perempuan berhasil ditempatkan di Gate & Redemption.";

        if ($assignedMaleCount < $totalRequiredMale || $assignedFemaleCount < $totalRequiredFemale) {
            $shortageMale = max(0, $totalRequiredMale - $assignedMaleCount);
            $shortageFemale = max(0, $totalRequiredFemale - $assignedFemaleCount);
            $message .= " (Catatan: Kekurangan ranger pendaftar: {$shortageMale} Laki-laki, {$shortageFemale} Perempuan).";
        }

        return redirect()->back()->with('success', $message);
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

        $rangers = Ranger::orderBy('assigned_gate', 'asc')->orderBy('name', 'asc')->get();

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
                'Nomor WhatsApp',
                'Gender',
                'Nama Bank / E-Wallet',
                'Nomor Rekening / E-Wallet',
                'Gate / Posisi Plotting',
                'Tanggal Registrasi',
            ]);

            foreach ($rangers as $ranger) {
                fputcsv($file, [
                    $ranger->id,
                    $ranger->name,
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
