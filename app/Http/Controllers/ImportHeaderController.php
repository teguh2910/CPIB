<?php

namespace App\Http\Controllers;

use App\Models\ImportHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportHeaderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kantor_pabean' => 'required|string',
            'jenis_pib' => 'required|string',
            'jenis_impor' => 'required|string',
            'cara_pembayaran' => 'required|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['nomor_aju'] = $this->generateNomorAju($validated['kantor_pabean']);

        $header = ImportHeader::create($validated);

        return redirect()->back()->with('success', 'Header berhasil disimpan dengan Nomor AJU: '.$header->nomor_aju);
    }

    public function update(Request $request, ImportHeader $header)
    {
        // Ensure user owns this header
        if ($header->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'kantor_pabean' => 'required|string',
            'jenis_pib' => 'required|string',
            'jenis_impor' => 'required|string',
            'cara_pembayaran' => 'required|string',
        ]);

        $header->update($validated);

        return redirect()->back()->with('success', 'Header berhasil diperbarui');
    }

    public function destroy(ImportHeader $header)
    {
        // Ensure user owns this header
        if ($header->user_id !== Auth::id()) {
            abort(403);
        }

        $header->delete();

        return redirect()->back()->with('success', 'Header berhasil dihapus');
    }

    private function generateNomorAju(string $kp): string
    {
        $year = date('Y');
        $rand = substr((string) (hrtime(true) % 100000000), -8);

        return "{$kp}-{$year}-{$rand}";
    }
}
