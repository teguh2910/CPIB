<?php

namespace App\Http\Controllers;

use App\Models\ImportDokumen;
use Illuminate\Http\Request;

class ImportDokumenController extends Controller
{
    public function store(Request $request)
    {
        ImportDokumen::create([
            'user_id' => 1,
            'import_notification_id' => null,
            'seri' => $request->seri,
            'kode_dokumen' => $request->kode_dokumen,
            'nomor_dokumen' => $request->nomor_dokumen,
            'tanggal_dokumen' => $request->tanggal_dokumen,
            'kode_fasilitas' => $request->kode_fasilitas,
            'no_aju' => session('nomor_aju'),
        ]);

        return redirect()->to(url()->previous())
            ->with('success', 'Dokumen berhasil disimpan');
    }

    public function edit($id)
    {
        $dokumen = ImportDokumen::findOrFail($id);

        return view('import.dokumen.edit', compact('dokumen'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'seri' => 'required|integer|min:1',
            'kode_dokumen' => 'required|string',
            'nomor_dokumen' => 'required|string|max:100',
            'tanggal_dokumen' => 'required|date',
            'kode_fasilitas' => 'nullable|string',
        ]);

        $dokumen = ImportDokumen::findOrFail($id);

        $dokumen->update([
            'seri' => $validated['seri'],
            'kode_dokumen' => $validated['kode_dokumen'],
            'nomor_dokumen' => $validated['nomor_dokumen'],
            'tanggal_dokumen' => $validated['tanggal_dokumen'],
            'kode_fasilitas' => $validated['kode_fasilitas'],
        ]);

        return redirect()->to(url()->previous())
            ->with('success', 'Dokumen berhasil diperbarui');
    }

    public function destroy(ImportDokumen $dokumen)
    {

        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus');
    }
}
