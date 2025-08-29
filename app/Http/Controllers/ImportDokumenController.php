<?php

namespace App\Http\Controllers;

use App\Models\ImportDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportDokumenController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dokumen' => 'required|array|min:1',
            'dokumen.*.seri' => 'required|integer|min:1',
            'dokumen.*.jenis' => 'required|string',
            'dokumen.*.nomor' => 'required|string|max:100',
            'dokumen.*.tanggal' => 'required|date',
        ]);

        $userId = Auth::id();
        $savedCount = 0;

        foreach ($validated['dokumen'] as $dokumenData) {
            ImportDokumen::create([
                'user_id' => $userId,
                'seri' => $dokumenData['seri'],
                'jenis' => $dokumenData['jenis'],
                'nomor' => $dokumenData['nomor'],
                'tanggal' => $dokumenData['tanggal'],
            ]);
            $savedCount++;
        }

        return redirect()->back()->with('success', "{$savedCount} dokumen berhasil disimpan");
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'dokumen' => 'required|array|min:1',
            'dokumen.*.id' => 'nullable|exists:import_dokumens,id',
            'dokumen.*.seri' => 'required|integer|min:1',
            'dokumen.*.jenis' => 'required|string',
            'dokumen.*.nomor' => 'required|string|max:100',
            'dokumen.*.tanggal' => 'required|date',
        ]);

        $userId = Auth::id();
        $savedCount = 0;
        $updatedCount = 0;

        foreach ($validated['dokumen'] as $dokumenData) {
            if (isset($dokumenData['id'])) {
                // Update existing
                $dokumen = ImportDokumen::where('user_id', $userId)->findOrFail($dokumenData['id']);
                $dokumen->update([
                    'seri' => $dokumenData['seri'],
                    'jenis' => $dokumenData['jenis'],
                    'nomor' => $dokumenData['nomor'],
                    'tanggal' => $dokumenData['tanggal'],
                ]);
                $updatedCount++;
            } else {
                // Create new
                ImportDokumen::create([
                    'user_id' => $userId,
                    'seri' => $dokumenData['seri'],
                    'jenis' => $dokumenData['jenis'],
                    'nomor' => $dokumenData['nomor'],
                    'tanggal' => $dokumenData['tanggal'],
                ]);
                $savedCount++;
            }
        }

        $message = '';
        if ($savedCount > 0) {
            $message .= "{$savedCount} dokumen baru disimpan. ";
        }
        if ($updatedCount > 0) {
            $message .= "{$updatedCount} dokumen diperbarui. ";
        }

        return redirect()->back()->with('success', trim($message));
    }

    public function destroy(ImportDokumen $dokumen)
    {
        // Ensure user owns this dokumen
        if ($dokumen->user_id !== Auth::id()) {
            abort(403);
        }

        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus');
    }
}
