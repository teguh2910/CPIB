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

<div class="space-y-6">
    {{-- Form Edit Barang --}}
    <div class="bg-gray-50 border rounded-xl p-4">
        <h3 class="font-semibold mb-3">Edit Barang - Seri {{ $barang->seri }}</h3>

        <form method="POST" action="{{ route('import.barang.update', $barang->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="ndpbm" value="{{ $ndpbm }}">
            <input type="hidden" name="seri" value="{{ $barang->seri }}">

            <div class="grid md:grid-cols-2 gap-5">
                {{-- ===== Informasi Dasar ===== --}}
                <div class="space-y-3">
                    <h4 class="font-medium">Informasi Dasar</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <x-field label="POS Tarif">
                            <input name="pos_tarif" value="{{ old('pos_tarif', $barang->pos_tarif) }}"
                                placeholder="HS Code" class="w-full border rounded px-3 py-2" required>
                        </x-field>

                        <div>
                            <label class="text-sm">Pernyataan Lartas</label>
                            <div class="flex gap-5 mt-1">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="lartas" value="1"
                                        {{ old('lartas', $barang->lartas) ? 'checked' : '' }} class="text-blue-600">
                                    <span>Ya</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="lartas" value="0"
                                        {{ old('lartas', $barang->lartas) ? '' : 'checked' }} class="text-blue-600">
                                    <span>Tidak</span>
                                </label>
                            </div>
                        </div>

                        <x-field label="Kode Barang">
                            <input name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}"
                                placeholder="Opsional" class="w-full border rounded px-3 py-2">
                        </x-field>

                        <x-field label="Uraian">
                            <textarea name="uraian" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('uraian', $barang->uraian) }}</textarea>
                        </x-field>

                        <x-field label="Spesifikasi Lain">
                            <select name="spesifikasi" class="w-full border rounded px-3 py-2">
                                <option value="">Pilih spesifikasi</option>
                                @foreach ($opsSpek as $key => $val)
                                    <option value="{{ $key }}"
                                        {{ old('spesifikasi', $barang->spesifikasi) == $key ? 'selected' : '' }}>
                                        {{ $val }}</option>
                                @endforeach
                            </select>
                        </x-field>

                        <x-field label="Kondisi Barang">
                            <select name="kondisi" class="w-full border rounded px-3 py-2">
                                <option value="">Pilih kondisi</option>
                                @foreach ($opsKond as $key => $val)
                                    <option value="{{ $key }}"
                                        {{ old('kondisi', $barang->kondisi) == $key ? 'selected' : '' }}>
                                        {{ $val }}</option>
                                @endforeach
                            </select>
                        </x-field>

                        <x-field label="Negara Asal">
                            <select name="negara_asal" class="w-full border rounded px-3 py-2" required>
                                <option value="">Pilih negara</option>
                                @foreach ($opsNegara as $key => $val)
                                    <option value="{{ $key }}"
                                        {{ old('negara_asal', $barang->negara_asal) == $key ? 'selected' : '' }}>
                                        {{ $val }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>
                </div>

                {{-- ===== Spesifikasi Teknis ===== --}}
                <div class="space-y-3">
                    <h4 class="font-medium">Spesifikasi Teknis</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <x-field label="Berat Bersih (Kg)">
                            <input type="number" step="0.001" name="berat_bersih"
                                value="{{ old('berat_bersih', $barang->berat_bersih) }}"
                                class="w-full border rounded px-3 py-2" required>
                        </x-field>

                        <div class="grid grid-cols-2 gap-2">
                            <x-field label="Jumlah">
                                <input type="number" step="0.0000001" name="jumlah"
                                    value="{{ old('jumlah', $barang->jumlah) }}"
                                    class="w-full border rounded px-3 py-2" required>
                            </x-field>
                            <x-field label="Satuan">
                                <select name="satuan" class="w-full border rounded px-3 py-2" required>
                                    <option value="">Pilih satuan</option>
                                    @foreach ($opsSatuan as $key => $val)
                                        <option value="{{ $key }}"
                                            {{ old('satuan', $barang->satuan) == $key ? 'selected' : '' }}>
                                            {{ $val }}</option>
                                    @endforeach
                                </select>
                            </x-field>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <x-field label="Jml Kemasan">
                                <input type="number" step="0.01" name="jml_kemasan"
                                    value="{{ old('jml_kemasan', $barang->jml_kemasan) }}"
                                    class="w-full border rounded px-3 py-2">
                            </x-field>
                            <x-field label="Jenis Kemasan">
                                <select name="jenis_kemasan" class="w-full border rounded px-3 py-2">
                                    <option value="">Pilih jenis</option>
                                    @foreach ($opsKemasan as $key => $val)
                                        <option value="{{ $key }}"
                                            {{ old('jenis_kemasan', $barang->jenis_kemasan) == $key ? 'selected' : '' }}>
                                            {{ $val }}</option>
                                    @endforeach
                                </select>
                            </x-field>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Nilai Pabean ===== --}}
            <div class="mt-6">
                <h4 class="font-medium mb-3">Nilai Pabean</h4>
                <div class="grid md:grid-cols-4 gap-3">
                    <x-field label="Nilai Barang (USD)">
                        <input type="number" step="0.01" name="nilai_barang"
                            value="{{ old('nilai_barang', $barang->nilai_barang) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </x-field>
                    <x-field label="FOB (USD)">
                        <input type="number" step="0.01" name="fob" value="{{ old('fob', $barang->fob) }}"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                    <x-field label="Freight (USD)">
                        <input type="number" step="0.01" name="freight"
                            value="{{ old('freight', $barang->freight) }}" class="w-full border rounded px-3 py-2">
                    </x-field>
                    <x-field label="Asuransi (USD)">
                        <input type="number" step="0.01" name="asuransi"
                            value="{{ old('asuransi', $barang->asuransi) }}" class="w-full border rounded px-3 py-2">
                    </x-field>
                </div>
            </div>

            {{-- ===== Perhitungan ===== --}}
            <div class="mt-6">
                <h4 class="font-medium mb-3">Perhitungan</h4>
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Harga Satuan (Rp)">
                        <input type="number" step="0.01" name="harga_satuan"
                            value="{{ old('harga_satuan', $barang->harga_satuan) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </x-field>
                    <x-field label="Nilai Pabean (Rp)">
                        <input type="number" step="0.01" name="nilai_pabean_rp"
                            value="{{ old('nilai_pabean_rp', $barang->nilai_pabean_rp) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </x-field>
                </div>
            </div>

            {{-- ===== Fasilitas ===== --}}
            <div class="mt-6">
                <h4 class="font-medium mb-3">Fasilitas</h4>
                <x-field label="Dokumen Fasilitas">
                    <select name="dokumen_fasilitas" class="w-full border rounded px-3 py-2">
                        <option value="">Pilih dokumen fasilitas</option>
                        @foreach ($dokList as $dok)
                            <option value="{{ $dok }}"
                                {{ old('dokumen_fasilitas', $barang->dokumen_fasilitas) == $dok ? 'selected' : '' }}>
                                {{ $dok }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>

            {{-- ===== Bea Masuk ===== --}}
            <div class="mt-6">
                <h4 class="font-medium mb-3">Bea Masuk</h4>
                <div class="grid md:grid-cols-3 gap-3">
                    <x-field label="Keterangan BM">
                        <select name="ket_bm" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih keterangan</option>
                            @foreach ($opsKetBM as $key => $val)
                                <option value="{{ $key }}"
                                    {{ old('ket_bm', $barang->ket_bm) == $key ? 'selected' : '' }}>{{ $val }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Tarif BM (%)">
                        <input type="number" step="0.01" name="tarif_bm"
                            value="{{ old('tarif_bm', $barang->tarif_bm) }}" class="w-full border rounded px-3 py-2">
                    </x-field>
                    <x-field label="Bayar BM (Rp)">
                        <input type="number" step="0.01" name="bayar_bm"
                            value="{{ old('bayar_bm', $barang->bayar_bm) }}" class="w-full border rounded px-3 py-2">
                    </x-field>
                </div>
            </div>

            {{-- ===== PPN ===== --}}
            <div class="mt-6">
                <h4 class="font-medium mb-3">PPN</h4>
                <div class="grid md:grid-cols-3 gap-3">
                    <x-field label="Tarif PPN">
                        <select name="ppn_tarif" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih tarif</option>
                            <option value="0" {{ old('ppn_tarif', $barang->ppn_tarif) == 0 ? 'selected' : '' }}>
                                0%</option>
                            <option value="11" {{ old('ppn_tarif', $barang->ppn_tarif) == 11 ? 'selected' : '' }}>
                                11%</option>
                            <option value="12" {{ old('ppn_tarif', $barang->ppn_tarif) == 12 ? 'selected' : '' }}>
                                12%</option>
                        </select>
                    </x-field>
                    <x-field label="Keterangan PPN">
                        <select name="ket_ppn" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih keterangan</option>
                            @foreach ($opsKetPPN as $key => $val)
                                <option value="{{ $key }}"
                                    {{ old('ket_ppn', $barang->ket_ppn) == $key ? 'selected' : '' }}>
                                    {{ $val }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Bayar PPN (Rp)">
                        <input type="number" step="0.01" name="bayar_ppn"
                            value="{{ old('bayar_ppn', $barang->bayar_ppn) }}"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                </div>
            </div>

            {{-- ===== PPh ===== --}}
            <div class="mt-6">
                <h4 class="font-medium mb-3">PPh</h4>
                <div class="grid md:grid-cols-3 gap-3">
                    <x-field label="Keterangan PPh">
                        <select name="ket_pph" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih keterangan</option>
                            @foreach ($opsKetPPH as $key => $val)
                                <option value="{{ $key }}"
                                    {{ old('ket_pph', $barang->ket_pph) == $key ? 'selected' : '' }}>
                                    {{ $val }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Tarif PPh (%)">
                        <input type="number" step="0.01" name="tarif_pph"
                            value="{{ old('tarif_pph', $barang->tarif_pph) }}"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                    <x-field label="Bayar PPh (Rp)">
                        <input type="number" step="0.01" name="bayar_pph"
                            value="{{ old('bayar_pph', $barang->bayar_pph) }}"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                </div>
            </div>

            {{-- ===== Tombol Aksi ===== --}}
            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Update Barang
                </button>
                <a href="{{ route('import.barang.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
