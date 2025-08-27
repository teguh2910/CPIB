<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImportNotification;
use Illuminate\Validation\Rule;

class ImportNotificationWizardController extends Controller
{
    private array $steps = [
        'header','entitas','dokumen','pengangkut','kemasan','transaksi','barang','pungutan','pernyataan'
    ];

    private array $rules;

    public function __construct()
    {
        $this->rules = [
    'header' => [
            // nomor_aju tidak di-post dari form; kita generate otomatis
                'kantor_pabean'   => 'required|string|in:' . implode(',', array_keys(config('import.kantor_pabean'))),
                'jenis_pib'       => 'required|string|in:' . implode(',', array_keys(config('import.jenis_pib'))),
                'jenis_impor'     => 'required|string|in:' . implode(',', array_keys(config('import.jenis_impor'))),
                'cara_pembayaran' => 'required|string|in:' . implode(',', array_keys(config('import.cara_pembayaran'))),
            ],
    'entitas' => [
    // Importir
    'importir.npwp'    => 'required|string|max:32',
    'importir.nitku'   => 'nullable|string|max:32',
    'importir.nama'    => 'required|string|max:150',
    'importir.alamat'  => 'required|string|max:255',
    'importir.api_nib' => 'nullable|string|max:50',
    'importir.status'  => 'required|string|in:' . implode(',', array_keys(config('import.status_importir'))),

    // NPWP Pemusatan (opsional)
    'pemusatan.npwp'   => 'nullable|string|max:32',
    'pemusatan.nitku'  => 'nullable|string|max:32',
    'pemusatan.nama'   => 'nullable|string|max:150',
    'pemusatan.alamat' => 'nullable|string|max:255',

    // Pemilik Barang (opsional)
    'pemilik.npwp'   => 'nullable|string|max:32',
    'pemilik.nitku'  => 'nullable|string|max:32',
    'pemilik.nama'   => 'nullable|string|max:150',
    'pemilik.alamat' => 'nullable|string|max:255',

    // Pengirim (wajib pilih master)
    'pengirim.party_id' => [
        'required',
        Rule::exists('parties','id')->where('type','pengirim'),
    ],
    'pengirim.alamat'   => 'required|string|max:255',
    'pengirim.negara'   => 'required|string|size:2',

    // Penjual (wajib pilih master)
    'penjual.party_id' => [
        'required',
        Rule::exists('parties','id')->where('type','penjual'),
    ],
    'penjual.alamat'   => 'required|string|max:255',
    'penjual.negara'   => 'required|string|size:2',
    ],

'dokumen' => [
        'invoice_no'         => 'required|string|max:100',
        'invoice_tgl'        => 'required|date',
        'packing_list_no'    => 'nullable|string|max:100',
        'packing_list_tgl'   => 'nullable|date',
        'bl_awb_no'          => 'nullable|string|max:100',
        'bl_awb_tgl'         => 'nullable|date',
        'lc_no'              => 'nullable|string|max:100',
        'lc_tgl'             => 'nullable|date',
    ],
'pengangkut' => [
        'moda'               => 'required|in:Laut,Udara,Darat',
        'voy_flight'         => 'nullable|string|max:50',
        'nama_kapal_pesawat' => 'nullable|string|max:120',
        'pelabuhan_muat'     => 'nullable|string|max:100',
        'pelabuhan_transit'  => 'nullable|string|max:100',
        'pelabuhan_bongkar'  => 'nullable|string|max:100',
        'etd'                => 'nullable|date',
        'eta'                => 'nullable|date',
    ],
'kemasan' => [
        'jenis'              => 'required|string|max:50',
        'jumlah'             => 'required|integer|min:0',
        'total_bruto'        => 'nullable|numeric|min:0',
        'total_netto'        => 'nullable|numeric|min:0',
        // (opsional: array container sederhana)
        'containers'                 => 'nullable|array',
        'containers.*.nomor'         => 'nullable|string|max:20',
        'containers.*.ukuran'        => 'nullable|in:20GP,40GP,40HC,45HC,Lainnya',
        'containers.*.seal_no'       => 'nullable|string|max:30',
    ],
'transaksi' => [
        'valuta'             => 'required|string|max:3',
        'kurs'               => 'nullable|numeric|min:0',
        'fob'                => 'nullable|numeric|min:0',
        'freight'            => 'nullable|numeric|min:0',
        'insurance'          => 'nullable|numeric|min:0',
        'cif'                => 'required|numeric|min:0',
        'incoterm'           => 'nullable|string|max:10',
        'cara_pembayaran'    => 'nullable|in:TT,LC,OpenAccount,Consignment,Lainnya',
        'negara_muatan'      => 'nullable|string|max:80',
    ],
'barang' => [
        'items'                      => 'required|array|min:1',
        'items.*.hs'                 => 'required|string|max:20',
        'items.*.uraian'             => 'required|string|max:255',
        'items.*.merk'               => 'nullable|string|max:100',
        'items.*.tipe'               => 'nullable|string|max:100',
        'items.*.negara_asal'        => 'nullable|string|max:80',
        'items.*.qty'                => 'required|numeric|min:0.0001',
        'items.*.sat'                => 'required|string|max:10',
        'items.*.bruto_kg'           => 'nullable|numeric|min:0',
        'items.*.neto_kg'            => 'nullable|numeric|min:0',
        'items.*.nilai_cif'          => 'required|numeric|min:0',
    ],
'pungutan' => [
        'bm_percent'         => 'nullable|numeric|min:0',
        'ppn_percent'        => 'nullable|numeric|min:0',
        'pph_percent'        => 'nullable|numeric|min:0',
        // nilai dihitung di UI namun tetap kita izinkan masuk (optional)
        'bea_masuk'          => 'nullable|numeric|min:0',
        'ppn'                => 'nullable|numeric|min:0',
        'pph'                => 'nullable|numeric|min:0',
        'total_pungutan'     => 'nullable|numeric|min:0',
    ],
'pernyataan' => [
        'declared_by'        => 'required|string|max:150',
        'jabatan'            => 'nullable|string|max:100',
        'place_date'         => 'required|string|max:150',
        'ttd_image_path'     => 'nullable|string|max:255',
        'agree'              => 'accepted',
    ],
        ];
    }    public function show(Request $request, ?string $step = 'header')
    {
        if (!in_array($step, $this->steps, true)) $step = 'header';
        // $data = $request->session()->get('wizard_import', []);
        // dd($data);
        $data = $request->session()->get('wizard_import', []);
        return view('import.wizard', [
            'current' => $step,
            'steps'   => $this->steps,
            'draft'   => $data,
        ]);
    }
    private function generateNomorAju(?string $kp = null): string
    {
        // Format contoh: {KP}{YYYY}{milliTimeRand}
        // Contoh: 020100-2025-68412345
        $kp = $kp ?: '000000';
        $year = date('Y');
        $rand = substr((string) (hrtime(true) % 100000000), -8); // 8 digit
        return "{$kp}-{$year}-{$rand}";
    }

