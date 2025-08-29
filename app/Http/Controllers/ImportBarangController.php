<?php

namespace App\Http\Controllers;

use App\Models\ImportBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportBarangController extends Controller
{
    public function index()
    {
        $barangData = ImportBarang::where('user_id', Auth::id())->get();

        // Get NDPBM from transaksi data
        $transaksiData = ImportTransaksi::where('user_id', Auth::id())->first();
        $ndpbm = $transaksiData ? $transaksiData->harga_ndpbm : 1;

        // Get dokumen data for dropdown
        $dokumenData = ImportDokumen::where('user_id', Auth::id())->get();
        $dokList = $dokumenData->map(function ($d) {
            $jenis = config('import.jenis_dokumen')[$d->jenis] ?? $d->jenis;
            $tgl = $d->tanggal ?? '';
            $no = $d->nomor ?? '';

            return trim("$jenis - $no ($tgl)");
        })->values()->all();

        return view('import.sections.barang', compact('barangData', 'ndpbm', 'dokList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seri' => 'required|integer|min:1',
            'pos_tarif' => 'required|string|max:20',
            'lartas' => 'required|boolean',
            'kode_barang' => 'nullable|string|max:50',
            'uraian' => 'required|string|max:500',
            'spesifikasi' => 'nullable|string|max:500',
            'kondisi' => 'nullable|string',
            'negara_asal' => 'required|string|size:2',
            'berat_bersih' => 'required|numeric|min:0',
            'jumlah' => 'required|numeric|min:0.0000001',
            'satuan' => 'required|string',
            'jml_kemasan' => 'nullable|numeric|min:0',
            'jenis_kemasan' => 'nullable|string',
            'nilai_barang' => 'required|numeric|min:0',
            'fob' => 'nullable|numeric|min:0',
            'freight' => 'nullable|numeric|min:0',
            'asuransi' => 'nullable|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'nilai_pabean_rp' => 'required|numeric|min:0',
            'dokumen_fasilitas' => 'nullable|string|max:150',
            'ket_bm' => 'nullable|string',
            'tarif_bm' => 'nullable|numeric|min:0',
            'bayar_bm' => 'nullable|numeric|min:0',
            'ppn_tarif' => 'nullable|integer|in:0,11,12',
            'ket_ppn' => 'nullable|string',
            'bayar_ppn' => 'nullable|numeric|min:0',
            'ket_pph' => 'nullable|string',
            'tarif_pph' => 'nullable|numeric|min:0',
            'bayar_pph' => 'nullable|numeric|min:0',
        ]);

        $userId = Auth::id();

        ImportBarang::create([
            'user_id' => $userId,
            'seri' => $validated['seri'],
            'pos_tarif' => $validated['pos_tarif'],
            'lartas' => $validated['lartas'],
            'kode_barang' => $validated['kode_barang'],
            'uraian' => $validated['uraian'],
            'spesifikasi' => $validated['spesifikasi'],
            'kondisi' => $validated['kondisi'],
            'negara_asal' => $validated['negara_asal'],
            'berat_bersih' => $validated['berat_bersih'],
            'jumlah' => $validated['jumlah'],
            'satuan' => $validated['satuan'],
            'jml_kemasan' => $validated['jml_kemasan'],
            'jenis_kemasan' => $validated['jenis_kemasan'],
            'nilai_barang' => $validated['nilai_barang'],
            'fob' => $validated['fob'],
            'freight' => $validated['freight'],
            'asuransi' => $validated['asuransi'],
            'harga_satuan' => $validated['harga_satuan'],
            'nilai_pabean_rp' => $validated['nilai_pabean_rp'],
            'dokumen_fasilitas' => $validated['dokumen_fasilitas'],
            'ket_bm' => $validated['ket_bm'],
            'tarif_bm' => $validated['tarif_bm'],
            'bayar_bm' => $validated['bayar_bm'],
            'ppn_tarif' => $validated['ppn_tarif'],
            'ket_ppn' => $validated['ket_ppn'],
            'bayar_ppn' => $validated['bayar_ppn'],
            'ket_pph' => $validated['ket_pph'],
            'tarif_pph' => $validated['tarif_pph'],
            'bayar_pph' => $validated['bayar_pph'],
        ]);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = ImportBarang::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        return view('import.sections.barang-edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'seri' => 'required|integer|min:1',
            'pos_tarif' => 'required|string|max:20',
            'lartas' => 'required|boolean',
            'kode_barang' => 'nullable|string|max:50',
            'uraian' => 'required|string|max:500',
            'spesifikasi' => 'nullable|string|max:500',
            'kondisi' => 'nullable|string',
            'negara_asal' => 'required|string|size:2',
            'berat_bersih' => 'required|numeric|min:0',
            'jumlah' => 'required|numeric|min:0.0000001',
            'satuan' => 'required|string',
            'jml_kemasan' => 'nullable|numeric|min:0',
            'jenis_kemasan' => 'nullable|string',
            'nilai_barang' => 'required|numeric|min:0',
            'fob' => 'nullable|numeric|min:0',
            'freight' => 'nullable|numeric|min:0',
            'asuransi' => 'nullable|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'nilai_pabean_rp' => 'required|numeric|min:0',
            'dokumen_fasilitas' => 'nullable|string|max:150',
            'ket_bm' => 'nullable|string',
            'tarif_bm' => 'nullable|numeric|min:0',
            'bayar_bm' => 'nullable|numeric|min:0',
            'ppn_tarif' => 'nullable|integer|in:0,11,12',
            'ket_ppn' => 'nullable|string',
            'bayar_ppn' => 'nullable|numeric|min:0',
            'ket_pph' => 'nullable|string',
            'tarif_pph' => 'nullable|numeric|min:0',
            'bayar_pph' => 'nullable|numeric|min:0',
        ]);

        $barang = ImportBarang::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $barang->update([
            'seri' => $validated['seri'],
            'pos_tarif' => $validated['pos_tarif'],
            'lartas' => $validated['lartas'],
            'kode_barang' => $validated['kode_barang'],
            'uraian' => $validated['uraian'],
            'spesifikasi' => $validated['spesifikasi'],
            'kondisi' => $validated['kondisi'],
            'negara_asal' => $validated['negara_asal'],
            'berat_bersih' => $validated['berat_bersih'],
            'jumlah' => $validated['jumlah'],
            'satuan' => $validated['satuan'],
            'jml_kemasan' => $validated['jml_kemasan'],
            'jenis_kemasan' => $validated['jenis_kemasan'],
            'nilai_barang' => $validated['nilai_barang'],
            'fob' => $validated['fob'],
            'freight' => $validated['freight'],
            'asuransi' => $validated['asuransi'],
            'harga_satuan' => $validated['harga_satuan'],
            'nilai_pabean_rp' => $validated['nilai_pabean_rp'],
            'dokumen_fasilitas' => $validated['dokumen_fasilitas'],
            'ket_bm' => $validated['ket_bm'],
            'tarif_bm' => $validated['tarif_bm'],
            'bayar_bm' => $validated['bayar_bm'],
            'ppn_tarif' => $validated['ppn_tarif'],
            'ket_ppn' => $validated['ket_ppn'],
            'bayar_ppn' => $validated['bayar_ppn'],
            'ket_pph' => $validated['ket_pph'],
            'tarif_pph' => $validated['tarif_pph'],
            'bayar_pph' => $validated['bayar_pph'],
        ]);

        return redirect()->route('import.barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $barang = ImportBarang::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $barang->delete();

        return redirect()->back()->with('success', 'Barang berhasil dihapus');
    }
}
