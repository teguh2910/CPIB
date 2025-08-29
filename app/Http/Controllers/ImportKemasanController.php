<?php

namespace App\Http\Controllers;

use App\Models\ImportKemasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportKemasanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kemasan' => 'required|array',
            'kemasan.*.seri' => 'required|integer|min:1',
            'kemasan.*.jumlah' => 'required|numeric|min:0.0001',
            'kemasan.*.jenis' => 'required|string',
            'kemasan.*.merek' => 'nullable|string|max:100',

            'petikemas' => 'required|array',
            'petikemas.*.seri' => 'required|integer|min:1',
            'petikemas.*.nomor' => 'required|string|max:20',
            'petikemas.*.ukuran' => 'required|string',
            'petikemas.*.jenis_muatan' => 'required|string',
            'petikemas.*.tipe' => 'required|string',
        ]);

        $userId = Auth::id();
        $savedKemasan = 0;
        $savedPetikemas = 0;

        // Save kemasan
        foreach ($validated['kemasan'] as $kemasanData) {
            ImportKemasan::create([
                'user_id' => $userId,
                'type' => 'kemasan',
                'seri' => $kemasanData['seri'],
                'jumlah' => $kemasanData['jumlah'],
                'jenis_kemasan' => $kemasanData['jenis'],
                'merek' => $kemasanData['merek'] ?? null,
            ]);
            $savedKemasan++;
        }

        // Save peti kemas
        foreach ($validated['petikemas'] as $petikemasData) {
            ImportKemasan::create([
                'user_id' => $userId,
                'type' => 'petikemas',
                'seri' => $petikemasData['seri'],
                'nomor' => $petikemasData['nomor'],
                'ukuran' => $petikemasData['ukuran'],
                'jenis_muatan' => $petikemasData['jenis_muatan'],
                'tipe' => $petikemasData['tipe'],
            ]);
            $savedPetikemas++;
        }

        return redirect()->back()->with('success', "{$savedKemasan} kemasan dan {$savedPetikemas} peti kemas berhasil disimpan");
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'kemasan' => 'required|array',
            'kemasan.*.id' => 'nullable|exists:import_kemasans,id',
            'kemasan.*.seri' => 'required|integer|min:1',
            'kemasan.*.jumlah' => 'required|numeric|min:0.0001',
            'kemasan.*.jenis' => 'required|string',
            'kemasan.*.merek' => 'nullable|string|max:100',

            'petikemas' => 'required|array',
            'petikemas.*.id' => 'nullable|exists:import_kemasans,id',
            'petikemas.*.seri' => 'required|integer|min:1',
            'petikemas.*.nomor' => 'required|string|max:20',
            'petikemas.*.ukuran' => 'required|string',
            'petikemas.*.jenis_muatan' => 'required|string',
            'petikemas.*.tipe' => 'required|string',
        ]);

        $userId = Auth::id();
        $savedKemasan = 0;
        $updatedKemasan = 0;
        $savedPetikemas = 0;
        $updatedPetikemas = 0;

        // Update kemasan
        foreach ($validated['kemasan'] as $kemasanData) {
            if (isset($kemasanData['id'])) {
                $kemasan = ImportKemasan::where('user_id', $userId)->findOrFail($kemasanData['id']);
                $kemasan->update([
                    'seri' => $kemasanData['seri'],
                    'jumlah' => $kemasanData['jumlah'],
                    'jenis_kemasan' => $kemasanData['jenis'],
                    'merek' => $kemasanData['merek'] ?? null,
                ]);
                $updatedKemasan++;
            } else {
                ImportKemasan::create([
                    'user_id' => $userId,
                    'type' => 'kemasan',
                    'seri' => $kemasanData['seri'],
                    'jumlah' => $kemasanData['jumlah'],
                    'jenis_kemasan' => $kemasanData['jenis'],
                    'merek' => $kemasanData['merek'] ?? null,
                ]);
                $savedKemasan++;
            }
        }

        // Update peti kemas
        foreach ($validated['petikemas'] as $petikemasData) {
            if (isset($petikemasData['id'])) {
                $petikemas = ImportKemasan::where('user_id', $userId)->findOrFail($petikemasData['id']);
                $petikemas->update([
                    'seri' => $petikemasData['seri'],
                    'nomor' => $petikemasData['nomor'],
                    'ukuran' => $petikemasData['ukuran'],
                    'jenis_muatan' => $petikemasData['jenis_muatan'],
                    'tipe' => $petikemasData['tipe'],
                ]);
                $updatedPetikemas++;
            } else {
                ImportKemasan::create([
                    'user_id' => $userId,
                    'type' => 'petikemas',
                    'seri' => $petikemasData['seri'],
                    'nomor' => $petikemasData['nomor'],
                    'ukuran' => $petikemasData['ukuran'],
                    'jenis_muatan' => $petikemasData['jenis_muatan'],
                    'tipe' => $petikemasData['tipe'],
                ]);
                $savedPetikemas++;
            }
        }

        $message = '';
        if ($savedKemasan > 0) {
            $message .= "{$savedKemasan} kemasan baru disimpan. ";
        }
        if ($updatedKemasan > 0) {
            $message .= "{$updatedKemasan} kemasan diperbarui. ";
        }
        if ($savedPetikemas > 0) {
            $message .= "{$savedPetikemas} peti kemas baru disimpan. ";
        }
        if ($updatedPetikemas > 0) {
            $message .= "{$updatedPetikemas} peti kemas diperbarui. ";
        }

        return redirect()->back()->with('success', trim($message));
    }

    public function destroy(ImportKemasan $kemasan)
    {
        // Ensure user owns this kemasan
        if ($kemasan->user_id !== Auth::id()) {
            abort(403);
        }

        $type = $kemasan->type;
        $kemasan->delete();

        return redirect()->back()->with('success', ucfirst($type).' berhasil dihapus');
    }
}
