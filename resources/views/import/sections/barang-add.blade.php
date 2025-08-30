@extends('layouts.app')

@section('title', 'Tambah Barang')

@php
    $opsNegara = config('import.negara');
    $opsSatuan = config('import.satuan_barang');
    $opsKemasan = config('import.jenis_kemasan');
    $opsSpek = config('import.spesifikasi_lain');
    $opsKond = config('import.kondisi_barang');
    $opsKetBM = config('import.ket_bm');
    $opsKetPPN = config('import.ket_ppn');
    $opsKetPPH = config('import.ket_pph');

    // Get NDPBM from transaksi data
    $transaksiData = \App\Models\ImportTransaksi::where('user_id', auth()->id())->first();
    $ndpbm = $transaksiData ? $transaksiData->harga_ndpbm : 1;

    // Ambil daftar dokumen untuk dropdown fasilitas/lartas
    $dokumenData = \App\Models\ImportDokumen::where('user_id', auth()->id())->get();
    $dokList = $dokumenData
        ->map(function ($d) {
            $jenis = config('import.jenis_dokumen')[$d->jenis] ?? $d->jenis;
            $tgl = $d->tanggal ?? '';
            $no = $d->nomor ?? '';
            return trim("$jenis - $no ($tgl)");
        })
        ->values()
        ->all();
@endphp

