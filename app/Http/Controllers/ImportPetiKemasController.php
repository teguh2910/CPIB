<?php

namespace App\Http\Controllers;

use App\Models\ImportPetiKemas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportPetiKemasController extends Controller
{
    public function store_petikemas(Request $request)
    {
        ImportPetiKemas::create([
            'user_id' => Auth::user()->id,
            'import_notification_id' => null,
            'seri' => $request->input('seri'),
            'nomor_kontainer' => $request->input('nomor'),
            'kode_ukuran_kontainer' => $request->input('ukuran'),
            'kode_jenis_kontainer' => $request->input('jenis_muatan'),
            'kode_tipe_kontainer' => $request->input('tipe'),
        ]);

        return redirect()->to(url()->previous())->with('success', 'Peti Kemas berhasil ditambahkan');
    }

    public function edit_petikemas($id)
    {
        $petikemas = ImportPetiKemas::findOrFail($id);

        return view('import.sections.petikemas-edit', compact('petikemas'));
    }

    public function update_petikemas(Request $request, $id)
    {
        $request->validate([
            'seri' => 'required|integer|min:1',
            'nomor_kontainer' => 'required|string|min:1',
            'kode_ukuran_kontainer' => 'required|string',
            'kode_jenis_kontainer' => 'required|string',
            'kode_tipe_kontainer' => 'required|string',
        ]);

        $petikemas = ImportPetiKemas::findOrFail($id);

        $petikemas->update([
            'seri' => $request->seri,
            'nomor_kontainer' => strtoupper($request->nomor_kontainer),
            'kode_ukuran_kontainer' => $request->kode_ukuran_kontainer,
            'kode_jenis_kontainer' => $request->kode_jenis_kontainer,
            'kode_tipe_kontainer' => $request->kode_tipe_kontainer,
        ]);

        return redirect()->to(url()->previous())
            ->with('success', 'Peti Kemas berhasil diperbarui');
    }

    public function destroy_petikemas($id)
    {
        $petikemas = ImportPetiKemas::findOrFail($id);
        $petikemas->delete();

        return redirect()->to(url()->previous())->with('success', 'Peti Kemas berhasil dihapus');
    }
}
