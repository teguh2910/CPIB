<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $q     = trim($request->get('q', ''));
        $type  = $request->get('type', '');
        $query = Party::query()
            ->when($type, fn($qr) => $qr->where('type', $type))
            ->when($q, function ($qr) use ($q) {
                $qr->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('code','like',"%$q%")
                      ->orWhere('address','like',"%$q%");
                });
            })
            ->orderBy('type')->orderBy('name');

        $parties = $query->paginate(12)->appends($request->query());
        $countries = config('import.negara');

        return view('parties.index', compact('parties','q','type','countries'));
    }

    public function create()
    {
        $countries = config('import.negara');
        return view('parties.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'    => ['required','in:pengirim,penjual'],
            'code'    => ['required','string','max:50','alpha_dash', Rule::unique('parties','code')],
            'name'    => ['required','string','max:150'],
            'address' => ['nullable','string','max:255'],
            'country' => ['nullable','string','size:2'],
        ]);

        Party::create($data);
        return redirect()->route('parties.index')->with('success', 'Master berhasil dibuat.');
    }

    public function edit(Party $party)
    {
        $countries = config('import.negara');
        return view('parties.edit', compact('party','countries'));
    }

    public function update(Request $request, Party $party)
    {
        $data = $request->validate([
            'type'    => ['required','in:pengirim,penjual'],
            'code'    => ['required','string','max:50','alpha_dash', Rule::unique('parties','code')->ignore($party->id)],
            'name'    => ['required','string','max:150'],
            'address' => ['nullable','string','max:255'],
            'country' => ['nullable','string','size:2'],
        ]);

        $party->update($data);
        return redirect()->route('parties.index')->with('success', 'Master diperbarui.');
    }

    public function destroy(Party $party)
    {
        $party->delete();
        return redirect()->route('parties.index')->with('success', 'Master dihapus.');
    }
}
