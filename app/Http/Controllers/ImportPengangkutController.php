<?php

namespace App\Http\Controllers;

use App\Models\ImportPengangkut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportPengangkutController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            // BC 1.1
            'bc11_no_tutup_pu' => 'nullable|string|max:50',
            'bc11_pos_1' => 'nullable|integer|min:0',
            'bc11_pos_2' => 'nullable|integer|min:0',
            'bc11_pos_3' => 'nullable|integer|min:0',

            // Pengangkutan
            'angkut_cara' => 'required|string',
            'angkut_nama' => 'nullable|string|max:120',
            'angkut_voy' => 'nullable|string|max:50',
            'angkut_bendera' => 'nullable|string',
            'angkut_eta' => 'nullable|date',

            // Pelabuhan
            'pelabuhan_muat' => 'nullable|string',
            'pelabuhan_transit' => 'nullable|string',
            'pelabuhan_tujuan' => 'nullable|string',
            'tps_kode' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $pengangkut = ImportPengangkut::create($validated);

        return redirect()->back()->with('success', 'Data pengangkut berhasil disimpan');
    }

    public function update(Request $request, ImportPengangkut $pengangkut)
    {
        // Ensure user owns this pengangkut
        if ($pengangkut->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            // BC 1.1
            'bc11_no_tutup_pu' => 'nullable|string|max:50',
            'bc11_pos_1' => 'nullable|integer|min:0',
            'bc11_pos_2' => 'nullable|integer|min:0',
            'bc11_pos_3' => 'nullable|integer|min:0',

            // Pengangkutan
            'angkut_cara' => 'required|string',
            'angkut_nama' => 'nullable|string|max:120',
            'angkut_voy' => 'nullable|string|max:50',
            'angkut_bendera' => 'nullable|string',
            'angkut_eta' => 'nullable|date',

            // Pelabuhan
            'pelabuhan_muat' => 'nullable|string',
            'pelabuhan_transit' => 'nullable|string',
            'pelabuhan_tujuan' => 'nullable|string',
            'tps_kode' => 'nullable|string',
        ]);

        $pengangkut->update($validated);

        return redirect()->back()->with('success', 'Data pengangkut berhasil diperbarui');
    }
}
