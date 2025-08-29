<?php

namespace App\Http\Controllers;

use App\Models\ImportNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $importNotifications = ImportNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('import.index', compact('importNotifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('import.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'header' => 'required|array',
            'entitas' => 'required|array',
            'dokumen' => 'required|array',
            'pengangkut' => 'required|array',
            'kemasan' => 'required|array',
            'transaksi' => 'required|array',
            'barang' => 'required|array',
            'pungutan' => 'required|array',
            'pernyataan' => 'required|array',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'draft';

        ImportNotification::create($validated);

        return redirect()->route('import.index')->with('success', 'Import notification created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ImportNotification $importNotification)
    {
        // Ensure user can only view their own notifications
        if ($importNotification->user_id !== Auth::id()) {
            abort(403);
        }

        return view('import.show', compact('importNotification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ImportNotification $importNotification)
    {
        // Ensure user can only edit their own notifications
        if ($importNotification->user_id !== Auth::id()) {
            abort(403);
        }

        return view('import.edit', compact('importNotification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImportNotification $importNotification)
    {
        // Ensure user can only update their own notifications
        if ($importNotification->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'header' => 'required|array',
            'entitas' => 'required|array',
            'dokumen' => 'required|array',
            'pengangkut' => 'required|array',
            'kemasan' => 'required|array',
            'transaksi' => 'required|array',
            'barang' => 'required|array',
            'pungutan' => 'required|array',
            'pernyataan' => 'required|array',
            'status' => 'required|string',
        ]);

        $importNotification->update($validated);

        return redirect()->route('import.index')->with('success', 'Import notification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImportNotification $importNotification)
    {
        // Ensure user can only delete their own notifications
        if ($importNotification->user_id !== Auth::id()) {
            abort(403);
        }

        $importNotification->delete();

        return redirect()->route('import.index')->with('success', 'Import notification deleted successfully.');
    }
}
