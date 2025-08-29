<?php

namespace App\Http\Controllers;

use App\Models\ImportEntitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportEntitasController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Importir
            'importir_npwp' => 'required|string|max:32',
            'importir_nitku' => 'nullable|string|max:32',
            'importir_nama' => 'required|string|max:150',
            'importir_alamat' => 'required|string|max:255',
            'importir_api_nib' => 'nullable|string|max:50',
            'importir_status' => 'required|string',

            // Pemusatan (optional)
            'pemusatan_npwp' => 'nullable|string|max:32',
            'pemusatan_nitku' => 'nullable|string|max:32',
            'pemusatan_nama' => 'nullable|string|max:150',
            'pemusatan_alamat' => 'nullable|string|max:255',

            // Pemilik (optional)
            'pemilik_npwp' => 'nullable|string|max:32',
            'pemilik_nitku' => 'nullable|string|max:32',
            'pemilik_nama' => 'nullable|string|max:150',
            'pemilik_alamat' => 'nullable|string|max:255',

            // Pengirim
            'pengirim_party_id' => 'required|exists:parties,id',
            'pengirim_alamat' => 'required|string|max:255',
            'pengirim_negara' => 'required|string|size:2',

            // Penjual
            'penjual_party_id' => 'required|exists:parties,id',
            'penjual_alamat' => 'required|string|max:255',
            'penjual_negara' => 'required|string|size:2',
        ]);

        $validated['user_id'] = Auth::id();

        $entitas = ImportEntitas::create($validated);

        return redirect()->back()->with('success', 'Entitas berhasil disimpan');
    }

    public function update(Request $request, ImportEntitas $entitas)
    {
        // Ensure user owns this entitas
        if ($entitas->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            // Importir
            'importir_npwp' => 'required|string|max:32',
            'importir_nitku' => 'nullable|string|max:32',
            'importir_nama' => 'required|string|max:150',
            'importir_alamat' => 'required|string|max:255',
            'importir_api_nib' => 'nullable|string|max:50',
            'importir_status' => 'required|string',

            // Pemusatan (optional)
            'pemusatan_npwp' => 'nullable|string|max:32',
            'pemusatan_nitku' => 'nullable|string|max:32',
            'pemusatan_nama' => 'nullable|string|max:150',
            'pemusatan_alamat' => 'nullable|string|max:255',

            // Pemilik (optional)
            'pemilik_npwp' => 'nullable|string|max:32',
            'pemilik_nitku' => 'nullable|string|max:32',
            'pemilik_nama' => 'nullable|string|max:150',
            'pemilik_alamat' => 'nullable|string|max:255',

            // Pengirim
            'pengirim_party_id' => 'required|exists:parties,id',
            'pengirim_alamat' => 'required|string|max:255',
            'pengirim_negara' => 'required|string|size:2',

            // Penjual
            'penjual_party_id' => 'required|exists:parties,id',
            'penjual_alamat' => 'required|string|max:255',
            'penjual_negara' => 'required|string|size:2',
        ]);

        $entitas->update($validated);

        return redirect()->back()->with('success', 'Entitas berhasil diperbarui');
    }

    public function destroy(ImportEntitas $entitas)
    {
        // Ensure user owns this entitas
        if ($entitas->user_id !== Auth::id()) {
            abort(403);
        }

        $entitas->delete();

        return redirect()->back()->with('success', 'Entitas berhasil dihapus');
    }
}
