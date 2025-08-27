<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 8px;
        }

        h2 {
            font-size: 13px;
            margin: 12px 0 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
        }

        .muted {
            color: #666;
        }

        .right {
            text-align: right;
        }

        .no-border td,
        .no-border th {
            border: none;
        }
    </style>
</head>

<body>
    @php
        $h = $data->header ?? [];
        $e = $data->entitas ?? [];
        $d = $data->dokumen ?? [];
        $pg = $data->pengangkut ?? [];
        $km = $data->kemasan ?? [];
        $tr = $data->transaksi ?? [];
        $br = $data->barang ?? [];
        $pgtn = $data->pungutan ?? [];
        $pry = $data->pernyataan ?? [];
        $totalCif = collect($br['items'] ?? [])->sum(fn($i) => (float) ($i['nilai_cif'] ?? 0));
    @endphp

    <h1>Pemberitahuan Impor #{{ $data->id }}</h1>
    <table class="no-border">
        <tr>
            <td><strong>Nomor Aju</strong>: {{ $h['nomor_aju'] ?? '-' }}</td>
            <td><strong>Kantor Pabean</strong>: {{ $h['kantor_pabean'] ?? '-' }}</td>
        </tr>

        <tr>
            <td><strong>Nomor</strong>: {{ $h['nomor_dokumen'] ?? '-' }}</td>
            <td><strong>Tanggal</strong>: {{ $h['tanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Importir</strong>: {{ $e['importir_nama'] ?? '-' }}</td>
            <td><strong>NPWP</strong>: {{ $e['importir_npwp'] ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>PPJK</strong>: {{ $e['ppjk_nama'] ?? '-' }}</td>
            <td><strong>KPPBC</strong>: {{ $h['kppbc'] ?? '-' }}</td>
        </tr>
    </table>

    <h2>Dokumen</h2>
    <table>
        <tr>
            <th>Invoice No</th>
            <th>Invoice Tgl</th>
        </tr>
        <tr>
            <td>{{ $d['invoice_no'] ?? '-' }}</td>
            <td>{{ $d['invoice_tgl'] ?? '-' }}</td>
        </tr>
    </table>

    <h2>Pengangkut</h2>
    <table>
        <tr>
            <th>Moda</th>
            <th>Voy/Flight</th>
        </tr>
        <tr>
            <td>{{ $pg['moda'] ?? '-' }}</td>
            <td>{{ $pg['voy_flight'] ?? '-' }}</td>
        </tr>
    </table>

    <h2>Kemasan & Peti Kemas</h2>
    <table>
        <tr>
            <th>Jenis</th>
            <th>Jumlah</th>
        </tr>
        <tr>
            <td>{{ $km['jenis'] ?? '-' }}</td>
            <td>{{ $km['jumlah'] ?? '-' }}</td>
        </tr>
    </table>

    <h2>Transaksi</h2>
    <table>
        <tr>
            <th>Valuta</th>
            <th>CIF</th>
            <th>Incoterm</th>
        </tr>
        <tr>
            <td>{{ $tr['valuta'] ?? '-' }}</td>
            <td class="right">{{ number_format((float) ($tr['cif'] ?? 0), 2, ',', '.') }}</td>
            <td>{{ $tr['incoterm'] ?? '-' }}</td>
        </tr>
    </table>

    <h2>Barang</h2>
    <table>
        <thead>
            <tr>
                <th>HS</th>
                <th>Uraian</th>
                <th class="right">Qty</th>
                <th>Sat</th>
                <th class="right">Nilai CIF</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($br['items'] ?? [] as $it)
                <tr>
                    <td>{{ $it['hs'] ?? '' }}</td>
                    <td>{{ $it['uraian'] ?? '' }}</td>
                    <td class="right">{{ $it['qty'] ?? '' }}</td>
                    <td>{{ $it['sat'] ?? '' }}</td>
                    <td class="right">{{ number_format((float) ($it['nilai_cif'] ?? 0), 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="right"><strong>Total CIF</strong></td>
                <td class="right"><strong>{{ number_format($totalCif, 2, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <h2>Pungutan</h2>
    <table>
        <tr>
            <th>BM (%)</th>
            <th>PPN (%)</th>
            <th>PPh (%)</th>
        </tr>
        <tr>
            <td class="right">{{ $pgtn['bm'] ?? '0' }}</td>
            <td class="right">{{ $pgtn['ppn'] ?? '0' }}</td>
            <td class="right">{{ $pgtn['pph'] ?? '0' }}</td>
        </tr>
    </table>

    <h2>Pernyataan</h2>
    <table>
        <tr>
            <th>Dinyatakan oleh</th>
            <th>Tempat & Tanggal</th>
        </tr>
        <tr>
            <td>{{ $pry['declared_by'] ?? '-' }}</td>
            <td>{{ $pry['place_date'] ?? '-' }}</td>
        </tr>
    </table>

    <p class="muted">Status: {{ $data->status }}</p>
</body>

</html>
