<?php

namespace App\Http\Controllers;

use App\Models\ImportPungutan;
use Illuminate\Http\Request;

class ImportPungutanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'import_notification_id' => 'required|exists:import_notifications,id',
            'bm_percent' => 'nullable|numeric|min:0|max:100',
            'ppn_percent' => 'nullable|numeric|min:0|max:100',
            'pph_percent' => 'nullable|numeric|min:0|max:100',
            'bea_masuk' => 'nullable|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pph' => 'nullable|numeric|min:0',
            'total_pungutan' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = auth()->id();

        $pungutan = ImportPungutan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pungutan created successfully',
            'data' => $pungutan,
        ]);
    }
}
