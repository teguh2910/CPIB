<?php

namespace App\Http\Controllers;

use App\Models\ImportTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportTransaksiController extends Controller
{
    public function index()
    {
        $transaksi = ImportTransaksi::where('user_id', Auth::id())->first();

        return view('import.sections.transaksi', compact('transaksi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Harga
            'harga_valuta' => 'required|string',
            'harga_ndpbm' => 'required|numeric|min:0',
            'harga_jenis' => 'required|string',
            'harga_incoterm' => 'required|string',
            'harga_barang' => 'required|numeric|min:0',

            // Biaya Lainnya
            'biaya_penambah' => 'nullable|numeric|min:0',
            'biaya_pengurang' => 'nullable|numeric|min:0',
            'biaya_freight' => 'nullable|numeric|min:0',
            'biaya_jenis_asuransi' => 'required|string',
            'biaya_asuransi' => 'nullable|numeric|min:0',
            'biaya_voluntary_on' => 'nullable|boolean',
            'biaya_voluntary_amt' => 'nullable|numeric|min:0',

            // Berat
            'berat_kotor' => 'required|numeric|min:0',
            'berat_bersih' => 'required|numeric|min:0',
        ]);

        // Calculate nilai pabean (NDPBM × Harga Barang)
        $validated['harga_nilai_pabean'] = $validated['harga_ndpbm'] * $validated['harga_barang'];

        $validated['user_id'] = Auth::id();

        // Delete existing transaksi for this user and create new one
        ImportTransaksi::where('user_id', Auth::id())->delete();
        $transaksi = ImportTransaksi::create($validated);

        return redirect()->back()->with('success', 'Data transaksi berhasil disimpan');
    }

    public function edit(ImportTransaksi $transaksi)
    {
        // Ensure user owns this transaksi
        if ($transaksi->user_id !== Auth::id()) {
            abort(403);
        }

        return view('import.sections.transaksi-edit', compact('transaksi'));
    }

    public function update(Request $request, ImportTransaksi $transaksi)
    {
        // Ensure user owns this transaksi
        if ($transaksi->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            // Harga
            'harga_valuta' => 'required|string',
            'harga_ndpbm' => 'required|numeric|min:0',
            'harga_jenis' => 'required|string',
            'harga_incoterm' => 'required|string',
            'harga_barang' => 'required|numeric|min:0',

            // Biaya Lainnya
            'biaya_penambah' => 'nullable|numeric|min:0',
            'biaya_pengurang' => 'nullable|numeric|min:0',
            'biaya_freight' => 'nullable|numeric|min:0',
            'biaya_jenis_asuransi' => 'required|string',
            'biaya_asuransi' => 'nullable|numeric|min:0',
            'biaya_voluntary_on' => 'nullable|boolean',
            'biaya_voluntary_amt' => 'nullable|numeric|min:0',

            // Berat
            'berat_kotor' => 'required|numeric|min:0',
            'berat_bersih' => 'required|numeric|min:0',
        ]);

        // Calculate nilai pabean (NDPBM × Harga Barang)
        $validated['harga_nilai_pabean'] = $validated['harga_ndpbm'] * $validated['harga_barang'];

        $transaksi->update($validated);

        return redirect()->route('import.transaksi.index')->with('success', 'Data transaksi berhasil diperbarui');
    }

    public function destroy(ImportTransaksi $transaksi)
    {
        // Ensure user owns this transaksi
        if ($transaksi->user_id !== Auth::id()) {
            abort(403);
        }

        $transaksi->delete();

        return redirect()->back()->with('success', 'Data transaksi berhasil dihapus');
    }
}
