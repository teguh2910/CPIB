@php
    $opsValuta = config('import.jenis_valuta');
    $opsJenis = config('import.jenis_transaksi');
    $opsIncoterm = config('import.incoterm');
    $opsAsuransi = config('import.jenis_asuransi');
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- ====== KOLOM KIRI ====== --}}
    <div class="space-y-4">
        {{-- HARGA --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Harga</h3>

            <form method="POST" action="{{ route('import.transaksi.update', $transaksi->id) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-3">
                    <div class="grid md:grid-cols-2 gap-3">
                        <x-field label="Jenis Valuta">
                            <select name="harga_valuta" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsValuta as $k => $v)
                                    <option value="{{ $k }}"
                                        {{ old('harga_valuta', $transaksi->harga_valuta) == $k ? 'selected' : '' }}>
                                        {{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="NDPBM">
                            <input type="number" step="0.0001" name="harga_ndpbm"
                                class="w-full border rounded px-3 py-2"
                                value="{{ old('harga_ndpbm', $transaksi->harga_ndpbm) }}" required>
                        </x-field>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <x-field label="Jenis Transaksi">
                            <select name="harga_jenis" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsJenis as $k => $v)
                                    <option value="{{ $k }}"
                                        {{ old('harga_jenis', $transaksi->harga_jenis) == $k ? 'selected' : '' }}>
                                        {{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="Incoterm">
                            <select name="harga_incoterm" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsIncoterm as $k => $v)
                                    <option value="{{ $k }}"
                                        {{ old('harga_incoterm', $transaksi->harga_incoterm) == $k ? 'selected' : '' }}>
                                        {{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <x-field label="Harga Barang">
                            <input type="number" step="0.01" name="harga_barang"
                                class="w-full border rounded px-3 py-2"
                                value="{{ old('harga_barang', $transaksi->harga_barang) }}" required>
                        </x-field>
                        <x-field label="Nilai Pabean (NDPBM × Harga)">
                            <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                                value="{{ number_format($transaksi->harga_nilai_pabean, 2, ',', '.') }}" readonly>
                        </x-field>
                    </div>
                </div>
        </div>

        {{-- BERAT --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Berat</h3>
            <div class="grid md:grid-cols-2 gap-3">
                <x-field label="Berat Kotor (KGM)">
                    <input type="number" step="0.001" name="berat_kotor" class="w-full border rounded px-3 py-2"
                        value="{{ old('berat_kotor', $transaksi->berat_kotor) }}" required>
                </x-field>
                <x-field label="Berat Bersih (KGM)">
                    <input type="number" step="0.001" name="berat_bersih" class="w-full border rounded px-3 py-2"
                        value="{{ old('berat_bersih', $transaksi->berat_bersih) }}" required>
                </x-field>
            </div>
        </div>
    </div>

    {{-- ====== KOLOM KANAN ====== --}}
    <div class="space-y-4">
        {{-- BIAYA LAINNYA --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Biaya Lainnya</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Biaya Penambah">
                        <input type="number" step="0.01" name="biaya_penambah"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_penambah', $transaksi->biaya_penambah) }}">
                    </x-field>
                    <x-field label="Biaya Pengurang">
                        <input type="number" step="0.01" name="biaya_pengurang"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_pengurang', $transaksi->biaya_pengurang) }}">
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Freight">
                        <input type="number" step="0.01" name="biaya_freight"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_freight', $transaksi->biaya_freight) }}">
                    </x-field>
                    <x-field label="Jenis Asuransi">
                        <select name="biaya_jenis_asuransi" class="w-full border rounded px-3 py-2" required>
                            @foreach ($opsAsuransi as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('biaya_jenis_asuransi', $transaksi->biaya_jenis_asuransi) == $k ? 'selected' : '' }}>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Amount Asuransi">
                        <input type="number" step="0.01" name="biaya_asuransi"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_asuransi', $transaksi->biaya_asuransi) }}">
                    </x-field>
                    <div class="grid grid-cols-1 gap-1">
                        <label class="text-sm">Voluntary Declaration</label>
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="biaya_voluntary_on" value="1"
                                    {{ old('biaya_voluntary_on', $transaksi->biaya_voluntary_on) ? 'checked' : '' }}>
                                <span class="text-sm">Aktif</span>
                            </label>
                            <input type="number" step="0.01" name="biaya_voluntary_amt" placeholder="Amount"
                                class="w-full border rounded px-3 py-2"
                                value="{{ old('biaya_voluntary_amt', $transaksi->biaya_voluntary_amt) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Tombol Aksi ===== --}}
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Transaksi
            </button>
            <a href="{{ route('import.transaksi.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Batal
            </a>
            <form method="POST" action="{{ route('import.transaksi.destroy', $transaksi->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus data transaksi ini?')">
                    Hapus
                </button>
            </form>
        </div>
        </form>
    </div>
</div>
</div>
