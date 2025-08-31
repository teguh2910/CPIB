<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;

class PartyLookupController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pengirim,penjual',
            'q' => 'nullable|string|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $q = $request->input('q', '');
        $type = $request->string('type');

        $query = Party::where('type', $type)
            ->when($q, fn ($qr) => $qr->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%");
            }))
            ->orderBy('name');

        // simple pagination for Select2 (optionally)
        $perPage = 20;
        $page = max(1, (int) $request->input('page', 1));
        $results = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'results' => $results->map(function ($p) {
                return [
                    'id' => $p->id,
                    'text' => "{$p->name} ({$p->code})",
                    'code' => $p->code,
                    'address' => $p->address,
                    'country' => $p->country,
                ];
            }),
            'pagination' => ['more' => $results->count() === $perPage],
        ]);
    }

    public function show(int $id)
    {
        $p = Party::findOrFail($id);

        return response()->json([
            'id' => $p->id,
            'text' => "{$p->name} ({$p->code})",
            'code' => $p->code,
            'name' => $p->name,
            'address' => $p->address,
            'country' => $p->country,
            'type' => $p->type,
        ]);
    }
}
