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
            'jumlah_kemasan' => 'required|numeric|min:0.0001',
            'kode_kemasan' => 'required|string',
            'merek' => 'nullable|string|max:100',
        ]);
        ImportKemasan::create([
            'user_id' => Auth::user()->id ?? 1,
            'import_notification_id' => null,
            'seri' => $validated['seri'],
            'jumlah_kemasan' => $validated['jumlah_kemasan'],
            'kode_kemasan' => $validated['kode_kemasan'],
            'merek' => $validated['merek'],
            'no_aju' => session('nomor_aju'),
        ]);

        return redirect()->to(url()->previous())
            ->with('success', 'Kemasan berhasil ditambahkan');
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
            'jumlah_kemasan' => 'required|numeric|min:0.0001',
            'kode_kemasan' => 'required|string',
            'merek' => 'nullable|string|max:100',
        ]);

        $kemasan = ImportKemasan::findOrFail($id);

        $kemasan->update([
            'seri' => $validated['seri'],
            'jumlah_kemasan' => $validated['jumlah_kemasan'],
            'kode_kemasan' => $validated['kode_kemasan'],
            'merek' => $validated['merek'],
        ]);

        return redirect()->to(url()->previous())
            ->with('success', 'Kemasan berhasil diperbarui');
    }

    public function destroy_kemasan(ImportKemasan $kemasan)
    {
        $kemasan->delete();

        return redirect()->route('import.create', ['step' => 'kemasan'])->with('success', 'Kemasan berhasil dihapus');
    }
}
