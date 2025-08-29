<?php

namespace App\Http\Controllers;

use App\Models\ImportHeader;
use App\Models\ImportNotification;
use Illuminate\Http\Request;
use App\Models\ImportDokumen;
use Illuminate\Support\Facades\Auth;

class ImportNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $importNotifications = ImportNotification::all()->paginate(10);
        return view('import.index', compact('importNotifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dokument= ImportDokumen::where('import_notification_id', null)
            ->get();
        return view('import.create', compact('dokument'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $steps = ['header', 'entitas', 'dokumen', 'pengangkut', 'kemasan', 'transaksi', 'barang', 'pungutan', 'pernyataan'];
        $currentStep = $request->input('current_step', 'header');
        $action = $request->input('action', 'submit');

    // Get existing draft data from session
    $draft = session('import_draft', []);
        // Update draft with current step data
        foreach ($steps as $step) {
            if ($request->has($step)) {
                $draft[$step] = $request->input($step);
            }
        }

        // Store updated draft in session
        session(['import_draft' => $draft]);

        if ($action === 'save_continue') {
            // Redirect to next step
            $currentIndex = array_search($currentStep, $steps, true);
            $nextStep = $currentIndex < count($steps) - 1 ? $steps[$currentIndex + 1] : $steps[0];
            
            if ($currentStep === 'header' && ! empty($draft['header'])) {
                    $hd = $draft['header'];
                    $payload = [
                        'user_id' => 1,
                        'import_notification_id' => null,
                        'nomor_aju' => $hd['nomor_aju'] ?? null,
                        'kantor_pabean' => $hd['kantor_pabean'] ?? null,
                        'jenis_pib' => $hd['jenis_pib'] ?? null,
                        'jenis_impor' => $hd['jenis_impor'] ?? null,
                        'cara_pembayaran' => $hd['cara_pembayaran'] ?? null,
                    ];

                    if (empty($payload['nomor_aju']) && ! empty($payload['kantor_pabean'])) {
                        $year = date('Y');
                        $rand = substr((string) (hrtime(true) % 100000000), -8);
                        $payload['nomor_aju'] = "{$payload['kantor_pabean']}-{$year}-{$rand}";
                    }

                    // Reuse an existing draft header for same nomor Aju
                    $existing = ImportHeader::where('nomor_aju', $payload['nomor_aju'])
                        ->whereNull('import_notification_id')
                        ->first();
                    
                    if ($existing) {
                        $existing->update($payload);
                        $generatedNomor = $existing->nomor_aju;
                    } else {
                        $created = ImportHeader::create($payload);
                        $generatedNomor = $created->nomor_aju;
                    }

                    // Persist generated nomor_aju into session draft so view shows it immediately
                    $draft['header']['nomor_aju'] = $generatedNomor ?? ($payload['nomor_aju'] ?? null);
                    session(['import_draft' => $draft]);
                }
            // Handle entitas step saving: create or update parties and entitas
            if ($currentStep === 'entitas') {
                // Start with existing draft entitas if any
                $et = $draft['entitas'] ?? [];

                // If the form submitted entitas-related groups (the view posts importir, pemusatan, pemilik, pengirim, penjual separately)
                if ($request->has('importir') || $request->has('pemusatan') || $request->has('pemilik') || $request->has('pengirim') || $request->has('penjual')) {
                    $inputEnt = [
                        'importir' => $request->input('importir', []),
                        'pemusatan' => $request->input('pemusatan', []),
                        'pemilik' => $request->input('pemilik', []),
                        'pengirim' => $request->input('pengirim', []),
                        'penjual' => $request->input('penjual', []),
                    ];

                    // Merge nested arrays into draft entitas
                    $et = array_merge($et, $inputEnt);

                    // Also populate flattened keys expected by the saving logic (compatibility)
                    $et['importir_npwp'] = $inputEnt['importir']['npwp'] ?? $et['importir_npwp'] ?? null;
                    $et['importir_nitku'] = $inputEnt['importir']['nitku'] ?? $et['importir_nitku'] ?? null;
                    $et['importir_api_nib'] = $inputEnt['importir']['api_nib'] ?? $et['importir_api_nib'] ?? null;
                    $et['importir_nama'] = $inputEnt['importir']['nama'] ?? $et['importir_nama'] ?? null;
                    $et['importir_alamat'] = $inputEnt['importir']['alamat'] ?? $et['importir_alamat'] ?? null;
                    $et['importir_status'] = $inputEnt['importir']['status'] ?? $et['importir_status'] ?? null;

                    $et['pemusatan_npwp'] = $inputEnt['pemusatan']['npwp'] ?? $et['pemusatan_npwp'] ?? null;
                    $et['pemusatan_nitku'] = $inputEnt['pemusatan']['nitku'] ?? $et['pemusatan_nitku'] ?? null;
                    $et['pemusatan_nama'] = $inputEnt['pemusatan']['nama'] ?? $et['pemusatan_nama'] ?? null;
                    $et['pemusatan_alamat'] = $inputEnt['pemusatan']['alamat'] ?? $et['pemusatan_alamat'] ?? null;

                    $et['pemilik_npwp'] = $inputEnt['pemilik']['npwp'] ?? $et['pemilik_npwp'] ?? null;
                    $et['pemilik_nitku'] = $inputEnt['pemilik']['nitku'] ?? $et['pemilik_nitku'] ?? null;
                    $et['pemilik_nama'] = $inputEnt['pemilik']['nama'] ?? $et['pemilik_nama'] ?? null;
                    $et['pemilik_alamat'] = $inputEnt['pemilik']['alamat'] ?? $et['pemilik_alamat'] ?? null;

                    $et['pengirim_party_id'] = $inputEnt['pengirim']['party_id'] ?? $et['pengirim_party_id'] ?? null;
                    $et['pengirim_alamat'] = $inputEnt['pengirim']['alamat'] ?? $et['pengirim_alamat'] ?? null;
                    $et['pengirim_negara'] = $inputEnt['pengirim']['negara'] ?? $et['pengirim_negara'] ?? null;

                    $et['penjual_party_id'] = $inputEnt['penjual']['party_id'] ?? $et['penjual_party_id'] ?? null;
                    $et['penjual_alamat'] = $inputEnt['penjual']['alamat'] ?? $et['penjual_alamat'] ?? null;
                    $et['penjual_negara'] = $inputEnt['penjual']['negara'] ?? $et['penjual_negara'] ?? null;

                    // Persist normalized entitas back into session draft immediately so view sees it
                    $draft['entitas'] = $et;
                    session(['import_draft' => $draft]);
                }
                // Pengirim party id: use selected party_id from the form (we don't create Party here)
                $pengirimId = $et['pengirim']['party_id'] ?? $et['pengirim_party_id'] ?? null;

                // Penjual party id: use selected party_id from the form (we don't create Party here)
                $penjualId = $et['penjual']['party_id'] ?? $et['penjual_party_id'] ?? null;

                // Prepare entitas payload
                $entitasPayload = [
                    'user_id' => Auth::id() ?? 1,
                    'import_notification_id' => null,
                    'importir_npwp' => $et['importir_npwp'] ?? null,
                    'importir_nitku' => $et['importir_nitku'] ?? null,
                    'importir_nama' => $et['importir_nama'] ?? $et['importir']['nama'] ?? null,
                    'importir_alamat' => $et['importir_alamat'] ?? $et['importir']['alamat'] ?? null,
                    'importir_api_nib' => $et['importir_api_nib'] ?? null,
                    'importir_status' => $et['importir_status'] ?? null,
                    'pemusatan_npwp' => $et['pemusatan_npwp'] ?? null,
                    'pemusatan_nitku' => $et['pemusatan_nitku'] ?? null,
                    'pemusatan_nama' => $et['pemusatan_nama'] ?? null,
                    'pemusatan_alamat' => $et['pemusatan_alamat'] ?? null,
                    'pemilik_npwp' => $et['pemilik_npwp'] ?? null,
                    'pemilik_nitku' => $et['pemilik_nitku'] ?? null,
                    'pemilik_nama' => $et['pemilik_nama'] ?? null,
                    'pemilik_alamat' => $et['pemilik_alamat'] ?? null,
                    'pengirim_party_id' => $pengirimId,
                    'pengirim_alamat' => $et['pengirim_alamat'] ?? $et['pengirim']['alamat'] ?? null,
                    'pengirim_negara' => $et['pengirim_negara'] ?? $et['pengirim']['negara'] ?? null,
                    'penjual_party_id' => $penjualId,
                    'penjual_alamat' => $et['penjual_alamat'] ?? $et['penjual']['alamat'] ?? null,
                    'penjual_negara' => $et['penjual_negara'] ?? $et['penjual']['negara'] ?? null,
                ];

                // Try to reuse existing entitas for same importir_npwp and null import_notification_id
                $existingEntitas = null;
                if (! empty($entitasPayload['importir_npwp'])) {
                    $existingEntitas = \App\Models\ImportEntitas::where('importir_npwp', $entitasPayload['importir_npwp'])
                        ->whereNull('import_notification_id')
                        ->first();
                }
                if ($existingEntitas) {
                    $existingEntitas->update(array_filter($entitasPayload));
                    $savedEntitas = $existingEntitas;
                } else {
                    $savedEntitas = \App\Models\ImportEntitas::create($entitasPayload);
                }

                // Persist generated ids/values into session draft
                $draft['entitas'] = array_merge($draft['entitas'] ?? [], [
                    'pengirim_party_id' => $pengirimId,
                    'penjual_party_id' => $penjualId,
                    'id' => $savedEntitas->id,
                ]);
                session(['import_draft' => $draft]);
            }
            return redirect()->route('import.create', ['step' => $nextStep])
                ->with('success', ucfirst($currentStep).' data saved successfully. Please continue with the next step.');
        }
        

        if ($action === 'auto_save') {
            // Handle auto-save (AJAX request)
            return response()->json(['status' => 'saved']);
        }

        // Final submission - validate all required data
        $validated = $request->validate([
            'header' => 'required|array',
            'header.kantor_pabean' => 'required',
            'header.jenis_pib' => 'required',
            'header.jenis_impor' => 'required',
            'header.cara_pembayaran' => 'required',
            'entitas' => 'required|array',
            'entitas.importir' => 'required|array',
            'entitas.importir.nama' => 'required',
            'entitas.importir.alamat' => 'required',
            'dokumen' => 'required|array',
            'pengangkut' => 'required|array',
            'kemasan' => 'required|array',
            'transaksi' => 'required|array',
            'barang' => 'required|array',
            'pungutan' => 'required|array',
            'pernyataan' => 'required|array',
        ]);

        $validated['status'] = 'draft';

        // Create the import notification
        $import = ImportNotification::create($validated);
        
        // Clear the draft from session
        session()->forget('import_draft');

        return redirect()->route('import.index')->with('success', 'Import notification created successfully.');
    }

}