    // === OVERRIDE store di akhir step: jika _editing_id ada, update instead of create ===
    public function store(Request $request, string $step)
    {
        if ($step === 'header') {
            // Generate nomor_aju sekali saja per draft
            $draft = $request->session()->get('wizard_import', []);
            if (empty($draft['header']['nomor_aju'])) {
                $validated['nomor_aju'] = $this->generateNomorAju(
                    $request->input('kantor_pabean')
                );
            } else {
                $validated['nomor_aju'] = $draft['header']['nomor_aju'];
            }
        }

        if (!in_array($step, $this->steps, true)) {
            return redirect()->route('wizard.show', 'header');
        }

        $validated = $request->validate($this->rules[$step]);

        $draft = $request->session()->get('wizard_import', []);
        $draft[$step] = array_merge($draft[$step] ?? [], $validated);
        $request->session()->put('wizard_import', $draft);

        $idx = array_search($step, $this->steps, true);
        $isLast = ($idx === count($this->steps) - 1);

        if ($isLast) {
            $payload = [
                'user_id'    => $request->session()->get('user_id'),
                'header'     => $draft['header'] ?? null,
                'entitas'    => $draft['entitas'] ?? null,
                'dokumen'    => $draft['dokumen'] ?? null,
                'pengangkut' => $draft['pengangkut'] ?? null,
                'kemasan'    => $draft['kemasan'] ?? null,
                'transaksi'  => $draft['transaksi'] ?? null,
                'barang'     => $draft['barang'] ?? null,
                'pungutan'   => $draft['pungutan'] ?? null,
                'pernyataan' => $draft['pernyataan'] ?? null,
                'status'     => 'submitted',
            ];

            if (!empty($draft['_editing_id'])) {
                $model = ImportNotification::where('user_id', $payload['user_id'])
                    ->findOrFail($draft['_editing_id']);
                $model->update($payload);
                $id = $model->id;
                $msg = 'Perubahan tersimpan (ID: '.$id.').';
            } else {
                $model = ImportNotification::create($payload);
                $id = $model->id;
                $msg = 'Pemberitahuan impor tersimpan (ID: '.$id.').';
            }

            $request->session()->forget('wizard_import');
            return redirect()->route('import.show', $id)->with('success', $msg);
        }

        $next = $this->steps[$idx + 1];
        return redirect()->route('wizard.show', $next)->with('success', 'Data disimpan. Lanjut ke '.$next.'.');
    }
    // === LIST ===
    public function index(Request $request)
    {
        $userId = $request->session()->get('user_id');

        $records = \App\Models\ImportNotification::where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('import.index', compact('records'));
    }
    // === DETAIL ===
    public function showDetail(Request $request, int $id)
    {
        $userId = $request->session()->get('user_id');
        $data = ImportNotification::where('user_id', $userId)->findOrFail($id);

        return view('import.show', ['data' => $data]);
    }
    // === EDIT: muat ke session lalu redirect ke step pertama ===
    public function edit(Request $request, int $id)
    {
        $userId = $request->session()->get('user_id');
        $data = ImportNotification::where('user_id', $userId)->findOrFail($id);

        $draft = [
            'header'     => $data->header ?? [],
            'entitas'    => $data->entitas ?? [],
            'dokumen'    => $data->dokumen ?? [],
            'pengangkut' => $data->pengangkut ?? [],
            'kemasan'    => $data->kemasan ?? [],
            'transaksi'  => $data->transaksi ?? [],
            'barang'     => $data->barang ?? [],
            'pungutan'   => $data->pungutan ?? [],
            'pernyataan' => $data->pernyataan ?? [],
            '_editing_id'=> $data->id, // penanda mode edit
        ];
        $request->session()->put('wizard_import', $draft);

        return redirect()->route('wizard.show', 'header')
            ->with('success', 'Mode edit: data dimuat ke draft. Lanjutkan perubahan dan Submit di step terakhir.');
    }
    // === EXPORT PDF ===
    public function exportPdf(Request $request, int $id)
    {
        $userId = $request->session()->get('user_id');
        $data = ImportNotification::where('user_id', $userId)->findOrFail($id);

        $html = view('import.pdf', ['data' => $data])->render();

        // Pastikan package dompdf sudah terinstall
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4');
        return $pdf->download('pemberitahuan-impor-'.$data->id.'.pdf');
    }

}
