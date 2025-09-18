<?php

namespace App\Http\Controllers;

use App\Models\ImportBarang;
use App\Models\ImportBarangDokumen;
use App\Models\ImportBarangTarif;
use App\Models\ImportBarangVd;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $importNotifications = \DB::table('import_notifications')
            ->join('import_entitas', 'import_notifications.id', '=', 'import_entitas.import_notification_id')
            ->join('import_headers', 'import_notifications.id', '=', 'import_headers.import_notification_id')
            ->join('import_pengangkuts', 'import_notifications.id', '=', 'import_pengangkuts.import_notification_id')
            ->join('parties', 'import_entitas.nama_identitas', '=', 'parties.id')
            ->where('kode_entitas', 9)
            ->get();

        return view('import.index', compact('importNotifications'));
    }

    /**
     * Export multiple tables into one Excel file where each table is a separate sheet.
     */
    public function exportAll()
    {
        // List of models and sheet names to export
        $sheets = [
            'IMPORT_NOTIFICATION' => \App\Models\ImportNotification::all()->toArray(),
            'IMPORT_HEADER' => \App\Models\ImportHeader::all()->toArray(),
            'IMPORT_ENTITAS' => \App\Models\ImportEntitas::all()->toArray(),
            'IMPORT_DOKUMEN' => \App\Models\ImportDokumen::all()->toArray(),
            'IMPORT_PENGANGKUT' => \App\Models\ImportPengangkut::all()->toArray(),
            'IMPORT_KEMASAN' => \App\Models\ImportKemasan::all()->toArray(),
            'IMPORT_PETIKEMAS' => \App\Models\ImportPetiKemas::all()->toArray(),
            'IMPORT_TRANSAKSI' => \App\Models\ImportTransaksi::all()->toArray(),
            'IMPORT_BARANG' => \App\Models\ImportBarang::all()->toArray(),
            'IMPORT_BARANG_TARIF' => \App\Models\ImportBarangTarif::all()->toArray(),
            'IMPORT_BARANG_DOKUMEN' => \App\Models\ImportBarangDokumen::all()->toArray(),
            'IMPORT_BARANG_VD' => \App\Models\ImportBarangVd::all()->toArray(),
            'IMPORT_PUNGUTAN' => \App\Models\ImportPungutan::all()->toArray(),
            'IMPORT_PERNYATAAN' => \App\Models\ImportPernyataan::all()->toArray(),
        ];

        $spreadsheet = new Spreadsheet;
        $sheetIndex = 0;

        foreach ($sheets as $name => $rows) {
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle(substr($name, 0, 31));
            } else {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle(substr($name, 0, 31));
            }

            if (empty($rows)) {
                $sheet->fromArray([['No data']], null, 'A1');
                $sheetIndex++;

                continue;
            }

            // Use keys from first row as headers
            $headers = array_keys($rows[0]);
            $sheet->fromArray($headers, null, 'A1');

            // Write data starting from row 2
            $rowNumber = 2;
            foreach ($rows as $r) {
                $col = 'A';
                foreach ($headers as $h) {
                    $value = $r[$h] ?? null;
                    $sheet->setCellValue($col.$rowNumber, $value);
                    $col++;
                }
                $rowNumber++;
            }

            $sheetIndex++;
        }

        // Set first sheet active
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'export_all_tables_'.date('Ymd_His').'.xlsx';

        // Stream to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export data related to a single import notification id into Excel (one sheet per related table)
     */
    public function exportByNotification(int $importNotificationId)
    {
        $sheets = [
            'IMPORT_NOTIFICATION' => \App\Models\ImportNotification::where('id', $importNotificationId)->get()->toArray(),
            'IMPORT_HEADER' => \App\Models\ImportHeader::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_ENTITAS' => \App\Models\ImportEntitas::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_DOKUMEN' => \App\Models\ImportDokumen::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_PENGANGKUT' => \App\Models\ImportPengangkut::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_KEMASAN' => \App\Models\ImportKemasan::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_PETIKEMAS' => \App\Models\ImportPetiKemas::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_TRANSAKSI' => \App\Models\ImportTransaksi::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_BARANG' => \App\Models\ImportBarang::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_BARANG_TARIF' => \App\Models\ImportBarangTarif::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_BARANG_DOKUMEN' => \App\Models\ImportBarangDokumen::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_BARANG_VD' => \App\Models\ImportBarangVd::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_PUNGUTAN' => \App\Models\ImportPungutan::where('import_notification_id', $importNotificationId)->get()->toArray(),
            'IMPORT_PERNYATAAN' => \App\Models\ImportPernyataan::where('import_notification_id', $importNotificationId)->get()->toArray(),
        ];

        $spreadsheet = new Spreadsheet;
        $sheetIndex = 0;

        foreach ($sheets as $name => $rows) {
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle(substr($name, 0, 31));
            } else {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle(substr($name, 0, 31));
            }

            if (empty($rows)) {
                $sheet->fromArray([['No data']], null, 'A1');
                $sheetIndex++;

                continue;
            }

            $headers = array_keys($rows[0]);
            $sheet->fromArray($headers, null, 'A1');

            $rowNumber = 2;
            foreach ($rows as $r) {
                $col = 'A';
                foreach ($headers as $h) {
                    $value = $r[$h] ?? null;
                    $sheet->setCellValue($col.$rowNumber, $value);
                    $col++;
                }
                $rowNumber++;
            }

            $sheetIndex++;
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        $fileName = 'export_import_notification_'.$importNotificationId.'_'.date('Ymd_His').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export data related to a single import notification id into a nested JSON structure
     */
    public function exportJsonByNotification(string $nomorAju)
    {
        $header = ImportHeader::where('nomor_aju', $nomorAju)->first();
        $transaksi = ImportTransaksi::where('no_aju', $nomorAju)->first();
        $pernyataan = ImportPernyataan::where('no_aju', $nomorAju)->first();

        $pengangkuts = ImportPengangkut::where('no_aju', $nomorAju)->get();
        $kemasans = ImportKemasan::where('no_aju', $nomorAju)->get();
        $petikemas = ImportPetiKemas::where('no_aju', $nomorAju)->get();
        $dokumens = ImportDokumen::where('no_aju', $nomorAju)->get();
        // $entitas = ImportEntitas::where('import_notification_id', $id)->get();
        $entitas = \DB::table('import_entitas')->join('parties', 'import_entitas.nama_identitas', '=', 'parties.id')
            ->where('import_entitas.no_aju', $nomorAju)
            ->get();
        $barangs = ImportBarang::where('no_aju', $nomorAju)->get();

        // Top level defaults and mappings (assumptions for fields not present in schema)
        $result = [
            'asalData' => 'S', // assumption: source S
            'asuransi' => (int) $transaksi->asuransi ?? 0,
            'biayaPengurang' => (int) $transaksi->biaya_pengurang ?? 0,
            'biayaTambahan' => (int) $transaksi->biaya_tambahan ?? 0,
            'bruto' => (float) $transaksi->bruto ?? 0,
            'cif' => (float) $transaksi->cif ?? 0,
            'disclaimer' => '1',
            'flagVd' => 'T',
            'fob' => (float) $transaksi->fob ?? 0,
            'freight' => (int) $transaksi->freight ?? 0,
            'hargaPenyerahan' => 0,
            'idPengguna' => 'aisinindonesia',
            'jabatanTtd' => $pernyataan->jabatan_pernyataan ?? null,
            'jumlahKontainer' => $petikemas->count(),
            'jumlahTandaPengaman' => 0,
            'kodeAsuransi' => $transaksi->kode_asuransi ?? null,
            'kodeCaraBayar' => $header->kode_cara_bayar ?? null,
            'kodeDokumen' => '20',
            'kodeIncoterm' => $transaksi->kode_incoterm ?? null,
            'kodeJenisImpor' => $header->kode_jenis_impor ?? null,
            'kodeJenisNilai' => $transaksi->kode_jenis_nilai ?? null,
            'kodeJenisProsedur' => $header->kode_jenis_prosedur ?? null,
            'kodeKantor' => $header->kode_kantor ?? null,
            'kodePelMuat' => $pengangkuts->first()->kode_pelabuhan_muat ?? null,
            'kodePelTransit' => $pengangkuts->first()->kode_pelabuhan_transit ?? null,
            'kodePelTujuan' => $pengangkuts->first()->kode_pelabuhan_tujuan ?? null,
            'kodeTps' => $pengangkuts->first()->kode_tps ?? null,
            'kodeTutupPu' => $pengangkuts->first()->kode_tutup_pu ?? null,
            'kodeValuta' => $transaksi->kode_valuta ?? null,
            'kotaTtd' => $pernyataan->kota_pernyataan ?? null,
            'namaTtd' => $pernyataan->nama_pernyataan ?? null,
            'ndpbm' => (float) $transaksi->ndpbm ?? 0,
            'netto' => (float) $transaksi->netto ?? 0,
            'nilaiBarang' => (int) $transaksi->nilai_barang ?? 0,
            'nilaiIncoterm' => (int) $transaksi->nilai_incoterm ?? 0,
            'nilaiMaklon' => 0,
            'nomorAju' => $header->nomor_aju ?? null,
            'nomorBc11' => $pengangkuts->first()->nomor_bc_11 ?? null,
            'posBc11' => $pengangkuts->first()->nomor_pos ?? null,
            'seri' => 0,
            'subPosBc11' => $pengangkuts->first()->nomor_sub_pos ?? null,
            'tanggalAju' => optional($header->created_at)->format('Y-m-d') ?? null,
            'tanggalBc11' => $pengangkuts->first()->tanggal_bc_11 ?? null,
            'tanggalTiba' => $pengangkuts->first()->tanggal_tiba ?? null,
            'tanggalTtd' => $pernyataan->tanggal_pernyataan ?? null,
            'totalDanaSawit' => 0,
            'volume' => 0,
            'vd' => (int) $transaksi->vd ?? 0,
        ];

        // barang
        $result['barang'] = [];
        foreach ($barangs as $b) {
            $seri = $b->seri_barang ?? $b->id;

            $barang = [
                'asuransi' => (int) $b->asuransi ?? 0,
                'bruto' => (float) $b->bruto ?? 0,
                'cif' => (float) $b->cif ?? 0,
                'cifRupiah' => (float) $b->cif_rupiah ?? (float) $b->cif ?? 0,
                'diskon' => 0,
                'fob' => (int) $b->fob ?? 0,
                'freight' => (int) $b->freight ?? 0,
                'hargaEkspor' => 0,
                'hargaPatokan' => 0,
                'hargaPenyerahan' => 0,
                'hargaPerolehan' => 0,
                'hargaSatuan' => (float) $b->harga_satuan ?? 0,
                'hjeCukai' => 0,
                'isiPerKemasan' => 0,
                'jumlahBahanBaku' => 0,
                'jumlahDilekatkan' => 0,
                'jumlahKemasan' => (int) $b->jumlah_kemasan ?? 0,
                'jumlahPitaCukai' => (int) $b->jumlah_pita_cukai ?? 0,
                'jumlahRealisasi' => 0,
                'jumlahSatuan' => (int) $b->jumlah_satuan ?? 0,
                'kapasitasSilinder' => 0,
                'kodeJenisKemasan' => $b->kode_kemasan ?? null,
                'kodeKondisiBarang' => $b->kode_kondisi_barang ?? null,
                'kodeNegaraAsal' => $b->kode_negara_asal ?? null,
                'kodeSatuanBarang' => $b->kode_satuan ?? null,
                'merk' => '-',
                'ndpbm' => (float) $b->ndpbm ?? 0,
                'netto' => (float) $b->netto ?? 0,
                'nilaiBarang' => (int) $b->nilai_barang ?? 0,
                'nilaiDanaSawit' => 0,
                'nilaiDevisa' => 0,
                'nilaiTambah' => 0,
                'pernyataanLartas' => $b->pernyataan_lartas,
                'persentaseImpor' => 0,
                'posTarif' => $b->hs ?? null,
                'saldoAkhir' => 0.0,
                'saldoAwal' => 0.0,
                'seriBarang' => (int) $seri,
                'seriBarangDokAsal' => 0,
                'seriIjin' => 0,
                'tahunPembuatan' => 0,
                'tarifCukai' => 0,
                'tipe' => '-',
                'uraian' => $b->uraian ?? null,
                'volume' => 0,
            ];

            // barangDokumen
            $barangDok = ImportBarangDokumen::where('no_aju', $nomorAju)->where('seri_barang', $seri)->get();
            $barang['barangDokumen'] = $barangDok->map(function ($d) {
                return ['seriDokumen' => (string) $d->seri_dokumen];
            })->toArray();

            // barangTarif
            $tarifs = \DB::table('import_barangs')
                ->join('import_barang_tarifs', 'import_barang_tarifs.seri_barang', '=', 'import_barangs.seri_barang')
                ->where('import_barang_tarifs.no_aju', $nomorAju)->where('import_barangs.seri_barang', $seri)->get();
            $barang['barangTarif'] = $tarifs->map(function ($t) use ($seri) {
                return [
                    'jumlahSatuan' => (int) $t->jumlah_satuan ?? 1,
                    'kodeFasilitasTarif' => (string) $t->kode_fasilitas ?? ($t->kodeFasilitasTarif ?? null),
                    'kodeJenisPungutan' => $t->kode_pungutan ?? ($t->kodeJenisPungutan ?? null),
                    'kodeJenisTarif' => (string) $t->kode_tarif ?? ($t->kodeJenisTarif ?? null),
                    'nilaiBayar' => (int) $t->nilai_bayar ?? 0,
                    'nilaiFasilitas' => $t->nilai_fasilitas ?? 1,
                    'seriBarang' => (int) $seri,
                    'tarif' => (float) $t->tarif ?? 0,
                    'tarifFasilitas' => (float) $t->tarif_fasilitas ?? 0,
                ];
            })->toArray();

            // barangVd
            $vds = ImportBarangVd::where('no_aju', $nomorAju)->where('seri_barang', $seri)->get();
            $barang['barangVd'] = $vds->map(function ($v) {
                return [
                    'kodeJenisVd' => $v->kode_vd ?? $v->kodeJenisVd ?? null,
                    'nilaiBarangVd' => $v->nilai_barang ?? 0,
                ];
            })->toArray();

            $barang['barangSpekKhusus'] = [];
            $barang['barangPemilik'] = [];

            $result['barang'][] = $barang;
        }

        // entitas
        $result['entitas'] = $entitas->map(function ($e) {
            $entitasItem = [];

            if (! is_null($e->alamat_identitas)) {
                $entitasItem['alamatEntitas'] = $e->alamat_identitas;
            }
            if (! is_null($e->kode_entitas)) {
                $entitasItem['kodeEntitas'] = $e->kode_entitas;
            }
            if (! is_null($e->kode_jenis_api)) {
                $entitasItem['kodeJenisApi'] = $e->kode_jenis_api;
            }
            if (! is_null($e->kode_jenis_identitas)) {
                $entitasItem['kodeJenisIdentitas'] = $e->kode_jenis_identitas;
            }
            if (! is_null($e->kode_status)) {
                $entitasItem['kodeStatus'] = $e->kode_status;
            }
            if (! is_null($e->nama_identitas)) {
                $entitasItem['namaEntitas'] = $e->name;
            }
            if (! is_null($e->nib_identitas)) {
                $entitasItem['nibEntitas'] = $e->nib_identitas;
            }
            if (! is_null($e->nomor_identitas)) {
                $entitasItem['nomorIdentitas'] = $e->nomor_identitas;
            }
            $entitasItem['seriEntitas'] = (int) $e->id; // Always include seriEntitas
            if (! is_null($e->kode_negara)) {
                $entitasItem['kodeNegara'] = $e->kode_negara;
            }

            return $entitasItem;
        })->toArray();

        // kemasan
        $result['kemasan'] = $kemasans->map(function ($k) {
            return [
                'jumlahKemasan' => (int) $k->jumlah_kemasan ?? 0,
                'kodeJenisKemasan' => $k->kode_kemasan ?? null,
                'merkKemasan' => $k->merek ?? $k->merk ?? null,
                'seriKemasan' => (int) $k->seri,
            ];
        })->toArray();

        // kontainer (petikemas)
        $result['kontainer'] = $petikemas->map(function ($p) {
            return [
                'kodeJenisKontainer' => $p->kode_jenis_kontainer ?? null,
                'kodeTipeKontainer' => $p->kode_tipe_kontainer ?? null,
                'kodeUkuranKontainer' => $p->kode_ukuran_kontainer ?? null,
                'nomorKontainer' => $p->nomor_kontainer ?? null,
                'seriKontainer' => (int) $p->seri,
            ];
        })->toArray();

        // dokumen - map kode_fasilitas to human name using config/import.php
        $fasilitasMap = config('import.kode_fasilitas', []);
        $result['dokumen'] = $dokumens->map(function ($d) use ($fasilitasMap) {
            $kode = $d->kode_fasilitas ?? ($d->kodeFasilitas ?? '');
            $namaFasilitas = $kode !== null && $kode !== '' ? ($fasilitasMap[$kode] ?? null) : null;

            // Build base item with namaFasilitas inserted right after kodeFasilitas when available
            $item = [
                'idDokumen' => (string) $d->id,
                'kodeDokumen' => $d->kode_dokumen ?? null,
                'kodeFasilitas' => $d->kode_fasilitas ?? '',
            ];

            if ($namaFasilitas !== null) {
                $item['namaFasilitas'] = $namaFasilitas;
            }

            $item['nomorDokumen'] = $d->nomor_dokumen ?? null;
            $item['seriDokumen'] = (int) $d->seri;
            $item['tanggalDokumen'] = $d->tanggal_dokumen ?? null;

            return $item;
        })->toArray();

        // pengangkut
        $result['pengangkut'] = $pengangkuts->map(function ($p) {
            return [
                'kodeBendera' => $p->angkut_bendera ?? null,
                'namaPengangkut' => $p->angkut_nama ?? null,
                'nomorPengangkut' => $p->angkut_voy ?? null,
                'kodeCaraAngkut' => $p->angkut_cara ?? null,
                'seriPengangkut' => (int) $p->id,
            ];
        })->toArray();

        // TEMPORARILY COMMENTED OUT - API sending disabled for JSON inspection
        // Send data to API according to documentation:
        // Query Parameters: isFinal=false (boolean, default=false for draft)
        // Headers: Authorization=Bearer {token}
        // Request Body: Data Pabean (string) containing JSONSchema Dokumen Pabean
        $apiUrl = config('services.document_api.url');
        $bearerToken = \App\Services\PelabuhanApiService::getToken();

        if (! $bearerToken) {
            return response()->json([
                'error' => 'No valid API token found. Please authenticate first.',
            ], 401);
        }

        try {
            // The API expects the raw JSON object directly as the request body
            // not wrapped in a "Data Pabean" field or double-encoded

            // Log the data being sent for debugging
            \Log::info('Sending data to API:', ['data' => $result]);

            // Send POST request to API with query parameter isFinal=false and JSON body
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$bearerToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl.'/openapi/document?isFinal=false', $result);

            // Log the response for debugging
            \Log::info('API Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data sent to API successfully',
                    'api_response' => $response->json(),
                    'data' => $result,
                ], 200, [], JSON_PRETTY_PRINT);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed',
                    'status_code' => $response->status(),
                    'api_response' => $response->json(),
                    'data' => $result,
                ], $response->status(), [], JSON_PRETTY_PRINT);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending data to API: '.$e->getMessage(),
                'data' => $result,
            ], 500, [], JSON_PRETTY_PRINT);
        }

        // TEMPORARY: Return JSON data for inspection
        // return response()->json([
        //     'message' => 'JSON format inspection - API sending is commented out',
        //     'data_pabean_json' => json_encode($result),
        //     'data_pabean_object' => $result,
        //     'api_request_body_would_be' => [
        //         'Data Pabean' => json_encode($result),
        //     ],
        // ], 200, [], JSON_PRETTY_PRINT);
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
        $entitas_importir = ImportEntitas::where('import_notification_id', null)->where('kode_entitas', 1)->first();
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

        return view('import.create', compact('header', 'entitas_importir', 'transaksi', 'dokument', 'entitas_pengirim', 'entitas_penjual', 'pengangkut', 'kemasan', 'petiKemas', 'barangData', 'BM', 'PPN', 'PPH', 'total'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $steps = ['header', 'entitas', 'dokumen', 'pengangkut', 'kemasan', 'transaksi', 'barang', 'pungutan', 'pernyataan'];
        $currentStep = $request->input('current_step', 'header');
        $action = $request->input('action', 'submit');
        foreach ($steps as $step) {
            if ($request->has($step)) {
                $draft[$step] = $request->input($step);
            }
        }
        if ($action === 'save_continue') {
            $currentIndex = array_search($currentStep, $steps, true);
            $nextStep = $currentIndex < count($steps) - 1 ? $steps[$currentIndex + 1] : $steps[0];
            if ($currentStep === 'header') {
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
                session(['nomor_aju' => $draft['header']['nomor_aju']]);
            }
            // Handle entitas step saving: create or update parties and entitas
            if ($currentStep === 'entitas') {
                $no_aju = session('nomor_aju');
                $cek_input = ImportEntitas::where('no_aju', $no_aju)->get();
                if ($cek_input) {
                    $entitas_importir = ImportEntitas::where('no_aju', $no_aju)->where('kode_entitas', 1)->first();
                    $entitas_importir->update([
                        'user_id' => Auth::user()->id ?? 1,
                        'kode_entitas' => $request->input('kode_entitas'),
                        'kode_jenis_identitas' => $request->input('kode_jenis_identitas'),
                        'nomor_identitas' => $request->input('importir')['nomor_identitas'],
                        'nama_identitas' => $request->input('importir')['nama_identitas'],
                        'alamat_identitas' => $request->input('importir')['alamat_identitas'],
                        'nib_identitas' => $request->input('importir')['nib_identitas'],
                        'kode_jenis_api' => 02,
                        'kode_status' => $request->input('importir')['kode_status'],
                        'no_aju' => $no_aju,
                    ]);
                    $entitas_pemusatan = ImportEntitas::where('no_aju', $no_aju)->where('kode_entitas', 11)->first();
                    $entitas_pemusatan->update([
                        'user_id' => Auth::user()->id ?? 1,
                        'kode_entitas' => $request->input('kode_entitas2'),
                        'kode_jenis_identitas' => $request->input('kode_jenis_identitas2'),
                        'nomor_identitas' => $request->input('pemusatan')['nomor_identitas2'],
                        'nama_identitas' => $request->input('pemusatan')['nama_identitas2'],
                        'alamat_identitas' => $request->input('pemusatan')['alamat_identitas2'],
                        'kode_jenis_api' => 02,
                        'kode_status' => $request->input('importir')['kode_status'],
                        'no_aju' => $no_aju,
                    ]);
                    $entitas_pemilik = ImportEntitas::where('no_aju', $no_aju)->where('kode_entitas', 7)->first();
                    $entitas_pemilik->update([
                        'user_id' => Auth::user()->id ?? 1,
                        'kode_entitas' => $request->input('kode_entitas3'),
                        'kode_jenis_identitas' => $request->input('kode_jenis_identitas3'),
                        'nomor_identitas' => $request->input('pemilik')['nomor_identitas3'],
                        'nama_identitas' => $request->input('pemilik')['nama_identitas3'],
                        'alamat_identitas' => $request->input('pemilik')['alamat_identitas3'],
                        'kode_jenis_api' => 02,
                        'kode_status' => $request->input('importir')['kode_status'],
                        'no_aju' => $no_aju,
                    ]);
                    $entitas_pengirim = ImportEntitas::where('no_aju', $no_aju)->where('kode_entitas', 9)->first();
                    $entitas_pengirim->update([
                        'user_id' => Auth::user()->id ?? 1,
                        'kode_entitas' => $request->input('kode_entitas4'),
                        'nama_identitas' => $request->input('pengirim')['nama_identitas4'],
                        'alamat_identitas' => $request->input('pengirim')['alamat_identitas4'],
                        'kode_negara' => $request->input('pengirim')['kode_negara'],
                        'no_aju' => $no_aju,
                    ]);
                    $entitas_penjual = ImportEntitas::where('no_aju', $no_aju)->where('kode_entitas', 10)->first();
                    $entitas_penjual->update([
                        'user_id' => Auth::user()->id ?? 1,
                        'kode_entitas' => $request->input('kode_entitas5'),
                        'nama_identitas' => $request->input('penjual')['nama_identitas5'],
                        'alamat_identitas' => $request->input('penjual')['alamat_identitas5'],
                        'kode_negara' => $request->input('penjual')['kode_negara2'],
                        'no_aju' => $no_aju,
                    ]);
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
                        'no_aju' => $no_aju,
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
                        'no_aju' => $no_aju,
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
                        'no_aju' => $no_aju,
                    ]);
                    $entitas_pengirim = ImportEntitas::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_entitas' => $request->input('kode_entitas4'),
                        'nama_identitas' => $request->input('pengirim')['nama_identitas4'],
                        'alamat_identitas' => $request->input('pengirim')['alamat_identitas4'],
                        'kode_negara' => $request->input('pengirim')['kode_negara'],
                        'no_aju' => $no_aju,
                    ]);
                    $entitas_penjual = ImportEntitas::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'kode_entitas' => $request->input('kode_entitas5'),
                        'nama_identitas' => $request->input('penjual')['nama_identitas5'],
                        'alamat_identitas' => $request->input('penjual')['alamat_identitas5'],
                        'kode_negara' => $request->input('penjual')['kode_negara2'],
                        'no_aju' => $no_aju,
                    ]);
                }
            }
            // Handle pengangkut step saving
            if ($currentStep === 'pengangkut') {
                $existingPengangkut = ImportPengangkut::where('no_aju', session('nomor_aju'))
                    ->first();
                if ($existingPengangkut) {
                    $existingPengangkut->update([
                        'user_id' => Auth::user()->id ?? 1,
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
                        'no_aju' => session('nomor_aju'),
                    ]);
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
                        'no_aju' => session('nomor_aju'),
                    ]);
                }
            }
            // Handle transaksi step saving
            if ($currentStep === 'transaksi') {
                $existingTransaksi = ImportTransaksi::where('no_aju', session('nomor_aju'))
                    ->first();
                if ($existingTransaksi) {
                    $existingTransaksi->update([
                        'user_id' => Auth::user()->id ?? 1,
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
                        'no_aju' => session('nomor_aju'),
                    ]);
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
                        'no_aju' => session('nomor_aju'),
                    ]);
                }
            }
            if ($currentStep === 'pungutan') {
                $existingPungutan = ImportPungutan::where('no_aju', session('nomor_aju'))
                    ->first();
                if ($existingPungutan) {
                    $existingPungutan->update([
                        'user_id' => Auth::user()->id ?? 1,
                        'bea_masuk' => $request->input('bea_masuk'),
                        'ppn' => $request->input('ppn'),
                        'pph' => $request->input('pph'),
                        'total_pungutan' => $request->input('total'),
                    ]);
                    $savedPungutan = $existingPungutan;
                } else {
                    ImportPungutan::create([
                        'user_id' => Auth::user()->id ?? 1,
                        'import_notification_id' => null,
                        'no_aju' => session('nomor_aju'),
                        'bea_masuk' => $request->input('bea_masuk'),
                        'ppn' => $request->input('ppn'),
                        'pph' => $request->input('pph'),
                        'total_pungutan' => $request->input('total'),
                    ]);
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
                'kode_kondisi_barang' => 'kode_kondisi_barang',
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
            try {
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
                    'no_aju' => session('nomor_aju'),
                ]);
            } catch (\Throwable $th) {
                dd($th);
            }
            // Clear the session draft after successful submission
            session()->forget('nomor_aju');
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
        $header = ImportHeader::where('import_notification_id', $id)->get();
        $entitas_importir = ImportEntitas::where('import_notification_id', $id)->where('kode_entitas', 1)->first();
        $entitas_pengirim = ImportEntitas::where('import_notification_id', $id)->where('kode_entitas', 9)->get();
        $entitas_penjual = ImportEntitas::where('import_notification_id', $id)->where('kode_entitas', 10)->get();
        $dokument = ImportDokumen::where('import_notification_id', $id)->get();
        $kemasan = ImportKemasan::where('import_notification_id', $id)->get();
        $petiKemas = ImportPetiKemas::where('import_notification_id', $id)->get();
        $pengangkut = ImportPengangkut::where('import_notification_id', $id)->get();
        $transaksi = ImportTransaksi::where('import_notification_id', $id)->get();
        $barangData = ImportBarang::where('import_notification_id', $id)->get();
        $pungutan = ImportPungutan::where('import_notification_id', $id)->get();
        $pernyataan = ImportPernyataan::where('import_notification_id', $id)->get();
        $BM = $pungutan->first()->bea_masuk ?? 0;
        $PPN = $pungutan->first()->ppn ?? 0;
        $PPH = $pungutan->first()->pph ?? 0;
        $total = $BM + $PPN + $PPH;

        return view('import.edit', compact('header', 'entitas_importir', 'entitas_pengirim', 'entitas_penjual', 'dokument', 'kemasan', 'petiKemas', 'barangData', 'importNotification', 'pengangkut', 'transaksi', 'pungutan', 'pernyataan', 'BM', 'PPN', 'PPH', 'total'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImportNotification $importNotification)
    {
        $steps = ['header', 'entitas', 'dokumen', 'pengangkut', 'kemasan', 'transaksi', 'barang', 'pungutan', 'pernyataan'];
        $currentStep = $request->input('current_step', 'header');
        $action = $request->input('action', 'submit');
        foreach ($steps as $step) {
            if ($request->has($step)) {
                $draft[$step] = $request->input($step);
            }
        }
        // Store updated draft in session
        $currentIndex = array_search($currentStep, $steps, true);
        $nextStep = $currentIndex < count($steps) - 1 ? $steps[$currentIndex + 1] : $steps[0];
        if ($action === 'save_continue') {
            if ($currentStep == 'header') {
                $header = ImportHeader::where('import_notification_id', $importNotification->id)->first();
                $header->update($request->input('header'));
                session(['nomor_aju' => $header->nomor_aju]);
            }
            if ($currentStep == 'entitas') {
                $entitas_importir = ImportEntitas::where('no_aju', session('nomor_aju'))->where('kode_entitas', 1)->first();
                $entitas_importir->update([
                    'user_id' => Auth::user()->id ?? 1,
                    'kode_entitas' => $request->input('kode_entitas'),
                    'kode_jenis_identitas' => $request->input('kode_jenis_identitas'),
                    'nomor_identitas' => $request->input('importir')['nomor_identitas'],
                    'nama_identitas' => $request->input('importir')['nama_identitas'],
                    'alamat_identitas' => $request->input('importir')['alamat_identitas'],
                    'nib_identitas' => $request->input('importir')['nib_identitas'],
                    'kode_jenis_api' => 02,
                    'kode_status' => $request->input('importir')['kode_status'],
                ]);
                $entitas_pemusatan = ImportEntitas::where('no_aju', session('nomor_aju'))->where('kode_entitas', 11)->first();
                $entitas_pemusatan->update([
                    'user_id' => Auth::user()->id ?? 1,
                    'kode_entitas' => $request->input('kode_entitas2'),
                    'kode_jenis_identitas' => $request->input('kode_jenis_identitas2'),
                    'nomor_identitas' => $request->input('pemusatan')['nomor_identitas2'],
                    'nama_identitas' => $request->input('pemusatan')['nama_identitas2'],
                    'alamat_identitas' => $request->input('pemusatan')['alamat_identitas2'],
                    'kode_jenis_api' => 02,
                    'kode_status' => $request->input('importir')['kode_status'],
                ]);
                $entitas_pemilik = ImportEntitas::where('no_aju', session('nomor_aju'))->where('kode_entitas', 7)->first();
                $entitas_pemilik->update([
                    'user_id' => Auth::user()->id ?? 1,
                    'kode_entitas' => $request->input('kode_entitas3'),
                    'kode_jenis_identitas' => $request->input('kode_jenis_identitas3'),
                    'nomor_identitas' => $request->input('pemilik')['nomor_identitas3'],
                    'nama_identitas' => $request->input('pemilik')['nama_identitas3'],
                    'alamat_identitas' => $request->input('pemilik')['alamat_identitas3'],
                    'kode_jenis_api' => 02,
                    'kode_status' => $request->input('importir')['kode_status'],
                ]);
                $entitas_pengirim = ImportEntitas::where('no_aju', session('nomor_aju'))->where('kode_entitas', 9)->first();
                $entitas_pengirim->update([
                    'user_id' => Auth::user()->id ?? 1,
                    'kode_entitas' => $request->input('kode_entitas4'),
                    'nama_identitas' => $request->input('pengirim')['nama_identitas4'],
                    'alamat_identitas' => $request->input('pengirim')['alamat_identitas4'],
                    'kode_negara' => $request->input('pengirim')['kode_negara'],
                ]);
                $entitas_penjual = ImportEntitas::where('no_aju', session('nomor_aju'))->where('kode_entitas', 10)->first();
                $entitas_penjual->update([
                    'user_id' => Auth::user()->id ?? 1,
                    'kode_entitas' => $request->input('kode_entitas5'),
                    'nama_identitas' => $request->input('penjual')['nama_identitas5'],
                    'alamat_identitas' => $request->input('penjual')['alamat_identitas5'],
                    'kode_negara' => $request->input('penjual')['kode_negara2'],
                ]);

            }
            if ($currentStep === 'pengangkut') {
                $existingPengangkut = ImportPengangkut::where('no_aju', session('nomor_aju'))
                    ->first();
                $existingPengangkut->update([
                    'user_id' => Auth::user()->id ?? 1,
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
            if ($currentStep === 'transaksi') {
                $transaksi = ImportTransaksi::where('no_aju', session('nomor_aju'))
                    ->first();
                $transaksi->update([
                    'user_id' => Auth::user()->id ?? 1,
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
            }
            if ($currentStep === 'pungutan') {
                $pungutan = ImportPungutan::where('no_aju', session('nomor_aju'))
                    ->first();
                $pungutan->update([
                    'user_id' => Auth::user()->id ?? 1,
                    'bea_masuk' => $request->input('bea_masuk'),
                    'ppn' => $request->input('ppn'),
                    'pph' => $request->input('pph'),
                    'total_pungutan' => $request->input('total'),
                ]);
            }
            if ($currentStep === 'pernyataan') {
                $pernyataan = ImportPernyataan::where('no_aju', session('nomor_aju'))
                    ->first();
                $pernyataan->update([
                    'user_id' => Auth::user()->id ?? 1,
                    'nama_pernyataan' => $request->input('nama_pernyataan'),
                    'jabatan_pernyataan' => $request->input('jabatan_pernyataan'),
                    'kota_pernyataan' => $request->input('kota_pernyataan'),
                    'tanggal_pernyataan' => $request->input('tanggal_pernyataan'),
                ]);
            }

            return redirect()->route('import.edit', [$importNotification, 'step' => $nextStep])
                ->with('success', ucfirst($currentStep).' data saved successfully. Please continue with the next step.');
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
        if ($action == 'submit') {
            return redirect()->route('import.index')->with('success', 'PIB Updated successfully.');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImportNotification $importNotification)
    {
        try {
            // Delete related records first to maintain referential integrity
            ImportBarang::where('import_notification_id', $importNotification->id)->delete();
            ImportBarangDokumen::where('import_notification_id', $importNotification->id)->delete();
            ImportBarangTarif::where('import_notification_id', $importNotification->id)->delete();
            ImportBarangVd::where('import_notification_id', $importNotification->id)->delete();
            ImportDokumen::where('import_notification_id', $importNotification->id)->delete();
            ImportEntitas::where('import_notification_id', $importNotification->id)->delete();
            ImportHeader::where('import_notification_id', $importNotification->id)->delete();
            ImportKemasan::where('import_notification_id', $importNotification->id)->delete();
            ImportPengangkut::where('import_notification_id', $importNotification->id)->delete();
            ImportPernyataan::where('import_notification_id', $importNotification->id)->delete();
            ImportPetiKemas::where('import_notification_id', $importNotification->id)->delete();
            ImportPungutan::where('import_notification_id', $importNotification->id)->delete();
            ImportTransaksi::where('import_notification_id', $importNotification->id)->delete();

            // Finally delete the main notification record
            $importNotification->delete();

            return redirect()->route('import.index')->with('success', 'Data pemberitahuan impor berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('import.index')->with('error', 'Terjadi kesalahan saat menghapus data: '.$e->getMessage());
        }
    }

    /**
     * Search pelabuhan from external API
     */
    public function searchPelabuhan(Request $request)
    {
        $query = $request->get('q', '');
        $apiUrl = config('services.pelabuhan_api.url');
        $apiToken = \App\Services\PelabuhanApiService::getToken();

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
        // Check if import_notification_id is provided in request
        $importNotificationId = $request->get('import_notification_id');

        // If not provided, try to get from session or detect from referer
        if (! $importNotificationId) {
            $referer = $request->headers->get('referer', '');
            if (preg_match('/\/import\/(\d+)\/edit/', $referer, $matches)) {
                $importNotificationId = $matches[1];
            }
        }

        // Get kode_kantor based on context
        if ($importNotificationId) {
            // Editing existing document - get kode_kantor from specific notification
            $header = ImportHeader::select('kode_kantor')
                ->where('import_notification_id', $importNotificationId)
                ->first();
        } else {
            // Creating new document - get kode_kantor from unlinked headers
            $header = ImportHeader::select('kode_kantor')
                ->where('import_notification_id', null)
                ->first();
        }

        if (! $header) {
            return response()->json(['status' => 'ERROR', 'message' => 'Header data not found']);
        }

        $kodeKantor = $header->kode_kantor;
        $searchTerm = $request->get('q', ''); // Get search term from request
        $apiUrl = config('services.pelabuhan_api.url');
        $apiToken = \App\Services\PelabuhanApiService::getToken();

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
        // Check if import_notification_id is provided in request
        $importNotificationId = $request->get('import_notification_id');

        // If not provided, try to get from session or detect from referer
        if (! $importNotificationId) {
            $referer = $request->headers->get('referer', '');
            if (preg_match('/\/import\/(\d+)\/edit/', $referer, $matches)) {
                $importNotificationId = $matches[1];
            }
        }

        // Get kode_kantor based on context
        if ($importNotificationId) {
            // Editing existing document - get kode_kantor from specific notification
            $header = ImportHeader::select('kode_kantor')
                ->where('import_notification_id', $importNotificationId)
                ->first();
        } else {
            // Creating new document - get kode_kantor from unlinked headers
            $header = ImportHeader::select('kode_kantor')
                ->where('import_notification_id', null)
                ->first();
        }

        if (! $header) {
            return response()->json(['status' => 'ERROR', 'message' => 'Header data not found']);
        }

        $kodeKantor = $header->kode_kantor;
        $searchTerm = $request->get('q', '');
        $apiUrl = config('services.pelabuhan_api.url');
        $apiToken = \App\Services\PelabuhanApiService::getToken();

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
        $apiToken = \App\Services\PelabuhanApiService::getToken();

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
