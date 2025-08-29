@extends('layouts.app')
@section('title', 'Detail Pemberitahuan Impor')

@section('content')
    @php
        $h = $importNotification->header ?? [];
        $e = $importNotification->entitas ?? [];
        $d = $importNotification->dokumen ?? [];
        $pg = $importNotification->pengangkut ?? [];
        $km = $importNotification->kemasan ?? [];
        $tr = $importNotification->transaksi ?? [];
        $br = $importNotification->barang ?? [];
        $pgtn = $importNotification->pungutan ?? [];
        $pry = $importNotification->pernyataan ?? [];
    @endphp

    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Detail Pemberitahuan Impor #{{ $importNotification->id }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('import.edit', $importNotification->id) }}" class="px-3 py-2 rounded border text-sm">Edit</a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Header</h2>
            <div class="text-sm">
                <div>Nomor Aju: {{ $importNotification->header['nomor_aju'] ?? '-' }}</div>
                <div>Nomor: {{ $h['nomor_dokumen'] ?? '-' }}</div>
                <div>Tanggal: {{ $h['tanggal'] ?? '-' }}</div>
                <div>KPPBC: {{ $h['kppbc'] ?? '-' }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Entitas</h2>
            <div class="text-sm">
                <div>NPWP: {{ $e['importir_npwp'] ?? '-' }}</div>
                <div>Importir: {{ $e['importir_nama'] ?? '-' }}</div>
                <div>PPJK: {{ $e['ppjk_nama'] ?? '-' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Dokumen</h2>
            <div class="text-sm">
                <div>Invoice No: {{ $d['invoice_no'] ?? '-' }}</div>
                <div>Invoice Tgl: {{ $d['invoice_tgl'] ?? '-' }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Pengangkut</h2>
            <div class="text-sm">
                <div>Moda: {{ $pg['moda'] ?? '-' }}</div>
                <div>Voy/Flight: {{ $pg['voy_flight'] ?? '-' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Kemasan & Peti Kemas</h2>
            <div class="text-sm">
                <div>Jenis: {{ $km['jenis'] ?? '-' }}</div>
                <div>Jumlah: {{ $km['jumlah'] ?? '-' }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Transaksi</h2>
            <div class="text-sm">
                <div>Valuta: {{ $tr['valuta'] ?? '-' }}</div>
                <div>CIF: {{ $tr['cif'] ?? '-' }}</div>
                <div>Incoterm: {{ $tr['incoterm'] ?? '-' }}</div>
            </div>
        </div>

        <div class="md:col-span-2 bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Barang</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">HS</th>
                            <th class="py-2">Uraian</th>
                            <th class="py-2">Qty</th>
                            <th class="py-2">Sat</th>
                            <th class="py-2">Nilai CIF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($br['items'] ?? [] as $it)
                            <tr class="border-b">
                                <td class="py-2">{{ $it['hs'] ?? '' }}</td>
                                <td class="py-2">{{ $it['uraian'] ?? '' }}</td>
                                <td class="py-2">{{ $it['qty'] ?? '' }}</td>
                                <td class="py-2">{{ $it['sat'] ?? '' }}</td>
                                <td class="py-2">{{ $it['nilai_cif'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @php
                $totalCif = collect($br['items'] ?? [])->sum(fn($i) => (float) ($i['nilai_cif'] ?? 0));
            @endphp
            <div class="mt-3 text-right font-semibold">Total CIF: {{ number_format($totalCif, 2) }}</div>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Pungutan</h2>
            <div class="text-sm">
                <div>BM: {{ $pgtn['bm'] ?? '0' }}%</div>
                <div>PPN: {{ $pgtn['ppn'] ?? '0' }}%</div>
                <div>PPh: {{ $pgtn['pph'] ?? '0' }}%</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Pernyataan</h2>
            <div class="text-sm">
                <div>Dinyatakan oleh: {{ $pry['declared_by'] ?? '-' }}</div>
                <div>Tempat & Tanggal: {{ $pry['place_date'] ?? '-' }}</div>
            </div>
        </div>
    </div>
@endsection
