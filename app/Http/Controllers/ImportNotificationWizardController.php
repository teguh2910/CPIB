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
        'items_json' => 'required|string',
    ],

    'pengangkut' => [
    // Section BC 1.1
    'bc11.no_tutup_pu' => 'nullable|string|max:50',
    'bc11.pos_1'       => 'nullable|integer|min:0',
    'bc11.pos_2'       => 'nullable|integer|min:0',
    'bc11.pos_3'       => 'nullable|integer|min:0',

    // Section Pengangkutan
    'angkut.cara'      => 'required|string|in:' . implode(',', array_keys(config('import.cara_pengangkutan'))),
    'angkut.nama'      => 'nullable|string|max:120', // jika pakai master local; bisa dijadikan in:... jika mau strict
    'angkut.voy'       => 'nullable|string|max:50',
    'angkut.bendera'   => 'nullable|string|in:' . implode(',', array_keys(config('import.negara'))),
    'angkut.eta'       => 'nullable|date',

    // Section Pelabuhan & Tempat Penimbunan
    'pelabuhan.muat'    => 'nullable|string|in:' . implode(',', array_keys(config('import.pelabuhan'))),
    'pelabuhan.transit' => 'nullable|string|in:' . implode(',', array_keys(config('import.pelabuhan'))),
    'pelabuhan.tujuan'  => 'nullable|string|in:' . implode(',', array_keys(config('import.pelabuhan'))),
    'tps.kode'          => 'nullable|string|in:' . implode(',', array_keys(config('import.tps'))),
    ],
    'kemasan' => [
    // List KEMASAN
    'kemasan'                 => 'required|array|min:0',
    'kemasan.*.seri'          => 'required|integer|min:1',
    'kemasan.*.jumlah'        => 'required|numeric|min:0.0001',
    'kemasan.*.jenis'         => 'required|string|in:' . implode(',', array_keys(config('import.jenis_kemasan'))),
    'kemasan.*.merek'         => 'nullable|string|max:100',

    // List PETI KEMAS
    'petikemas'               => 'required|array|min:0',
    'petikemas.*.seri'        => 'required|integer|min:1',
    'petikemas.*.nomor'       => 'required','string','max:20',
    'petikemas.*.ukuran'      => 'required|string|in:' . implode(',', array_keys(config('import.ukuran_petikemas'))),
    'petikemas.*.jenis_muatan'=> 'required|string|in:' . implode(',', array_keys(config('import.jenis_muatan_petikemas'))),
    'petikemas.*.tipe'        => 'required|string|in:' . implode(',', array_keys(config('import.tipe_petikemas'))),
    ],

    'transaksi' => [
    // HARGA
    'harga.valuta'       => 'required|string|in:' . implode(',', array_keys(config('import.jenis_valuta'))),
    'harga.ndpbm'        => 'required|numeric|min:0',
    'harga.jenis'        => 'required|string|in:' . implode(',', array_keys(config('import.jenis_transaksi'))),
    'harga.incoterm'     => 'required|string|in:' . implode(',', array_keys(config('import.incoterm'))),
    'harga.harga_barang' => 'required|numeric|min:0',
    'harga.nilai_pabean' => 'required|numeric|min:0', // dikalkulasi di front, tapi tetap kita terima

    // BIAYA LAINNYA
    'biaya.penambah'     => 'nullable|numeric|min:0',
    'biaya.pengurang'    => 'nullable|numeric|min:0',
    'biaya.freight'      => 'nullable|numeric|min:0',
    'biaya.jenis_asuransi' => 'required|string|in:' . implode(',', array_keys(config('import.jenis_asuransi'))),
    'biaya.asuransi'     => 'nullable|numeric|min:0',
    'biaya.voluntary_on' => 'nullable|boolean',
    'biaya.voluntary_amt'=> 'nullable|numeric|min:0',

    // BERAT
    'berat.kotor'        => 'required|numeric|min:0',
    'berat.bersih'       => 'required|numeric|min:0',
],

    'barang' => [
    'items'                                     => 'required|array|min:1',
    'items.*.seri'                              => 'required|integer|min:1',

    // Informasi Dasar
    'items.*.pos_tarif'                         => 'required|string|max:20',
    'items.*.lartas'                            => 'required|boolean',
    'items.*.kode_barang'                       => 'nullable|string|max:50',
    'items.*.uraian'                            => 'required|string|max:500',
    'items.*.spesifikasi'                       => 'nullable|string|max:500',
    'items.*.kondisi'                           => 'nullable|string|in:' . implode(',', array_keys(config('import.kondisi_barang'))),
    'items.*.negara_asal'                       => 'required|string|in:' . implode(',', array_keys(config('import.negara'))),
    'items.*.berat_bersih'                      => 'required|numeric|min:0',

    // Jumlah & Kemasan
    'items.*.jumlah'                            => 'required|numeric|min:0.0000001',
    'items.*.satuan'                            => 'required|string|in:' . implode(',', array_keys(config('import.satuan_barang'))),
    'items.*.jml_kemasan'                       => 'nullable|numeric|min:0',
    'items.*.jenis_kemasan'                     => 'nullable|string|in:' . implode(',', array_keys(config('import.jenis_kemasan'))),

    // Nilai & Keuangan
    'items.*.nilai_barang'                      => 'required|numeric|min:0',
    'items.*.fob'                               => 'nullable|numeric|min:0',
    'items.*.freight'                           => 'nullable|numeric|min:0',
    'items.*.asuransi'                          => 'nullable|numeric|min:0',
    'items.*.harga_satuan'                      => 'required|numeric|min:0',
    'items.*.nilai_pabean_rp'                   => 'required|numeric|min:0',
    'items.*.dokumen_fasilitas'                 => 'nullable|string|max:150', // label dari tab Dokumen

    // Pungutan
    'items.*.ket_bm'                            => 'nullable|string|in:' . implode(',', array_keys(config('import.ket_bm'))),
    'items.*.tarif_bm'                          => 'nullable|numeric|min:0',   // persen
    'items.*.bayar_bm'                          => 'nullable|numeric|min:0',

    'items.*.ppn_tarif'                         => 'nullable|numeric|in:0,11,12', // 0/11/12
    'items.*.ket_ppn'                           => 'nullable|string|in:' . implode(',', array_keys(config('import.ket_ppn'))),
    'items.*.bayar_ppn'                         => 'nullable|numeric|min:0',

    'items.*.ket_pph'                           => 'nullable|string|in:' . implode(',', array_keys(config('import.ket_pph'))),
    'items.*.tarif_pph'                         => 'nullable|numeric|min:0',   // persen
    'items.*.bayar_pph'                         => 'nullable|numeric|min:0',
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
        if (!in_array($step, $this->steps, true)) {
            return redirect()->route('wizard.show', 'header');
        }

        // Save current input to session before validation to preserve data on failure
        $draft = $request->session()->get('wizard_import', []);
        $inputData = $request->input();
        $draft[$step] = array_merge($draft[$step] ?? [], $inputData);
        $request->session()->put('wizard_import', $draft);

        $validated = $request->validate($this->rules[$step]);

        // Special handling for dokumen step - decode JSON and validate items
        if ($step === 'dokumen') {
            $itemsJson = $validated['items_json'] ?? '';
            if (empty($itemsJson)) {
                return back()->withErrors(['items_json' => 'The items field is required.']);
            }
            
            $items = json_decode($itemsJson, true);
            if (!is_array($items) || count($items) < 1) {
                return back()->withErrors(['items_json' => 'The items field is required and must contain at least 1 document.']);
            }
            
            foreach ($items as $idx => $item) {
                if (!isset($item['seri']) || !is_numeric($item['seri']) || $item['seri'] < 1) {
                    return back()->withErrors(['items_json' => "Item #".($idx+1).": seri is required and must be a positive integer."]);
                }
                if (!isset($item['jenis']) || !in_array($item['jenis'], array_keys(config('import.jenis_dokumen')))) {
                    return back()->withErrors(['items_json' => "Item #".($idx+1).": jenis is required and must be valid."]);
                }
                if (!isset($item['nomor']) || empty(trim($item['nomor'])) || strlen($item['nomor']) > 100) {
                    return back()->withErrors(['items_json' => "Item #".($idx+1).": nomor is required and must be less than 100 characters."]);
                }
                if (!isset($item['tanggal']) || !strtotime($item['tanggal'])) {
                    return back()->withErrors(['items_json' => "Item #".($idx+1).": tanggal is required and must be a valid date."]);
                }
            }
            
            $validated['items'] = $items;
            unset($validated['items_json']);
        }

        // For header, generate nomor_aju if not already set
        if ($step === 'header') {
            $draft = $request->session()->get('wizard_import', []);
            if (empty($draft['header']['nomor_aju'])) {
                $validated['nomor_aju'] = $this->generateNomorAju(
                    $validated['kantor_pabean'] ?? null
                );
            }
        }

        // Update with validated data
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
