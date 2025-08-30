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
            'jenis' => $request->jenis,
            'nomor' => $request->nomor,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->to('import/create?step=dokumen');
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
            'jenis' => 'required|string',
            'nomor' => 'required|string|max:100',
            'tanggal' => 'required|date',
        ]);

        $dokumen = ImportDokumen::findOrFail($id);

        $dokumen->update([
            'seri' => $validated['seri'],
            'jenis' => $validated['jenis'],
            'nomor' => $validated['nomor'],
            'tanggal' => $validated['tanggal'],
        ]);

        return redirect()->route('import.create', ['step' => 'dokumen'])
            ->with('success', 'Dokumen berhasil diperbarui');
    }

    public function destroy(ImportDokumen $dokumen)
    {

        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus');
    }
}
