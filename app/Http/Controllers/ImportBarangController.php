<?php

namespace App\Http\Controllers;

use App\Models\ImportBarang;
use App\Models\ImportBarangDokumen;
use App\Models\ImportBarangTarif;
use App\Models\ImportBarangVd;
use App\Models\ImportDokumen;
use App\Models\ImportPungutan;
use App\Models\ImportTransaksi;
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
        $new_ndpbm = $transaksiData->ndpbm;
        $dokumenData = ImportDokumen::where('import_notification_id', null)->whereNotNull('kode_fasilitas')->get();

        return view('import.sections.barang-add', compact('new_ndpbm', 'dokumenData'));
    }

    public function creates($id)
    {
        // Get NDPBM from transaksi data
        $transaksiData = ImportTransaksi::where('import_notification_id', $id)->first();
        $new_ndpbm = $transaksiData->ndpbm;
        $dokumenData = ImportDokumen::where('import_notification_id', $id)->whereNotNull('kode_fasilitas')->get();

        return view('import.sections.barang-add', compact('new_ndpbm', 'dokumenData'));
    }

    public function store(Request $request)
    {
        ImportBarang::create([
            'user_id' => Auth::user()->id ?? 1,
            'seri_barang' => $request->input('seri_barang'),
            'hs' => $request->input('hs'),
            'pernyataan_lartas' => $request->input('pernyataan_lartas'),
            'kode_barang' => $request->input('kode_barang'),
            'uraian' => $request->input('uraian'),
            'kode_kondisi_barang' => $request->input('spesifikasi_lain'),
            'kode_negara_asal' => $request->input('kode_negara_asal'),
            'netto' => $request->input('netto'),
            'jumlah_satuan' => $request->input('jumlah_satuan'),
            'kode_satuan' => $request->input('kode_satuan'),
            'jumlah_kemasan' => $request->input('jumlah_kemasan'),
            'kode_kemasan' => $request->input('kode_kemasan'),
            'cif' => $request->input('cif'),
            'ndpbm' => $request->input('ndpbm'),
            'cif_rupiah' => $request->input('cif_rupiah'),
            'fob' => $request->input('fob'),
            'harga_satuan' => $request->input('harga_satuan'),
            'freight' => $request->input('freight'),
            'asuransi' => $request->input('asuransi'),
            'no_aju' => session('nomor_aju'),
        ]);
        ImportBarangVd::create([
            'user_id' => Auth::user()->id ?? 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_vd' => $request->input('kodeJenisVd'),
            'nilai_barang' => 0,
            'no_aju' => session('nomor_aju'),
        ]);
        ImportBarangTarif::create([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('bm')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => $request->input('cif_rupiah') * ($request->input('bm') / 100),
            'kode_pungutan' => 'BM',
            'no_aju' => session('nomor_aju'),
        ]);
        $nilai_bm = $request->input('cif_rupiah') * ($request->input('bm') / 100);
        ImportBarangTarif::create([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('ppn')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => ($request->input('cif_rupiah') + $nilai_bm) * ($request->input('ppn') / 100),
            'kode_pungutan' => 'PPN',
            'no_aju' => session('nomor_aju'),
        ]);
        ImportBarangTarif::create([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('pph')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => ($request->input('cif_rupiah') + $nilai_bm) * ($request->input('pph') / 100),
            'kode_pungutan' => 'PPH',
            'no_aju' => session('nomor_aju'),
        ]);
        if ($request->input('dok_fasilitas') != null) {
            ImportBarangDokumen::create([
                'user_id' => 1,
                'seri_barang' => $request->input('seri_barang'),
                'seri_dokumen' => $request->input('dok_fasilitas'),
                'no_aju' => session('nomor_aju'),
            ]);
        }

        return redirect()->route('import.create', ['step' => 'barang'])
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = ImportBarang::where('id', $id)->firstOrFail();
        $vd = ImportBarangVd::where('no_aju', $barang->no_aju)->first();
        $barang_dokumen = ImportBarangDokumen::where('import_notification_id', $barang->import_notification_id)->first();
        $barang_dokumen2 = ImportBarangDokumen::whereNull('import_notification_id')->first();
        try{
            $dokumenData = ImportDokumen::where('seri', $barang_dokumen->seri_dokumen)->first() ?? ImportDokumen::where('seri', $barang_dokumen2->seri_dokumen)->first();
        }catch(\Exception $e){
            $dokumenData = ImportDokumen::all();
                        
        }
        $dokumen = ImportDokumen::all();
        $new_ndpbm = ImportTransaksi::where('import_notification_id', $barang->import_notification_id)->first();
        $new_ndpbm = $new_ndpbm->ndpbm;
        $barang_tarif_bm = ImportBarangTarif::where('import_notification_id', $barang->import_notification_id)->where('kode_pungutan', 'BM')->first();
        $barang_tarif_ppn = ImportBarangTarif::where('import_notification_id', $barang->import_notification_id)->where('kode_pungutan', 'PPN')->first();
        $barang_tarif_pph = ImportBarangTarif::where('import_notification_id', $barang->import_notification_id)->where('kode_pungutan', 'PPH')->first();

        return view('import.sections.barang-edit', compact('barang', 'vd', 'dokumenData', 'dokumen', 'new_ndpbm', 'barang_tarif_bm', 'barang_tarif_ppn', 'barang_tarif_pph'));

    }

    public function update(Request $request, $id)
    {
        $barang = ImportBarang::where('id', $id)->firstOrFail();
        $barang->update([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'hs' => $request->input('hs'),
            'pernyataan_lartas' => $request->input('pernyataan_lartas'),
            'kode_barang' => $request->input('kode_barang'),
            'uraian' => $request->input('uraian'),
            'kode_kondisi_barang' => $request->input('spesifikasi_lain'),
            'ndpbm' => $request->input('ndpbm'),
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
        $ImportBarangVd = ImportBarangVd::where('no_aju', $barang->no_aju)->first();
        $ImportBarangVd->update([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_vd' => $request->input('kodeJenisVd'),
            'nilai_barang' => 0,
        ]);
        $ImportBarangTarif = ImportBarangTarif::where('no_aju', $barang->no_aju)->where('kode_pungutan', 'BM')->first();
        $ImportBarangTarif->update([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('bm')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => $request->input('cif_rupiah') * ($request->input('bm') / 100),
            'kode_pungutan' => 'BM',
        ]);
        $nilai_bm = $request->input('cif_rupiah') * ($request->input('bm') / 100);
        $ImportBarangTarif = ImportBarangTarif::where('no_aju', $barang->no_aju)->where('kode_pungutan', 'PPN')->first();
        $ImportBarangTarif->update([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('ppn')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => ($request->input('cif_rupiah') + $nilai_bm) * ($request->input('ppn') / 100),
            'kode_pungutan' => 'PPN',
        ]);
        $ImportBarangTarif = ImportBarangTarif::where('no_aju', $barang->no_aju)->where('kode_pungutan', 'PPH')->first();
        $ImportBarangTarif->update([
            'user_id' => 1,
            'seri_barang' => $request->input('seri_barang'),
            'kode_tarif' => 1,
            'tarif' => ($request->input('pph')),
            'kode_fasilitas' => 1,
            'tarif_fasilitas' => 100,
            'nilai_bayar' => ($request->input('cif_rupiah') + $nilai_bm) * ($request->input('pph') / 100),
            'kode_pungutan' => 'PPH',
        ]);
        if ($request->input('dok_fasilitas') != null) {
            $ImportBarangDokumen = ImportBarangDokumen::where('no_aju', $barang->no_aju)->first();
            $ImportBarangDokumen->update([
                'user_id' => 1,
                'seri_barang' => $request->input('seri_barang'),
                'seri_dokumen' => $request->input('dok_fasilitas'),
            ]);
        }
        $bm = ImportBarangTarif::where('no_aju', $barang->no_aju)->where('kode_pungutan', 'BM')->sum('nilai_bayar');
        $ppn = ImportBarangTarif::where('no_aju', $barang->no_aju)->where('kode_pungutan', 'PPN')->sum('nilai_bayar');
        $pph = ImportBarangTarif::where('no_aju', $barang->no_aju)->where('kode_pungutan', 'PPH')->sum('nilai_bayar');
        ImportPungutan::where('no_aju', $barang->no_aju)->update([
            'bea_masuk' => $bm,
            'ppn' => $ppn,
            'pph' => $pph,
            'total_pungutan' => $bm + $ppn + $pph,
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
