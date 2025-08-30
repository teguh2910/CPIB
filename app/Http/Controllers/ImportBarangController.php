<?php

namespace App\Http\Controllers;

use App\Models\ImportBarang;
use App\Models\ImportDokumen;
use App\Models\ImportTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
        // Get dokumen data for dropdown
        $dokumenData = ImportDokumen::whereNull('import_notification_id')->get();
        $dokument = $dokumenData->map(function ($d) {
            $jenis = config('import.jenis_dokumen')[$d->jenis] ?? $d->jenis;
            $no = $d->nomor ?? '';

            return trim("$jenis - No $no");
        })->values()->all();

        return view('import.sections.barang-add', compact('dokument'));
    }

    public function store(Request $request)
    {

        $transaksiData = ImportTransaksi::whereNull('import_notification_id')->first();
        $ndpbm = $transaksiData->harga_ndpbm;
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
            'jumlah_kemasan' => 'nullable|numeric|min:0',
            'jenis_kemasan' => 'nullable|string',
            'nilai_barang' => 'required|numeric|min:0',
            'fob' => 'nullable|numeric|min:0',
            'freight' => 'nullable|numeric|min:0',
            'asuransi' => 'nullable|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'dokumen_fasilitas' => 'nullable|string|max:150',
            'tarif_bm' => 'nullable|numeric|min:0',
            'status_bm' => 'nullable|string',
            'ket_bm' => 'nullable|numeric|min:0',
            'tarif_ppn' => 'nullable|numeric',
            'status_ppn' => 'nullable|string',
            'ket_ppn' => 'nullable|numeric|min:0',
            'tarif_pph' => 'nullable|numeric',
            'status_pph' => 'nullable|string',
            'ket_pph' => 'nullable|numeric',
        ]);

        ImportBarang::create([
            'user_id' => 1,
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
            'jml_kemasan' => $validated['jumlah_kemasan'],
            'jenis_kemasan' => $validated['jenis_kemasan'],
            'nilai_barang' => $validated['nilai_barang'],
            'fob' => $validated['fob'],
            'freight' => $validated['freight'],
            'asuransi' => $validated['asuransi'],
            'harga_satuan' => $validated['harga_satuan'],
            'nilai_pabean_rp' => $validated['nilai_barang']*$ndpbm,
            'dokumen_fasilitas' => $validated['dokumen_fasilitas'],
            'ket_bm' => $validated['ket_bm'],
            'tarif_bm' => $validated['tarif_bm'],
            'bayar_bm' => $validated['status_bm'],
            'ppn_tarif' => $validated['tarif_ppn'],
            'ket_ppn' => $validated['ket_ppn'],
            'bayar_ppn' => $validated['status_ppn'],
            'ket_pph' => $validated['ket_pph'],
            'tarif_pph' => $validated['tarif_pph'],
            'bayar_pph' => $validated['status_pph'],
            'biaya_bm' => $validated['tarif_bm']/100*$validated['nilai_barang']*$ndpbm,
            'biaya_ppn' => $validated['tarif_ppn']/100*$validated['nilai_barang']*$ndpbm,
            'biaya_pph' => $validated['tarif_pph']/100*$validated['nilai_barang']*$ndpbm,
        ]);

        return redirect()->route('import.create', ['step' => 'barang'])
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = ImportBarang::where('id', $id)->firstOrFail();
        // Get dokumen data for dropdown
        $dokumenData = ImportDokumen::whereNull('import_notification_id')->get();
        $dokument = $dokumenData->map(function ($d) {
            $jenis = config('import.jenis_dokumen')[$d->jenis] ?? $d->jenis;
            $no = $d->nomor ?? '';

            return trim("$jenis - No $no");
        })->values()->all();

        return view('import.sections.barang-edit', compact('barang', 'dokument'));
    }

    public function update(Request $request, $id)
    {
        $transaksiData = ImportTransaksi::whereNull('import_notification_id')->first();
        $ndpbm = $transaksiData->harga_ndpbm;
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
            'dokumen_fasilitas' => 'nullable|string|max:150',
            'tarif_bm' => 'nullable|numeric|min:0',
            'status_bm' => 'nullable|string',
            'ket_bm' => 'nullable|numeric|min:0',
            'tarif_ppn' => 'nullable|numeric',
            'status_ppn' => 'nullable|string',
            'ket_ppn' => 'nullable|numeric|min:0',
            'tarif_pph' => 'nullable|numeric',
            'status_pph' => 'nullable|string',
            'ket_pph' => 'nullable|numeric',
        ]);

        $barang = ImportBarang::where('id', $id)->firstOrFail();
        $barang->update([
            'user_id' => 1,
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
            'nilai_pabean_rp' => $validated['nilai_barang']*$ndpbm,
            'dokumen_fasilitas' => $validated['dokumen_fasilitas'],
            'ket_bm' => $validated['ket_bm'],
            'tarif_bm' => $validated['tarif_bm'],
            'bayar_bm' => $validated['status_bm'],
            'ppn_tarif' => $validated['tarif_ppn'],
            'ket_ppn' => $validated['ket_ppn'],
            'bayar_ppn' => $validated['status_ppn'],
            'ket_ppn' => $validated['ket_ppn'],
            'tarif_pph' => $validated['tarif_pph'],
            'bayar_pph' => $validated['status_pph'],
            'biaya_bm' => $validated['tarif_bm']/100*$validated['nilai_barang']*$ndpbm,
            'biaya_ppn' => $validated['tarif_ppn']/100*$validated['nilai_barang']*$ndpbm,
            'biaya_pph' => $validated['tarif_pph']/100*$validated['nilai_barang']*$ndpbm,
        ]);

        return redirect()->back()
            ->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(ImportBarang $barang)
    {
        $barang->delete();

        return redirect()->back()->with('success', 'Barang berhasil dihapus');
    }

    /**
     * Return a downloadable CSV template for barang import.
     */
    public function templateCsv()
    {
        $columns = [
            'seri','pos_tarif','lartas','kode_barang','uraian','spesifikasi','kondisi','negara_asal','berat_bersih','jumlah','satuan','jml_kemasan','jenis_kemasan','nilai_barang','fob','freight','asuransi','harga_satuan','dokumen_fasilitas',
            // BM
            'ket_bm','tarif_bm','bayar_bm',
            // PPN
            'ppn_tarif','ket_ppn','bayar_ppn',
            // PPh
            'ket_pph','tarif_pph','bayar_pph',
            'nilai_pabean_rp',
            'NDPMB',
        ];

        $filename = 'barang-template.csv';
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $columns);
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    

}
