<?php

namespace App\Http\Controllers;

use App\Models\ImportBarang;
use App\Models\ImportDokumen;
use App\Models\ImportEntitas;
use App\Models\ImportHeader;
use App\Models\ImportKemasan;
use App\Models\ImportNotification;
use App\Models\ImportPengangkut;
use App\Models\ImportPernyataan;
use App\Models\ImportPetiKemas;
use App\Models\ImportPungutan;
use App\Models\ImportTransaksi;
use App\Models\ImportBarangDokumen;
use App\Models\ImportBarangTarif;
use App\Models\ImportBarangVd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $importNotifications = ImportNotification::with(['headerRecord', 'entitasRecord.pengirimParty'])->latest()->paginate(10);

        return view('import.index', compact('importNotifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $header = ImportHeader::where('import_notification_id', null)
            ->get();
        $dokument = ImportDokumen::where('import_notification_id', null)
            ->get();
        $entitas_pengirim = ImportEntitas::where('import_notification_id', null)->where('kode_entitas', 9)
            ->get();
        $entitas_penjual = ImportEntitas::where('import_notification_id', null)->where('kode_entitas', 10)
            ->get();
        $pengangkut = ImportPengangkut::where('import_notification_id', null)
            ->get();
        $kemasan = ImportKemasan::where('import_notification_id', null)
            ->get();
        $petiKemas = ImportPetiKemas::where('import_notification_id', null)
            ->get();
        $transaksi = ImportTransaksi::where('import_notification_id', null)
            ->get();
        $barangData = ImportBarang::where('import_notification_id', null)
            ->get();
        $BM = ImportBarangTarif::whereNull('import_notification_id')->where('kode_pungutan', 'BM')->sum('nilai_bayar');
        $PPN = ImportBarangTarif::whereNull('import_notification_id')->where('kode_pungutan', 'PPN')->sum('nilai_bayar');
        $PPH = ImportBarangTarif::whereNull('import_notification_id')->where('kode_pungutan', 'PPH')->sum('nilai_bayar');
        $total = $BM + $PPN + $PPH;

        return view('import.create', compact('header', 'transaksi', 'dokument', 'entitas_pengirim', 'entitas_penjual', 'pengangkut', 'kemasan', 'petiKemas', 'barangData', 'BM', 'PPN', 'PPH', 'total'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $steps = ['header', 'entitas', 'dokumen', 'pengangkut', 'kemasan', 'transaksi', 'barang', 'pungutan', 'pernyataan'];
        $currentStep = $request->input('current_step', 'header');
        $action = $request->input('action', 'submit');
        $draft = session('import_draft', []);
        foreach ($steps as $step) {
            if ($request->has($step)) {
                $draft[$step] = $request->input($step);
            }
        }
        session(['import_draft' => $draft]);
        if ($action === 'save_continue') {
            $currentIndex = array_search($currentStep, $steps, true);
            $nextStep = $currentIndex < count($steps) - 1 ? $steps[$currentIndex + 1] : $steps[0];
            if ($currentStep === 'header' && ! empty($draft['header'])) {
                $hd = $draft['header'];
                $payload = [
                    'user_id' => Auth::user()->id,
                    'import_notification_id' => null,
                    'nomor_aju' => $hd['nomor_aju'] ?? null,
                    'kode_kantor' => $hd['kode_kantor'] ?? null,
                    'kode_jenis_prosedur' => $hd['kode_jenis_prosedur'] ?? null,
                    'kode_jenis_impor' => $hd['kode_jenis_impor'] ?? null,
                    'kode_cara_bayar' => $hd['kode_cara_bayar'] ?? null,
                ];

                if (empty($payload['nomor_aju']) && ! empty($payload['kode_kantor'])) {
                    $year = date('Y');
                    $month = date('m');
                    $day = date('d');
                    $prefix = "000020010653{$year}{$month}{$day}";

                    // Cari nomor aju terakhir untuk hari ini
                    $lastNomorAju = ImportHeader::where('nomor_aju', 'like', $prefix.'%')
                        ->orderBy('nomor_aju', 'desc')
                        ->value('nomor_aju');

                    if ($lastNomorAju) {
                        // Ekstrak sequence (6 digit terakhir)
                        $lastSequence = (int) substr($lastNomorAju, -6);
                        $nextSequence = $lastSequence + 1;
                    } else {
                        // Mulai dari 100332 jika belum ada
                        $nextSequence = 100332;
                    }

                    // Format sequence sebagai 6 digit dengan padding nol
                    $sequence = str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
                    $payload['nomor_aju'] = $prefix.$sequence;
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
                $cek_input = ImportEntitas::where('import_notification_id', null)->where('kode_entitas', 9)->first();
                if ($cek_input) {
                    session(['import_draft' => $draft]);
                } else {
                    $entitas_importir = ImportEntitas::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_entitas' => $request->input('kode_entitas'),
                        'kode_jenis_identitas' => $request->input('kode_jenis_identitas'),
                        'nomor_identitas' => $request->input('importir')['nomor_identitas'],
                        'nama_identitas' => $request->input('importir')['nama_identitas'],
                        'alamat_identitas' => $request->input('importir')['alamat_identitas'],
                        'nib_identitas' => $request->input('importir')['nib_identitas'],
                        'kode_jenis_api' => 02,
                        'kode_status' => $request->input('importir')['kode_status'],
                    ]);
                    $entitas_pemusatan = ImportEntitas::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_entitas' => $request->input('kode_entitas2'),
                        'kode_jenis_identitas' => $request->input('kode_jenis_identitas2'),
                        'nomor_identitas' => $request->input('pemusatan')['nomor_identitas2'],
                        'nama_identitas' => $request->input('pemusatan')['nama_identitas2'],
                        'alamat_identitas' => $request->input('pemusatan')['alamat_identitas2'],
                        'kode_jenis_api' => 02,
                        'kode_status' => $request->input('importir')['kode_status'],
                    ]);
                    $entitas_pemilik = ImportEntitas::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_entitas' => $request->input('kode_entitas3'),
                        'kode_jenis_identitas' => $request->input('kode_jenis_identitas3'),
                        'nomor_identitas' => $request->input('pemilik')['nomor_identitas3'],
                        'nama_identitas' => $request->input('pemilik')['nama_identitas3'],
                        'alamat_identitas' => $request->input('pemilik')['alamat_identitas3'],
                        'kode_jenis_api' => 02,
                        'kode_status' => $request->input('importir')['kode_status'],
                    ]);
                    $entitas_pengirim = ImportEntitas::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_entitas' => $request->input('kode_entitas4'),
                        'nama_identitas' => $request->input('pengirim')['nama_identitas4'],
                        'alamat_identitas' => $request->input('pengirim')['alamat_identitas4'],
                        'kode_negara' => $request->input('pengirim')['kode_negara'],
                    ]);
                    $entitas_penjual = ImportEntitas::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_entitas' => $request->input('kode_entitas5'),
                        'nama_identitas' => $request->input('penjual')['nama_identitas5'],
                        'alamat_identitas' => $request->input('penjual')['alamat_identitas5'],
                        'kode_negara' => $request->input('penjual')['kode_negara2'],
                    ]);

                    session(['import_draft' => $draft]);
                }
            }
            // Handle pengangkut step saving
            if ($currentStep === 'pengangkut') {
                $existingPengangkut = ImportPengangkut::whereNull('import_notification_id')
                    ->first();
                if ($existingPengangkut) {
                    $existingPengangkut->update($request->except(['_token', 'current_step', 'action']));
                    $savedPengangkut = $existingPengangkut;
                } else {
                    $savedPengangkut = ImportPengangkut::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_tutup_pu' => $request->input('kode_tutup_pu'),
                        'nomor_bc_11' => $request->input('nomor_bc_11'),
                        'tanggal_bc_11' => $request->input('tanggal_bc_11'),
                        'nomor_pos' => $request->input('nomor_pos'),
                        'nomor_sub_pos' => $request->input('nomor_sub_pos'),
                        'angkut_cara' => $request->input('angkut_cara'),
                        'angkut_nama' => $request->input('angkut_nama'),
                        'angkut_voy' => $request->input('angkut_voy'),
                        'angkut_bendera' => $request->input('angkut_bendera'),
                        'tanggal_tiba' => $request->input('tanggal_tiba'),
                        'kode_pelabuhan_muat' => $request->input('kode_pelabuhan_muat'),
                        'kode_pelabuhan_transit' => $request->input('kode_pelabuhan_transit'),
                        'kode_pelabuhan_tujuan' => $request->input('kode_pelabuhan_tujuan'),
                        'kode_tps' => $request->input('kode_tps'),

                    ]);
                }

                session(['import_draft' => $draft]);
            }
            // Handle transaksi step saving
            if ($currentStep === 'transaksi') {
                $existingTransaksi = ImportTransaksi::whereNull('import_notification_id')
                    ->first();
                if ($existingTransaksi) {
                    $existingTransaksi->update($request->except(['_token', 'current_step', 'action']));
                    $savedTransaksi = $existingTransaksi;
                } else {
                    ImportTransaksi::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_valuta' => $request->input('kode_valuta'),
                        'ndpbm' => $request->input('ndpbm'),
                        'kode_jenis_nilai' => $request->input('kode_jenis_nilai'),
                        'kode_incoterm' => $request->input('kode_incoterm'),
                        'cif' => $request->input('cif'),
                        'fob' => $request->input('fob'),
                        'bruto' => $request->input('bruto'),
                        'netto' => $request->input('netto'),
                        'nilai_incoterm' => 0,
                        'nilai_barang' => 0,
                        'biaya_tambahan' => $request->input('biaya_tambahan'),
                        'biaya_pengurang' => $request->input('biaya_pengurang'),
                        'freight' => $request->input('freight'),
                        'kode_asuransi' => $request->input('kode_asuransi'),
                        'asuransi' => $request->input('asuransi'),
                        'vd' => $request->input('vd'),
                    ]);
                    session(['import_draft' => $draft]);
                }
            }
            if ($currentStep === 'pungutan'){
                $existingPungutan = ImportPungutan::whereNull('import_notification_id')
                    ->first();
                if ($existingPungutan) {
                    $existingPungutan->update($request->except(['_token', 'current_step', 'action']));
                    $savedPungutan = $existingPungutan;
                } else {
                    ImportPungutan::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'bea_masuk' => $request->input('bea_masuk'),
                        'ppn' => $request->input('ppn'),
                        'pph' => $request->input('pph'),
                        'total_pungutan' => $request->input('total'),
                    ]);
                    session(['import_draft' => $draft]);
                }
            }
            return redirect()->route('import.create', ['step' => $nextStep])
                ->with('success', ucfirst($currentStep).' data saved successfully. Please continue with the next step.');
        }
        if ($action === 'import') {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $uploaded = $request->file('file');
                $path = $uploaded->getRealPath();
            }

            if (! file_exists($path)) {
                return redirect()->back()->with('error', 'File not found: '.$path);
            }

            $spreadsheet = IOFactory::load($path);
            $availableSheets = $spreadsheet->getSheetNames();
            $sheet_barang = $spreadsheet->getSheetByName('BARANG') ?? $spreadsheet->getSheetByName('barang') ?? $spreadsheet->getActiveSheet();
            $sheet_barang_tarif = $spreadsheet->getSheetByName('BARANGTARIF') ?? $spreadsheet->getSheetByName('barangtarif');
            $sheet_barang_dokumen = $spreadsheet->getSheetByName('BARANGDOKUMEN') ?? $spreadsheet->getSheetByName('barangdokumen');
            $sheet_barang_vd = $spreadsheet->getSheetByName('BARANGVD') ?? $spreadsheet->getSheetByName('barangvd');
            // Check if required sheets exist
            if (! $sheet_barang) {
                return redirect()->back()->with('error', 'Sheet "BARANG" tidak ditemukan. Sheet yang tersedia: '.implode(', ', $availableSheets));
            }
            $rows_barang = $sheet_barang->toArray(); // numeric-indexed rows
            $rows_barang_tarif = $sheet_barang_tarif->toArray(); // numeric-indexed rows
            $rows_barang_dokumen = $sheet_barang_dokumen->toArray(); // numeric-indexed rows
            $rows_barang_vd = $sheet_barang_vd->toArray(); // numeric-indexed rows

            // Normalize headers
            $rawHeaders_barang = $rows_barang[0];
            $headers_barang = [];
            foreach ($rawHeaders_barang as $h) {
                $norm = strtolower(trim((string) $h));
                $norm = preg_replace('/[^a-z0-9]+/', '_', $norm);
                $norm = trim($norm, '_');
                $headers_barang[] = $norm;
            }
            // Normalize barang tarif
            $rawHeaders_barang_tarif = $rows_barang_tarif[0] ?? [];
            $headers_barang_tarif = [];
            foreach ($rawHeaders_barang_tarif as $h) {
                $norm = strtolower(trim((string) $h));
                $norm = preg_replace('/[^a-z0-9]+/', '_', $norm);
                $norm = trim($norm, '_');
                $headers_barang_tarif[] = $norm;
            }
            // Normalize barang dokumen
            $rawHeaders_barang_dokumen = $rows_barang_dokumen[0] ?? [];
            $headers_barang_dokumen = [];
            foreach ($rawHeaders_barang_dokumen as $h) {
                $norm = strtolower(trim((string) $h));
                $norm = preg_replace('/[^a-z0-9]+/', '_', $norm);
                $norm = trim($norm, '_');
                $headers_barang_dokumen[] = $norm;
            }
            // Normalize barang vd
            $rawHeaders_barang_vd = $rows_barang_vd[0] ?? [];
            $headers_barang_vd = [];
            foreach ($rawHeaders_barang_vd as $h) {
                $norm = strtolower(trim((string) $h));
                $norm = preg_replace('/[^a-z0-9]+/', '_', $norm);
                $norm = trim($norm, '_');
                $headers_barang_vd[] = $norm;
            }
            // Simple header -> model field map (extend as needed)
            $map_barang = [
                'seri_barang' => 'seri_barang',
                'hs' => 'hs',
                'pernyataan_lartas' => 'pernyataan_lartas',
                'kode_barang' => 'kode_barang',
                'uraian' => 'uraian',
                'spesifikasi_lain' => 'spesifikasi_lain',
                'kode_negara_asal' => 'kode_negara_asal',
                'netto' => 'netto',
                'jumlah_satuan' => 'jumlah_satuan',
                'kode_satuan' => 'kode_satuan',
                'jumlah_kemasan' => 'jumlah_kemasan',
                'kode_kemasan' => 'kode_kemasan',
                'cif' => 'cif',
                'fob' => 'fob',
                'freight' => 'freight',
                'asuransi' => 'asuransi',
                'harga_satuan' => 'harga_satuan',
                'cif_rupiah' => 'cif_rupiah',
                'ndpbm' => 'ndpbm',
            ];
            $map_barang_tarif = [
                'seri_barang' => 'seri_barang',
                'kode_tarif' => 'kode_tarif',
                'kode_pungutan' => 'kode_pungutan',
                'tarif' => 'tarif',
                'kode_fasilitas' => 'kode_fasilitas',
                'tarif_fasilitas' => 'tarif_fasilitas',
                'nilai_bayar' => 'nilai_bayar',
            ];
            $map_barang_dokumen = [
                'seri_barang' => 'seri_barang',
                'seri_dokumen' => 'seri_dokumen',
            ];
            $map_barang_vd = [
                'seri_barang' => 'seri_barang',
                'kode_vd' => 'kode_vd',
                'nilai_barang' => 'nilai_barang',
            ];
            $created = 0;
            $skipped = 0;
            // Process create data barang
            for ($i = 1; $i < count($rows_barang); $i++) {
                $row = $rows_barang[$i];
                // Build payload
                $payload = [
                    'user_id' => Auth::user()->id,
                    'import_notification_id' => null,
                ];

                foreach ($headers_barang as $colIndex => $hkey) {
                    $value = $row[$colIndex] ?? null;
                    if ($value === null || $value === '') {
                        continue;
                    }

                    if (isset($map_barang[$hkey])) {
                        $field = $map_barang[$hkey];
                        $payload[$field] = $value;
                    }
                }
                try {
                    ImportBarang::create($payload);
                    $created++;
                } catch (\Exception $e) {
                    dd('error', $e->getMessage());
                    $skipped++;
                }
            }
            // Prosess create data barang tarif
            for ($i = 1; $i < count($rows_barang_tarif); $i++) {
                $row = $rows_barang_tarif[$i];
                // Build payload
                $payload = [
                    'user_id' => Auth::user()->id,
                    'import_notification_id' => null,
                ];

                foreach ($headers_barang_tarif as $colIndex => $hkey) {
                    $value = $row[$colIndex] ?? null;
                    if ($value === null || $value === '') {
                        continue;
                    }

                    if (isset($map_barang_tarif[$hkey])) {
                        $field = $map_barang_tarif[$hkey];
                        $payload[$field] = $value;
                    }
                }
                try {
                    ImportBarangTarif::create($payload);
                    $created++;
                } catch (\Exception $e) {
                    dd('error', $e->getMessage());
                    $skipped++;
                }
            }
            // Prosess create data barang dokumen
            for ($i = 1; $i < count($rows_barang_dokumen); $i++) {
                $row = $rows_barang_dokumen[$i];
                // Build payload
                $payload = [
                    'user_id' => Auth::user()->id,
                    'import_notification_id' => null,
                ];

                foreach ($headers_barang_dokumen as $colIndex => $hkey) {
                    $value = $row[$colIndex] ?? null;
                    if ($value === null || $value === '') {
                        continue;
                    }

                    if (isset($map_barang_dokumen[$hkey])) {
                        $field = $map_barang_dokumen[$hkey];
                        $payload[$field] = $value;
                    }
                }
                try {
                    ImportBarangDokumen::create($payload);
                    $created++;
                } catch (\Exception $e) {
                    dd('error', $e->getMessage());
                    $skipped++;
                }
            }
            // Prosess create data barang vd
            for ($i = 1; $i < count($rows_barang_vd); $i++) {
                $row = $rows_barang_vd[$i];
                // Build payload
                $payload = [
                    'user_id' => Auth::user()->id,
                    'import_notification_id' => null,
                ];

                foreach ($headers_barang_vd as $colIndex => $hkey) {
                    $value = $row[$colIndex] ?? null;
                    if ($value === null || $value === '') {
                        continue;
                    }

                    if (isset($map_barang_vd[$hkey])) {
                        $field = $map_barang_vd[$hkey];
                        $payload[$field] = $value;
                    }
                }
                try {
                    ImportBarangVd::create($payload);
                    $created++;
                } catch (\Exception $e) {
                    dd('error', $e->getMessage());
                    $skipped++;
                }
            }

            return redirect()->back()->with('success', "Import finished: {$created} created, {$skipped} skipped/Gagal.");
        }
        if ($action === 'submit') {
            // Create the main ImportNotification record first
            $importNotification = ImportNotification::create([
                'status' => 'draft', // or whatever default status you want
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get the import_notification_id for updating related records
            $notificationId = $importNotification->id;

            // Update all related records with the new import_notification_id
            // Update ImportHeader
            ImportHeader::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportEntitas
            ImportEntitas::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportDokumen
            ImportDokumen::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportPengangkut
            ImportPengangkut::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportKemasan
            ImportKemasan::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportPetiKemas
            ImportPetiKemas::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportTransaksi
            ImportTransaksi::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportBarang
            ImportBarang::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportBarangTarif
            ImportBarangTarif::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);
            
            // Update ImportBarangDokumen
            ImportBarangDokumen::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);
            
            // Update ImportBarangVd
            ImportBarangVd::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Update ImportPungutan
            ImportPungutan::whereNull('import_notification_id')
                ->update(['import_notification_id' => $notificationId]);

            // Create ImportPernyataan
            $pernyataan = ImportPernyataan::create([
                'user_id' => Auth::user()->id,
                'import_notification_id' => $notificationId,
                'nama_pernyataan' => $request->input('nama_pernyataan'),
                'jabatan_pernyataan' => $request->input('jabatan_pernyataan'),
                'kota_pernyataan' => $request->input('kota_pernyataan'),
                'tanggal_pernyataan' => $request->input('tanggal_pernyataan'),
            ]);

            // Clear the session draft after successful submission
            session()->forget('import_draft');
        }

        return redirect()->route('import.index')->with('success', 'PIB created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ImportNotification $importNotification)
    {
        $id = $importNotification->id;

        // Include records that belong to this notification or are still unlinked (null)
        $dokument = ImportDokumen::where(function ($q) use ($id) {
            $q->whereNull('import_notification_id')->orWhere('import_notification_id', $id);
        })->get();

        $kemasan = ImportKemasan::where(function ($q) use ($id) {
            $q->whereNull('import_notification_id')->orWhere('import_notification_id', $id);
        })->get();

        $petiKemas = ImportPetiKemas::where(function ($q) use ($id) {
            $q->whereNull('import_notification_id')->orWhere('import_notification_id', $id);
        })->get();

        $barangData = ImportBarang::where(function ($q) use ($id) {
            $q->whereNull('import_notification_id')->orWhere('import_notification_id', $id);
        })->get();

        // Totals for this notification (only include linked items)
        $BM = ImportBarang::where('import_notification_id', $id)->sum('biaya_bm');
        $PPN = ImportBarang::where('import_notification_id', $id)->sum('biaya_ppn');
        $PPH = ImportBarang::where('import_notification_id', $id)->sum('biaya_pph');
        $total = $BM + $PPN + $PPH;

        // Build a draft array from existing related tables so the edit view can reuse the create UI
        $draft = [];

        $header = ImportHeader::where('import_notification_id', $id)->first();
        $draft['header'] = $header ? [
            'nomor_aju' => $header->nomor_aju,
            'kantor_pabean' => $header->kantor_pabean,
            'jenis_pib' => $header->jenis_pib,
            'jenis_impor' => $header->jenis_impor,
            'cara_pembayaran' => $header->cara_pembayaran,
        ] : ($importNotification->header ?? []);

        $entitas = ImportEntitas::where('import_notification_id', $id)->first();
        $draft['entitas'] = $entitas ? $entitas->toArray() : ($importNotification->entitas ?? []);

        $docs = ImportDokumen::where('import_notification_id', $id)->get();
        $draft['dokumen'] = $docs->map(function ($d) {
            return $d->toArray();
        })->all();

        $peng = ImportPengangkut::where('import_notification_id', $id)->first();
        $draft['pengangkut'] = $peng ? $peng->toArray() : ($importNotification->pengangkut ?? []);

        $kms = ImportKemasan::where('import_notification_id', $id)->get();
        $draft['kemasan'] = $kms->map(function ($k) {
            return $k->toArray();
        })->all();

        $trans = ImportTransaksi::where('import_notification_id', $id)->first();
        $draft['transaksi'] = $trans ? $trans->toArray() : ($importNotification->transaksi ?? []);

        $brs = ImportBarang::where('import_notification_id', $id)->get();
        $draft['barang'] = $brs->map(function ($b) {
            return $b->toArray();
        })->all();

        $pung = ImportPungutan::where('import_notification_id', $id)->first();
        $draft['pungutan'] = $pung ? $pung->toArray() : ($importNotification->pungutan ?? []);

        $per = ImportPernyataan::where('import_notification_id', $id)->first();
        $draft['pernyataan'] = $per ? $per->toArray() : ($importNotification->pernyataan ?? []);

        // Store draft in session so the existing create UI can render current values
        session(['import_draft' => $draft]);

        return view('import.edit', compact('dokument', 'kemasan', 'petiKemas', 'barangData', 'BM', 'PPN', 'PPH', 'total', 'importNotification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImportNotification $importNotification)
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
        $currentIndex = array_search($currentStep, $steps, true);
        $nextStep = $currentIndex < count($steps) - 1 ? $steps[$currentIndex + 1] : $steps[0];
        if ($action === 'save_continue') {
            if ($currentStep == 'transaksi') {
                $transaksi = ImportTransaksi::where('import_notification_id', $importNotification->id)->first();
                $transaksi->update($request->input());

                return redirect()->route('import.edit', [$importNotification, 'step' => $nextStep])
                    ->with('success', ucfirst($currentStep).' data updated successfully. Please continue with the next step.');
            }
            if ($currentStep == 'header') {
                // Update the ImportHeader record linked to this notification
                $header = ImportHeader::where('import_notification_id', $importNotification->id)->first();
                // Update the ImportHeader record
                $header->update($request->input('header'));

                // Redirect back to the edit form for this notification and go to the next step
                return redirect()->route('import.edit', [$importNotification, 'step' => $nextStep])
                    ->with('success', ucfirst($currentStep).' data updated successfully. Please continue with the next step.');
            } elseif ($currentStep == 'entitas') {
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

                // Determine pengirim and penjual party ids
                $pengirimId = $et['pengirim']['party_id'] ?? $et['pengirim_party_id'] ?? null;
                $penjualId = $et['penjual']['party_id'] ?? $et['penjual_party_id'] ?? null;

                // Prepare entitas payload for DB (link to this notification)
                $entitasPayload = [
                    'user_id' => Auth::id() ?? 1,
                    'import_notification_id' => $importNotification->id,
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

                // Find existing entitas linked to this notification or create/update accordingly
                $existingEntitas = ImportEntitas::where('import_notification_id', $importNotification->id)->first();
                if ($existingEntitas) {
                    $existingEntitas->update(array_filter($entitasPayload));
                    $savedEntitas = $existingEntitas;
                } else {
                    $savedEntitas = ImportEntitas::create($entitasPayload);
                }

                // Persist generated ids/values into session draft
                $draft['entitas'] = array_merge($draft['entitas'] ?? [], [
                    'pengirim_party_id' => $pengirimId,
                    'penjual_party_id' => $penjualId,
                    'id' => $savedEntitas->id,
                ]);
                session(['import_draft' => $draft]);

                // Redirect back to the edit form for this notification and go to the next step
                return redirect()->route('import.edit', [$importNotification, 'step' => $nextStep])
                    ->with('success', ucfirst($currentStep).' data updated successfully. Please continue with the next step.');
            } elseif ($currentStep == 'pengangkut') {
                // Start with existing draft pengangkut if any
                $peng = $draft['pengangkut'] ?? [];

                // If the form submitted pengangkut-related data
                if ($request->has('bc11') || $request->has('angkut') || $request->has('pelabuhan') || $request->has('tps')) {
                    $inputPeng = [
                        'bc11' => $request->input('bc11', []),
                        'angkut' => $request->input('angkut', []),
                        'pelabuhan' => $request->input('pelabuhan', []),
                        'tps' => $request->input('tps', []),
                    ];

                    // Merge nested arrays into draft pengangkut
                    $peng = array_merge($peng, $inputPeng);

                    // Persist normalized pengangkut back into session draft immediately so view sees it
                    $draft['pengangkut'] = $peng;
                    session(['import_draft' => $draft]);
                }

                // Prepare pengangkut payload for DB (link to this notification)
                $pengangkutPayload = [
                    'user_id' => 1,
                    'import_notification_id' => $importNotification->id,
                    // BC 1.1 data
                    'bc11_no_tutup_pu' => $peng['bc11']['no_tutup_pu'] ?? null,
                    'bc11_pos_1' => $peng['bc11']['pos_1'] ?? null,
                    'bc11_pos_2' => $peng['bc11']['pos_2'] ?? null,
                    'bc11_pos_3' => $peng['bc11']['pos_3'] ?? null,
                    // Pengangkutan data
                    'angkut_cara' => $peng['angkut']['cara'] ?? null,
                    'angkut_nama' => $peng['angkut']['nama'] ?? null,
                    'angkut_voy' => $peng['angkut']['voy'] ?? null,
                    'angkut_bendera' => $peng['angkut']['bendera'] ?? null,
                    'angkut_eta' => $peng['angkut']['eta'] ?? null,
                    // Pelabuhan data
                    'pelabuhan_muat' => $peng['pelabuhan']['muat'] ?? null,
                    'pelabuhan_transit' => $peng['pelabuhan']['transit'] ?? null,
                    'pelabuhan_tujuan' => $peng['pelabuhan']['tujuan'] ?? null,
                    // TPS data
                    'tps_kode' => $peng['tps']['kode'] ?? null,
                ];

                // Find existing pengangkut linked to this notification or create/update accordingly
                $existingPengangkut = ImportPengangkut::where('import_notification_id', $importNotification->id)->first();
                if ($existingPengangkut) {
                    $existingPengangkut->update(array_filter($pengangkutPayload));
                    $savedPengangkut = $existingPengangkut;
                } else {
                    $savedPengangkut = ImportPengangkut::create($pengangkutPayload);
                }

                // Persist generated id into session draft
                $draft['pengangkut'] = array_merge($draft['pengangkut'] ?? [], [
                    'id' => $savedPengangkut->id,
                ]);
                session(['import_draft' => $draft]);

                // Redirect back to the edit form for this notification and go to the next step
                return redirect()->route('import.edit', [$importNotification, 'step' => $nextStep])
                    ->with('success', ucfirst($currentStep).' data updated successfully. Please continue with the next step.');
            }

            return redirect()->route('import.edit', $importNotification)->with('success', ucfirst($currentStep).' data saved.')->with('step', $nextStep);
        }

        if ($action === 'import') {
            // Read uploaded CSV/Excel file from the form (input name="file").
            // If no uploaded file is provided, fallback to storage/app/users.xlsx
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $uploaded = $request->file('file');
                $path = $uploaded->getRealPath();
            }

            if (! file_exists($path)) {
                return redirect()->back()->with('error', 'File not found: users.xlsx (expected in storage/app or upload).');
            }

            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(); // numeric-indexed rows
            if (count($rows) < 2) {
                return redirect()->back()->with('error', 'Spreadsheet has no data rows.');
            }

            // Normalize headers
            $rawHeaders = $rows[0];
            $headers = [];
            foreach ($rawHeaders as $h) {
                $norm = strtolower(trim((string) $h));
                $norm = preg_replace('/[^a-z0-9]+/', '_', $norm);
                $norm = trim($norm, '_');
                $headers[] = $norm;
            }

            // Simple header -> model field map (extend as needed)
            $map = [
                'seri' => 'seri',
                'pos_tarif' => 'pos_tarif',
                'postarif' => 'pos_tarif',
                'lartas' => 'lartas',
                'kode_barang' => 'kode_barang',
                'kodebarang' => 'kode_barang',
                'uraian' => 'uraian',
                'spesifikasi' => 'spesifikasi',
                'kondisi' => 'kondisi',
                'negara_asal' => 'negara_asal',
                'negaraasal' => 'negara_asal',
                'berat_bersih' => 'berat_bersih',
                'beratbersih' => 'berat_bersih',
                'jumlah' => 'jumlah',
                'nilai_pabean_rp' => 'nilai_pabean_rp',
                'satuan' => 'satuan',
                'jml_kemasan' => 'jml_kemasan',
                'jmlkemasan' => 'jml_kemasan',
                'jenis_kemasan' => 'jenis_kemasan',
                'jeniskemasan' => 'jenis_kemasan',
                'nilai_barang' => 'nilai_barang',
                'fob' => 'fob',
                'freight' => 'freight',
                'asuransi' => 'asuransi',
                'harga_satuan' => 'harga_satuan',
                'nilai_pabean_rp' => 'nilai_pabean_rp',
                'dokumen_fasilitas' => 'dokumen_fasilitas',
                'ket_bm' => 'ket_bm', 'tarif_bm' => 'tarif_bm', 'bayar_bm' => 'bayar_bm', 'biaya_bm' => 'biaya_bm',
                'ppn_tarif' => 'ppn_tarif', 'ket_ppn' => 'ket_ppn', 'bayar_ppn' => 'bayar_ppn', 'biaya_ppn' => 'biaya_ppn',
                'ket_pph' => 'ket_pph', 'tarif_pph' => 'tarif_pph', 'bayar_pph' => 'bayar_pph', 'biaya_pph' => 'biaya_pph',
            ];

            $created = 0;
            $skipped = 0;
            // Process data rows (skip header at index 0)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                // Build payload
                $payload = [
                    'user_id' => 1,
                    'import_notification_id' => null,
                ];

                foreach ($headers as $colIndex => $hkey) {
                    $value = $row[$colIndex] ?? null;
                    if ($value === null || $value === '') {
                        continue;
                    }

                    if (isset($map[$hkey])) {
                        $field = $map[$hkey];
                        // Normalize some fields
                        if ($field === 'lartas') {
                            $payload[$field] = in_array(strtolower((string) $value), ['1', 'true', 'yes', 'y'], true) ? 1 : 0;
                        } else {
                            // Remove thousands separators for numeric-like fields
                            if (is_numeric(str_replace([',', '.'], ['', '.'], (string) $value))) {
                                $payload[$field] = strpos((string) $value, ',') !== false ? floatval(str_replace(',', '', (string) $value)) : $value;
                            } else {
                                $payload[$field] = $value;
                            }
                        }
                    }
                }

                // If payload has only user_id, skip
                if (count($payload) <= 2) {
                    $skipped++;

                    continue;
                }

                try {
                    \App\Models\ImportBarang::create($payload);
                    $created++;
                } catch (\Exception $e) {
                    dd('error', $e->getMessage());
                    $skipped++;
                }
            }

            return redirect()->back()->with('success', "Import finished: {$created} created, {$skipped} skipped.");
        }
        if ($action == 'updateAll') {
            $userId = 1;

            // Get current NDPMB/harga_ndpbm
            $transaksiData = ImportTransaksi::where('import_notification_id', $importNotification->id)->first();
            $ndpbm = $transaksiData ? $transaksiData->harga_ndpbm : 1;

            $barangs = ImportBarang::where('user_id', $userId)->get();
            $updated = 0;
            foreach ($barangs as $b) {
                $nilaiBarang = $b->nilai_barang ?? 0;
                $tarifBm = $b->tarif_bm ?? 0;
                $tarifPpn = $b->ppn_tarif ?? 0;
                $tarifPph = $b->tarif_pph ?? 0;
                $b->import_notification_id = $importNotification->id;
                $b->nilai_pabean_rp = $nilaiBarang * $ndpbm;
                $b->biaya_bm = ($tarifBm / 100) * $nilaiBarang * $ndpbm;
                $b->biaya_ppn = ($tarifPpn / 100) * $nilaiBarang * $ndpbm;
                $b->biaya_pph = ($tarifPph / 100) * $nilaiBarang * $ndpbm;

                if ($b->isDirty()) {
                    $b->save();
                    $updated++;
                }
            }

            return redirect()->back()->with('success', "Selesai: {$updated} baris diperbarui");
        }

    }

    /**
     * Search pelabuhan from external API
     */
    public function searchPelabuhan(Request $request)
    {
        $query = $request->get('q', '');
        $apiUrl = config('services.pelabuhan_api.url');
        $apiToken = config('services.pelabuhan_api.token');

        if (empty($query) || empty($apiUrl) || empty($apiToken)) {
            return response()->json(['status' => 'ERROR', 'message' => 'Missing parameters']);
        }

        try {
            $client = new \GuzzleHttp\Client;
            $response = $client->get("{$apiUrl}/openapi/pelabuhan/kata/{$query}", [
                'headers' => [
                    'Authorization' => "Bearer {$apiToken}",
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Failed to fetch pelabuhan data: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search TPS from external API
     */
    public function searchTps(Request $request)
    {

        $header = ImportHeader::select('kode_kantor')->where('import_notification_id', null)->first();
        $kodeKantor = $header->kode_kantor;
        $searchTerm = $request->get('q', ''); // Get search term from request
        $apiUrl = config('services.pelabuhan_api.url');
        $apiToken = config('services.pelabuhan_api.token');

        if (empty($kodeKantor) || empty($apiUrl) || empty($apiToken)) {
            return response()->json(['status' => 'ERROR', 'message' => 'Missing parameters']);
        }

        try {
            $client = new \GuzzleHttp\Client;
            $response = $client->get("{$apiUrl}/openapi/gudangTPS/kodeKantor/{$kodeKantor}", [
                'headers' => [
                    'Authorization' => "Bearer {$apiToken}",
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);

            $data = json_decode($response->getBody(), true);

            // Filter results based on search term if provided
            if (! empty($searchTerm) && isset($data['data']) && is_array($data['data'])) {
                $filteredData = array_filter($data['data'], function ($item) use ($searchTerm) {
                    $searchLower = strtolower($searchTerm);
                    $namaGudang = strtolower($item['namaGudang'] ?? '');
                    $kodeGudang = strtolower($item['kodeGudang'] ?? '');

                    return strpos($namaGudang, $searchLower) !== false ||
                           strpos($kodeGudang, $searchLower) !== false;
                });

                $data['data'] = array_values($filteredData);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Failed to fetch TPS data: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search Pelabuhan Tujuan from external API based on kodeKantor
     */
    public function searchPelabuhanTujuan(Request $request)
    {
        $kodeKantor = ImportHeader::select('kode_kantor')->where('import_notification_id', null)->first();
        $kodeKantor = $kodeKantor->kode_kantor;
        $searchTerm = $request->get('q', '');
        $apiUrl = config('services.pelabuhan_api.url');
        $apiToken = config('services.pelabuhan_api.token');

        if (empty($kodeKantor) || empty($apiUrl) || empty($apiToken)) {
            return response()->json(['status' => 'ERROR', 'message' => 'Missing parameters']);
        }

        try {
            $client = new \GuzzleHttp\Client;
            $response = $client->get("{$apiUrl}/openapi/pelabuhan/kodeKantor/{$kodeKantor}", [
                'headers' => [
                    'Authorization' => "Bearer {$apiToken}",
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);

            $data = json_decode($response->getBody(), true);

            // Filter results based on search term if provided
            if (! empty($searchTerm) && isset($data['data']) && is_array($data['data'])) {
                $filteredData = array_filter($data['data'], function ($item) use ($searchTerm) {
                    $searchLower = strtolower($searchTerm);
                    $namaPelabuhan = strtolower($item['namaPelabuhan'] ?? '');
                    $kodePelabuhan = strtolower($item['kodePelabuhan'] ?? '');

                    return strpos($namaPelabuhan, $searchLower) !== false ||
                           strpos($kodePelabuhan, $searchLower) !== false;
                });

                $data['data'] = array_values($filteredData);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Failed to fetch pelabuhan tujuan data: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search Negara (Countries) from config
     */
    public function searchNegara(Request $request)
    {
        $searchTerm = $request->get('q', '');
        $countries = config('import.kode_negara', []);

        // Filter countries based on search term if provided
        if (! empty($searchTerm)) {
            $searchLower = strtolower($searchTerm);
            $filteredCountries = [];

            foreach ($countries as $code => $name) {
                $nameLower = strtolower($name);
                $codeLower = strtolower($code);

                if (strpos($nameLower, $searchLower) !== false ||
                    strpos($codeLower, $searchLower) !== false) {
                    $filteredCountries[$code] = $name;
                }
            }

            $countries = $filteredCountries;
        }

        // Convert to format expected by Select2
        $results = [];
        foreach ($countries as $code => $name) {
            $results[] = [
                'id' => $code,
                'text' => $name.' ('.$code.')',
                'kodeNegara' => $code,
                'namaNegara' => $name,
            ];
        }

        return response()->json([
            'status' => 'OK',
            'data' => $results,
        ]);
    }

    /**
     * Get kurs from external API
     */
    public function getKurs(Request $request)
    {
        $kodeValuta = $request->get('kode_valuta');
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        $apiUrl = config('services.pelabuhan_api.url');
        $apiToken = config('services.pelabuhan_api.token');

        if (empty($kodeValuta) || empty($apiUrl) || empty($apiToken)) {
            return response()->json(['status' => 'ERROR', 'message' => 'Missing parameters']);
        }

        try {
            $client = new \GuzzleHttp\Client;
            $response = $client->get("{$apiUrl}/openapi/kurs/{$kodeValuta}?tanggal={$tanggal}", [
                'headers' => [
                    'Authorization' => "Bearer {$apiToken}",
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Failed to fetch kurs: '.$e->getMessage(),
            ]);
        }
    }
}
