<?php

namespace App\Http\Controllers;

use App\Models\ImportPetiKemas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportPetiKemasController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'seri' => 'required|integer|min:1',
            'nomor' => 'required|string|regex:/^[A-Z]{4}\d{7}$/',
            'ukuran' => 'required|string',
            'jenis_muatan' => 'required|string',
            'tipe' => 'required|string',
        ]);

        ImportPetiKemas::create([
            'user_id' => Auth::id(),
            'seri' => $request->seri,
            'nomor' => strtoupper($request->nomor),
            'ukuran' => $request->ukuran,
            'jenis_muatan' => $request->jenis_muatan,
            'tipe' => $request->tipe,
        ]);

        return redirect()->back()->with('success', 'Peti Kemas berhasil ditambahkan');
    }

    public function edit($id)
    {
        $petikemas = ImportPetiKemas::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        return view('import.sections.petikemas-edit', compact('petikemas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'seri' => 'required|integer|min:1',
            'nomor' => 'required|string|regex:/^[A-Z]{4}\d{7}$/',
            'ukuran' => 'required|string',
            'jenis_muatan' => 'required|string',
            'tipe' => 'required|string',
        ]);

        $petikemas = ImportPetiKemas::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $petikemas->update([
            'seri' => $request->seri,
            'nomor' => strtoupper($request->nomor),
            'ukuran' => $request->ukuran,
            'jenis_muatan' => $request->jenis_muatan,
            'tipe' => $request->tipe,
        ]);

        return redirect()->route('import.kemasan.index')->with('success', 'Peti Kemas berhasil diperbarui');
    }

    public function destroy($id)
    {
        $petikemas = ImportPetiKemas::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $petikemas->delete();

        return redirect()->back()->with('success', 'Peti Kemas berhasil dihapus');
    }
}
