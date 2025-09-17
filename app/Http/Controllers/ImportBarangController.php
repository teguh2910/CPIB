<?php

namespace App\Http\Controllers;

use App\Models\ImportBarang;
use App\Models\ImportDokumen;
use App\Models\ImportTransaksi;
use App\Models\ImportBarangDokumen;
use App\Models\ImportBarangVd;
use App\Models\ImportBarangTarif;
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

    public function create()
    {
        // Get NDPBM from transaksi data
        $transaksiData = ImportTransaksi::where('import_notification_id', null)->first();
        $new_ndpbm = $transaksiData ? $transaksiData->ndpbm : 1;
        $dokumenData = ImportDokumen::where('import_notification_id', null)->whereNotNull('kode_fasilitas')->get();
        return view('import.sections.barang-add', compact('new_ndpbm', 'dokumenData'));
    }

    public function store(Request $request)
    {
        ImportBarang::create([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'hs' => $request->input('hs'),
            'pernyataan_lartas' => $request->input('pernyataan_lartas'),
            'kode_barang' => $request->input('kode_barang'),
            'uraian' => $request->input('uraian'),
            'spesifikasi_lain' => $request->input('spesifikasi_lain'),
            'kode_negara_asal' => $request->input('kode_negara_asal'),
            'netto' => $request->input('netto'),
            'jumlah_satuan' => $request->input('jumlah_satuan'),
            'kode_satuan' => $request->input('kode_satuan'),
            'jumlah_kemasan' => $request->input('jumlah_kemasan'),
            'kode_kemasan' => $request->input('kode_kemasan'),
            'cif' => $request->input('cif'),
            'cif_rupiah' => $request->input('cif_rupiah'),
            'fob' => $request->input('fob'),
            'harga_satuan' => $request->input('harga_satuan'),
            'freight' => $request->input('freight'),
            'asuransi' => $request->input('asuransi'),
        ]);
        ImportBarangVd::create([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_vd' => $request->input('kodeJenisVd'),
            'nilai_barang' => 0,            
        ]);
        ImportBarangTarif::create([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('bm')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => $request->input('cif_rupiah')*($request->input('bm')/100),
            'kode_pungutan' => 'BM',
        ]);
        ImportBarangTarif::create([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('ppn')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => $request->input('cif_rupiah')*($request->input('ppn')/100),
            'kode_pungutan' => 'PPN',
        ]);
        ImportBarangTarif::create([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('pph')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => $request->input('cif_rupiah')*($request->input('pph')/100),
            'kode_pungutan' => 'PPH',
        ]);
        if($request->input('dok_fasilitas')!=null)
            {
            ImportBarangDokumen::create([
                'user_id' => 1,
                'seri_barang' => $request->input('seri_barang'),
                'seri_dokumen' => $request->input('dok_fasilitas'),
            ]);
            }
        return redirect()->route('import.create', ['step' => 'barang'])
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = ImportBarang::where('id', $id)->firstOrFail();

        return view('import.sections.barang-edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $barang = ImportBarang::where('id', $id)->firstOrFail();
        $barang->update([
            'seri_barang' => $request->input('seri_barang'),
            'hs' => $request->input('hs'),
            'pernyataan_lartas' => $request->input('pernyataan_lartas'),
            'kode_barang' => $request->input('kode_barang'),
            'uraian' => $request->input('uraian'),
            'spesifikasi_lain' => $request->input('spesifikasi_lain'),
            'kode_negara_asal' => $request->input('kode_negara_asal'),
            'netto' => $request->input('netto'),
            'jumlah_satuan' => $request->input('jumlah_satuan'),
            'kode_satuan' => $request->input('kode_satuan'),
            'jumlah_kemasan' => $request->input('jumlah_kemasan'),
            'kode_kemasan' => $request->input('kode_kemasan'),
            'cif' => $request->input('cif'),
            'cif_rupiah' => $request->input('cif_rupiah'),
            'fob' => $request->input('fob'),
            'harga_satuan' => $request->input('harga_satuan'),
            'freight' => $request->input('freight'),
            'asuransi' => $request->input('asuransi'),
        ]);

        return redirect()->back()
            ->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(ImportBarang $barang)
    {
        $barang->delete();

        return redirect()->back()->with('success', 'Barang berhasil dihapus');
    }
}
