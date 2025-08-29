<?php

namespace App\Http\Controllers;

use App\Models\ImportBarang;
use App\Models\ImportPungutan;
use App\Models\ImportTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportPungutanController extends Controller
{
    public function index()
    {
        $pungutan = ImportPungutan::where('user_id', Auth::id())->first();
        $cifBasis = $this->calculateCifBasis();

        return view('import.sections.pungutan', compact('pungutan', 'cifBasis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bm_percent' => 'nullable|numeric|min:0',
            'ppn_percent' => 'nullable|numeric|min:0',
            'pph_percent' => 'nullable|numeric|min:0',
        ]);

        // Calculate CIF basis
        $cifBasis = $this->calculateCifBasis();

        // Calculate the pungutan values
        $bmPercent = $validated['bm_percent'] ?? 0;
        $ppnPercent = $validated['ppn_percent'] ?? 0;
        $pphPercent = $validated['pph_percent'] ?? 0;

        $beaMasuk = $cifBasis * ($bmPercent / 100);
        $ppn = ($cifBasis + $beaMasuk) * ($ppnPercent / 100);
        $pph = ($cifBasis + $beaMasuk) * ($pphPercent / 100);
        $totalPungutan = $beaMasuk + $ppn + $pph;

        $validated['user_id'] = Auth::id();
        $validated['bea_masuk'] = $beaMasuk;
        $validated['ppn'] = $ppn;
        $validated['pph'] = $pph;
        $validated['total_pungutan'] = $totalPungutan;

        // Delete existing pungutan for this user and create new one
        ImportPungutan::where('user_id', Auth::id())->delete();
        $pungutan = ImportPungutan::create($validated);

        return redirect()->back()->with('success', 'Data pungutan berhasil disimpan');
    }

    public function edit(ImportPungutan $pungutan)
    {
        // Ensure user owns this pungutan
        if ($pungutan->user_id !== Auth::id()) {
            abort(403);
        }

        $cifBasis = $this->calculateCifBasis();

        return view('import.sections.pungutan-edit', compact('pungutan', 'cifBasis'));
    }

    public function update(Request $request, ImportPungutan $pungutan)
    {
        // Ensure user owns this pungutan
        if ($pungutan->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'bm_percent' => 'nullable|numeric|min:0',
            'ppn_percent' => 'nullable|numeric|min:0',
            'pph_percent' => 'nullable|numeric|min:0',
        ]);

        // Calculate CIF basis
        $cifBasis = $this->calculateCifBasis();

        // Calculate the pungutan values
        $bmPercent = $validated['bm_percent'] ?? 0;
        $ppnPercent = $validated['ppn_percent'] ?? 0;
        $pphPercent = $validated['pph_percent'] ?? 0;

        $beaMasuk = $cifBasis * ($bmPercent / 100);
        $ppn = ($cifBasis + $beaMasuk) * ($ppnPercent / 100);
        $pph = ($cifBasis + $beaMasuk) * ($pphPercent / 100);
        $totalPungutan = $beaMasuk + $ppn + $pph;

        $validated['bea_masuk'] = $beaMasuk;
        $validated['ppn'] = $ppn;
        $validated['pph'] = $pph;
        $validated['total_pungutan'] = $totalPungutan;

        $pungutan->update($validated);

        return redirect()->route('import.pungutan.index')->with('success', 'Data pungutan berhasil diperbarui');
    }

    public function destroy(ImportPungutan $pungutan)
    {
        // Ensure user owns this pungutan
        if ($pungutan->user_id !== Auth::id()) {
            abort(403);
        }

        $pungutan->delete();

        return redirect()->back()->with('success', 'Data pungutan berhasil dihapus');
    }

    private function calculateCifBasis()
    {
        // Get CIF from transaksi data first
        $transaksi = ImportTransaksi::where('user_id', Auth::id())->first();
        if ($transaksi && $transaksi->cif > 0) {
            return $transaksi->cif;
        }

        // If no transaksi CIF, calculate from barang items
        $barangItems = ImportBarang::where('user_id', Auth::id())->get();
        $totalNilaiPabean = $barangItems->sum('nilai_pabean_rp');

        return $totalNilaiPabean;
    }
}
