<?php

namespace App\Http\Controllers;

use App\Models\ImportKemasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportKemasanController extends Controller
{
    public function store_kemasan(Request $request)
    {
        $validated = $request->validate([
            'seri' => 'required|integer|min:1',
            'jumlah' => 'required|numeric|min:0.0001',
            'jenis_kemasan' => 'required|string',
            'merek' => 'nullable|string|max:100',
        ]);
        ImportKemasan::create([
            'user_id' => Auth::id() ?? 1,
            'import_notification_id' => null,
            'seri' => $validated['seri'],
            'jumlah' => $validated['jumlah'],
            'jenis_kemasan' => $validated['jenis_kemasan'],
            'merek' => $validated['merek'],
        ]);

        return redirect()->route('import.create', ['step' => 'kemasan'])->with('success', 'Kemasan berhasil ditambahkan');
    }

    public function edit_kemasan($id)
    {
        $kemasan = ImportKemasan::findOrFail($id);

        return view('import.kemasan.edit', compact('kemasan'));
    }

    public function update_kemasan(Request $request, $id)
    {
        $validated = $request->validate([
            'seri' => 'required|integer|min:1',
            'jumlah' => 'required|numeric|min:0.0001',
            'jenis_kemasan' => 'required|string',
            'merek' => 'nullable|string|max:100',
        ]);

        $kemasan = ImportKemasan::where('import_notification_id', null)->findOrFail($id);

        $kemasan->update([
            'seri' => $validated['seri'],
            'jumlah' => $validated['jumlah'],
            'jenis_kemasan' => $validated['jenis_kemasan'],
            'merek' => $validated['merek'],
        ]);

        return redirect()->route('import.create', ['step' => 'kemasan'])
            ->with('success', 'Kemasan berhasil diperbarui');
    }

    public function destroy_kemasan(ImportKemasan $kemasan)
    {
        $kemasan->delete();

        return redirect()->route('import.create', ['step' => 'kemasan'])->with('success', 'Kemasan berhasil dihapus');
    }
}