@section('content')

    <div class="bg-white rounded-lg shadow-md">
        <!-- Form -->
        <form action="{{ route('barang.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="ndpbm" value="{{ $ndpbm }}">

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                    <h3 class="text-sm font-medium text-red-800 mb-2">Mohon perbaiki kesalahan berikut:</h3>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Content -->
            <div class="mb-6">
                {{-- ===== Informasi Dasar ===== --}}
                <div class="bg-gray-50 border rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Informasi Dasar</h3>

                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- Kolom Kiri --}}
                        <div class="space-y-4">
                            <x-field label="Seri">
                                <input name="seri" placeholder="Seri" value="{{ old('seri') }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                            </x-field>
                            <x-field label="POS Tarif / HS Code">
                                <input name="pos_tarif" placeholder="HS Code" value="{{ old('pos_tarif') }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                            </x-field>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pernyataan
                                    Lartas</label>
                                <div class="flex gap-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="lartas" value="1"
                                            {{ old('lartas') == '1' ? 'checked' : '' }}
                                            class="text-blue-600 focus:ring-blue-500" required>
                                        <span class="ml-2 text-sm text-gray-700">Ya</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="lartas" value="0"
                                            {{ old('lartas', '0') == '0' ? 'checked' : '' }}
                                            class="text-blue-600 focus:ring-blue-500" required>
                                        <span class="ml-2 text-sm text-gray-700">Tidak</span>
                                    </label>
                                </div>
                            </div>

                            <x-field label="Kode Barang">
                                <input name="kode_barang" value="{{ old('kode_barang') }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </x-field>

                            <x-field label="Uraian Jenis Barang">
                                <textarea name="uraian"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    rows="3" required>{{ old('uraian') }}</textarea>
                            </x-field>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Spesifikasi Lain">
                                    <input name="spesifikasi" value="{{ old('spesifikasi') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </x-field>
                                <x-field label="Kondisi Barang">
                                    <select name="kondisi"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($opsKond as $k => $v)
                                            <option value="{{ $k }}"
                                                @if ((string) old('kondisi') === (string) $k) selected @endif>
                                                {{ $v }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Negara Asal">
                                    <select name="negara_asal"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($opsNegara as $k => $v)
                                            <option value="{{ $k }}"
                                                @if ((string) old('negara_asal') === (string) $k) selected @endif>
                                                {{ $v }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                                <x-field label="Berat Bersih (Kg)">
                                    <input type="number" step="0.001" name="berat_bersih"
                                        value="{{ old('berat_bersih') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                </x-field>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Jumlah Satuan Barang">
                                    <input type="number" step="0.000001" name="jumlah" id="jumlah"
                                        value="{{ old('jumlah') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                </x-field>
                                <x-field label="Jenis Satuan">
                                    <select name="satuan"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($opsSatuan as $k => $v)
                                            <option value="{{ $k }}"
                                                @if ((string) old('satuan') === (string) $k) selected @endif>
                                                {{ $v }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Jumlah Kemasan">
                                    <input type="number" step="0.000001" name="jumlah_kemasan"
                                        value="{{ old('jumlah_kemasan') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                </x-field>
                                <x-field label="Jenis Kemasan">
                                    <select name="jenis_kemasan"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($opsKemasan as $k => $v)
                                            <option value="{{ $k }}"
                                                @if ((string) old('jenis_kemasan') === (string) $k) selected @endif>
                                                {{ $v }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Nilai Barang">
                                    <input type="number" step="0.01" name="nilai_barang" id="nilai_barang"
                                        value="{{ old('nilai_barang') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </x-field>
                                <x-field label="FOB">
                                    <input type="number" step="0.01" name="fob" value="{{ old('fob') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </x-field>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Freight">
                                    <input type="number" step="0.01" name="freight"
                                        value="{{ old('freight') ?? '0' }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </x-field>
                                <x-field label="Asuransi">
                                    <input type="number" step="0.01" name="asuransi"
                                        value="{{ old('asuransi') ?? '0' }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </x-field>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Harga Satuan">
                                    <input type="number" step="0.01" name="harga_satuan" id="harga_satuan"
                                        value="{{ old('harga_satuan') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                                        readonly>
                                </x-field>
                                <x-field label="Dokumen Fasilitas">
                                    <select name="dokumen_fasilitas"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih Dokumen --</option>
                                        @foreach ($dokument as $dok)
                                            <option value="{{ $dok }}"
                                                @if (old('dokumen_fasilitas') === $dok) selected @endif>
                                                {{ $dok }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Pungutan ===== --}}
                <div class="bg-gray-50 border rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Pungutan</h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        {{-- BM (Bea Masuk) --}}
                        <div class="bg-white border border-gray-300 rounded-lg p-4 space-y-4">
                            <h4 class="text-md font-medium text-gray-800">BM (Bea Masuk)</h4>
                            <x-field label="% Tarif">
                                <input type="number" step="0.01" name="tarif_bm"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </x-field>
                            <x-field label="">
                                <select name="status_bm"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1">Dibayar</option>
                                    <option value="2">Tidak Dibayar</option>
                                    <option value="3">Bebas</option>
                                </select>
                            </x-field>
                            <x-field label="(%)">
                                <input type="number" value="100" step="0.01" name="ket_bm"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </x-field>
                        </div>

                        {{-- PPN --}}
                        <div class="bg-white border border-gray-300 rounded-lg p-4 space-y-4">
                            <h4 class="text-md font-medium text-gray-800">PPN</h4>
                            <x-field label="% Tarif (11% / 12%)">
                                <select name="tarif_ppn"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="0">0</option>
                                    <option value="11" selected>11</option>
                                    <option value="12">12</option>
                                </select>
                            </x-field>
                            <x-field label="">
                                <select name="status_ppn"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1">Dibayar</option>
                                    <option value="2">Tidak Dibayar</option>
                                    <option value="3">Bebas</option>
                                </select>
                            </x-field>
                            <x-field label="(%)">
                                <input type="number" value="100" step="0.01" name="ket_ppn"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </x-field>
                        </div>

                        {{-- PPh --}}
                        <div class="bg-white border border-gray-300 rounded-lg p-4 space-y-4">
                            <h4 class="text-md font-medium text-gray-800">PPh</h4>
                            <x-field label="% Tarif">
                                <input type="number" step="0.01" name="tarif_pph" value="2.5"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </x-field>
                            <x-field label="">
                                <select name="status_pph"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1">Dibayar</option>
                                    <option value="2">Tidak Dibayar</option>
                                    <option value="3">Bebas</option>
                                </select>
                            </x-field>
                            <x-field label="(%)">
                                <input type="number" value="100" step="0.01" name="ket_pph"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </x-field>
                        </div>
                    </div>
                </div>

                {{-- ===== Actions ===== --}}
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                        Simpan Barang
                    </button>
                    <a href="{{ route('import.create', ['step' => 'barang']) }}"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nilaiBarangInput = document.getElementById('nilai_barang');
            const jumlahInput = document.getElementById('jumlah');
            const hargaSatuanInput = document.getElementById('harga_satuan');

            function calculateHargaSatuan() {
                const nilaiBarang = parseFloat(nilaiBarangInput.value) || 0;
                const jumlah = parseFloat(jumlahInput.value) || 0;

                if (jumlah > 0) {
                    const hargaSatuan = nilaiBarang / jumlah;
                    hargaSatuanInput.value = hargaSatuan.toFixed(2);
                } else {
                    hargaSatuanInput.value = '';
                }
            }

            // Calculate on input change
            nilaiBarangInput.addEventListener('input', calculateHargaSatuan);
            jumlahInput.addEventListener('input', calculateHargaSatuan);

            // Calculate on page load if values exist
            calculateHargaSatuan();
        });
    </script>
@endsection
