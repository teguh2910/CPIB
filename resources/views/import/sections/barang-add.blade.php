@extends('layouts.app')

@section('title', 'Tambah Barang')

@php
    $opsNegara = config('import.kode_negara');
    $opsSatuan = config('import.satuan_barang');
    $opsKemasan = config('import.kode_kemasan');
    $opsSpek = config('import.spesifikasi_lain');
    $opsKond = config('import.kondisi_barang');
    $opsKetBM = config('import.ket_bm');
    $opsKetPPN = config('import.ket_ppn');
    $opsKetPPH = config('import.ket_pph');    

@endphp

@section('content')

    <style>
        /* Custom Select2 styling to match Tailwind CSS */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: #ffffff;
            padding: 0.5rem 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            line-height: 1.5;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6b7280;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 10px;
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .select2-container--default .select2-results__option {
            padding: 0.5rem 0.75rem;
            color: #374151;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6;
            color: #ffffff;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            margin: 0.5rem;
            width: calc(100% - 1rem);
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
    </style>

    <div class="bg-white rounded-lg shadow-md">
        <!-- Form -->
        <form action="{{ route('barang.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="ndpbm" value="{{ $new_ndpbm }}">

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
                                <input name="seri_barang" placeholder="Seri" value="{{ old('seri') }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                            </x-field>
                            <x-field label="POS Tarif / HS Code">
                                <input name="hs" placeholder="HS Code" value="{{ old('hs') }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                            </x-field>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pernyataan
                                    Lartas</label>
                                <div class="flex gap-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="pernyataan_lartas" value="Y"
                                            {{ old('pernyataan_lartas') == '1' ? 'checked' : '' }}
                                            class="text-blue-600 focus:ring-blue-500" required>
                                        <span class="ml-2 text-sm text-gray-700">Ya</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="pernyataan_lartas" value="T"
                                            {{ old('pernyataan_lartas', '0') == '0' ? 'checked' : '' }}
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
                                    <select name="spesifikasi_lain"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach ($opsKond as $k => $v)
                                            <option value="{{ $k }}"
                                                @if ((string) old('spesifikasi_lain') === (string) $k) selected @endif>
                                                {{ $v }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                                <x-field label="Fasilitas">
                                    <select name="dok_fasilitas"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih Fasilitas --</option>
                                        @foreach ($dokumenData as $dok)
                                            <option value="{{ $dok->seri }}"
                                                @if (old('dok_fasilitas') === $dok->kode_dokumen) selected @endif>
                                                {{ 'Kode ' . $dok->kode_dokumen . ' - ' . ' Nomor ' . $dok->nomor_dokumen . ' (' . $dok->tanggal_dokumen->format('d-m-Y') . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                                <x-field label="Jenis Transaksi">
                                    <select name="kodeJenisVd"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Pilih Jenis Transaksi --</option>
                                        <option value="NTR" selected>NTR - TRANSAKSI JUAL BELI</option>
                                        <option value="BTR">BTR - BUKAN TRANSAKSI JUAL BELI</option>
                                        <option value="CAM">CAM - Barang terdiri dari barang-barang yang merupakan obyek
                                            transaksi gabungan dari dua atau lebih jenis transaksi 1 sampai dengan 10
                                        </option>
                                        <option value="CMA">CMA - BUKAN TRANSAKSI JUAL BELI BERUPA BARANG
                                            HADIAH/PROMOSI/CONTOH</option>
                                        <option value="FTR">FTR - TRANSAKSI JUAL BELI BERDASARKAN HARGA FUTURES (FUTURE
                                            PRICE), YAITU HARGA YANG BARU DAPAT DITENTUKAN SETELAH PIB DISAMPAIKAN</option>
                                        <option value="HBH">HBH - BUKAN TRANSAKSI JUAL BELI BERUPA BARANG BANTUAN/HIBAH
                                        </option>
                                        <option value="ITM">ITM - BUKAN TRANSAKSI JUAL BELI BERUPA BARANG YANG DIIMPOR
                                            OLEH INTERMEDIARY YANG TIDAK MEMBELI BARANG</option>
                                        <option value="KON">KON - BUKAN TRANSAKSI JUAL BELI BERUPA BARANG KONSINYASI
                                        </option>
                                        <option value="LES">LES - BUKAN TRANSAKSI JUAL BELI BERUPA BARANG SEWA (LEASING)
                                        </option>
                                        <option value="PRO">PRO - TRANSAKSI JUAL BELI MENGANDUNG PROCEEDS YANG NILAINYA
                                            BELUM DAPAT DITENTUKAN</option>
                                        <option value="ROY">ROY - TRANSAKSI JUAL BELI MENGANDUNG ROYALTI YANG NILAINYA
                                            BELUM DAPAT DITENTUKAN</option>
                                        <option value="TIT">TIT - TITIPAN</option>
                                    </select>
                                </x-field>
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Negara Asal">
                                    <select name="kode_negara_asal"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach ($opsNegara as $k => $v)
                                            <option value="{{ $k }}"
                                                @if ((string) old('kode_negara_asal') === (string) $k) selected @endif>
                                                {{ $v }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                                <x-field label="Berat Bersih (Kg)">
                                    <input type="number" step="0.001" name="netto" value="{{ old('netto') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                </x-field>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="Jumlah Satuan Barang">
                                    <input type="number" step="0.000001" name="jumlah_satuan" id="jumlah_satuan"
                                        value="{{ old('jumlah_satuan') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                </x-field>
                                <x-field label="Jenis Satuan">
                                    <select name="kode_satuan"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach ($opsSatuan as $k => $v)
                                            <option value="{{ $k }}"
                                                @if ((string) old('kode_satuan') === (string) $k) selected @endif>
                                                {{ $k }}
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
                                    <select name="kode_kemasan"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach ($opsKemasan as $k => $v)
                                            <option value="{{ $k }}"
                                                @if ((string) old('kode_kemasan') === (string) $k) selected @endif>
                                                {{ $k }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-field>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-field label="CIF">
                                    <input type="number" step="0.01" name="cif" id="cif"
                                        value="{{ old('cif') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </x-field>
                                <x-field label="CIF Rupiah">
                                    <input type="number" step="0.01" name="cif_rupiah" id="cif_rupiah"
                                        value="{{ old('cif_rupiah') }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </x-field>
                                <x-field label="NDPBM">
                                    <input type="number" step="0.01" name="ndpbm" id="ndpbm"
                                        value="{{ $new_ndpbm }}"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </x-field>
                                <input type="hidden" step="0.01" name="fob" value="0"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <x-field label="Harga Satuan">
                                    <input type="number" step="0.01" name="harga_satuan" id="harga_satuan"
                                        value="{{ old('harga_satuan') }}"
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

                            {{-- ===== Bea dan Pajak ===== --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                                <h4 class="text-md font-medium text-blue-800 mb-3">Bea Masuk</h4>
                                <div class="grid md:grid-cols-3 gap-4">
                                    <x-field label="BM(%)">
                                        <input type="number" value="0" step="0.01" name="bm"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Masukkan nilai BM">
                                    </x-field>
                                    <x-field label="PPN(%)">
                                        <input type="number" value="11" step="0.01" name="ppn"
                                            value="{{ old('ket_ppn') }}"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Masukkan nilai PPN">
                                    </x-field>
                                    <x-field label="PPH(%)">
                                        <input type="number" value="2.5" step="0.01" name="pph"
                                            value="{{ old('ket_pph') }}"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Masukkan nilai PPH">
                                    </x-field>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Actions ===== --}}
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                        Simpan Barang
                    </button>
                    <a href="javascript:history.back()"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-calculate CIF Rupiah
            const cifInput = document.getElementById('cif');
            const ndpbmInput = document.getElementById('ndpbm');
            const cifRupiahInput = document.getElementById('cif_rupiah');

            function calculateCifRupiah() {
                const cif = parseFloat(cifInput.value) || 0;
                const ndpbm = parseFloat(ndpbmInput.value) || 0;

                const cifRupiah = cif * ndpbm;
                cifRupiahInput.value = cifRupiah.toFixed(2);
            }

            // Calculate CIF Rupiah on input change
            if (cifInput) cifInput.addEventListener('input', calculateCifRupiah);
            if (ndpbmInput) ndpbmInput.addEventListener('input', calculateCifRupiah);

            // Calculate on page load if values exist
            calculateCifRupiah();

            // Auto-calculate Harga Satuan
            const jumlahSatuanInput = document.getElementById('jumlah_satuan');
            const hargaSatuanInput = document.getElementById('harga_satuan');

            function calculateHargaSatuan() {
                const cif = parseFloat(cifInput.value) || 0;
                const jumlahSatuan = parseFloat(jumlahSatuanInput.value) || 0;

                if (jumlahSatuan > 0) {
                    const hargaSatuan = cif / jumlahSatuan;
                    hargaSatuanInput.value = hargaSatuan.toFixed(2);
                } else {
                    hargaSatuanInput.value = '';
                }
            }

            // Calculate Harga Satuan on input change
            if (cifInput) cifInput.addEventListener('input', calculateHargaSatuan);
            if (jumlahSatuanInput) jumlahSatuanInput.addEventListener('input', calculateHargaSatuan);

            // Calculate on page load if values exist
            calculateHargaSatuan();
        });

        // Initialize Select2 for all select elements
        $(document).ready(function() {
            // Initialize Select2 for Negara Asal
            $('select[name="kode_negara_asal"]').select2({
                placeholder: '-- Pilih Negara Asal --',
                allowClear: true,
                width: '100%'
            });

            // Initialize Select2 for Spesifikasi Lain
            $('select[name="spesifikasi_lain"]').select2({
                placeholder: '-- Pilih Spesifikasi --',
                allowClear: true,
                width: '100%'
            });

            // Initialize Select2 for Jenis Satuan
            $('select[name="kode_satuan"]').select2({
                placeholder: '-- Pilih Satuan --',
                allowClear: true,
                width: '100%'
            });

            // Initialize Select2 for Jenis Kemasan
            $('select[name="kode_kemasan"]').select2({
                placeholder: '-- Pilih Kemasan --',
                allowClear: true,
                width: '100%'
            });

            // Initialize Select2 for Fasilitas
            $('select[name="dok_fasilitas"]').select2({
                placeholder: '-- Pilih Fasilitas --',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
