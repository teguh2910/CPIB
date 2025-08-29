<?php

namespace App\Http\Controllers;

use App\Models\ImportPernyataan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportPernyataanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'declared_by' => 'required|string|max:150',
            'jabatan' => 'nullable|string|max:100',
            'place_date' => 'required|string|max:150',
            'ttd_image_path' => 'nullable|string|max:255',
            'agree' => 'accepted',
        ]);

        $validated['user_id'] = Auth::id();

        $pernyataan = ImportPernyataan::create($validated);

        return redirect()->back()->with('success', 'Pernyataan berhasil disimpan');
    }

    public function update(Request $request, ImportPernyataan $pernyataan)
    {
        // Ensure user owns this pernyataan
        if ($pernyataan->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'declared_by' => 'required|string|max:150',
            'jabatan' => 'nullable|string|max:100',
            'place_date' => 'required|string|max:150',
            'ttd_image_path' => 'nullable|string|max:255',
            'agree' => 'accepted',
        ]);

        $pernyataan->update($validated);

        return redirect()->back()->with('success', 'Pernyataan berhasil diperbarui');
    }
}
