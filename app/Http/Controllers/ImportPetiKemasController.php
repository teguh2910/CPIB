<?php

namespace App\Http\Controllers;

use App\Models\ImportPetiKemas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportPetiKemasController extends Controller
{
    public function store_petikemas(Request $request)
    {
        $request->validate([
            'seri' => 'required|integer|min:1',
            'nomor' => 'required|string|min:1',
            'ukuran' => 'required|string',
            'jenis_muatan' => 'required|string',
            'tipe' => 'required|string',
        ]);

        ImportPetiKemas::create([
            'user_id' => 1,
            'import_notification_id' => null, // Will be set when import notification is finalized
            'seri' => $request->seri,
            'nomor' => strtoupper($request->nomor),
            'ukuran' => $request->ukuran,
            'jenis_muatan' => $request->jenis_muatan,
            'tipe' => $request->tipe,
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
            'nomor' => 'required|string|min:1',
            'ukuran' => 'required|string',
            'jenis_muatan' => 'required|string',
            'tipe' => 'required|string',
        ]);

        $petikemas = ImportPetiKemas::findOrFail($id);

        $petikemas->update([
            'seri' => $request->seri,
            'nomor' => strtoupper($request->nomor),
            'ukuran' => $request->ukuran,
            'jenis_muatan' => $request->jenis_muatan,
            'tipe' => $request->tipe,
        ]);

        return redirect()->to(url()->previous())
            ->with('success', 'Peti Kemas berhasil diperbarui');
    }

    public function destroy_petikemas($id)
    {
        $petikemas = ImportPetiKemas::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $petikemas->delete();

        return redirect()->to(url()->previous())->with('success', 'Peti Kemas berhasil dihapus');
    }
}
