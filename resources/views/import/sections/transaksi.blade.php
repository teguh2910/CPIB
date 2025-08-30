@php
    $opsValuta = config('import.jenis_valuta');
    $opsJenis = config('import.jenis_transaksi');
    $opsIncoterm = config('import.incoterm');
    $opsAsuransi = config('import.jenis_asuransi');
    $t = $draft ?? [];
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- ====== KOLOM KIRI ====== --}}
    <div class="space-y-4">
        {{-- HARGA --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Harga</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Jenis Valuta">
                        <select name="harga_valuta" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($opsValuta as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('harga_valuta', $t['harga_valuta'] ?? '') == $k ? 'selected' : '' }}>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="NDPBM">
                        <input type="number" step="0.0001" name="harga_ndpbm" class="w-full border rounded px-3 py-2"
                            value="{{ old('harga_ndpbm', $t['harga_ndpbm'] ?? '') }}" required>
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Jenis Transaksi">
                        <select name="harga_jenis" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($opsJenis as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('harga_jenis', $t['harga_jenis'] ?? '') == $k ? 'selected' : '' }}>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Incoterm">
                        <select name="harga_incoterm" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($opsIncoterm as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('harga_incoterm', $t['harga_incoterm'] ?? '') == $k ? 'selected' : '' }}>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Harga Barang">
                        <input type="number" step="0.01" name="harga_barang" class="w-full border rounded px-3 py-2"
                            value="{{ old('harga_barang', $t['harga_barang'] ?? '') }}" required>
                    </x-field>
                    <x-field label="Nilai Pabean (NDPBM × Harga)">
                        <input type="text" id="harga_nilai_pabean" name="harga_nilai_pabean"
                            class="w-full border rounded px-3 py-2 bg-gray-100"
                            value="{{ old('harga_nilai_pabean', $t['harga_nilai_pabean'] ?? '') }}" readonly>
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
                        value="{{ old('berat_kotor', $t['berat_kotor'] ?? '') }}" required>
                </x-field>
                <x-field label="Berat Bersih (KGM)">
                    <input type="number" step="0.001" name="berat_bersih" class="w-full border rounded px-3 py-2"
                        value="{{ old('berat_bersih', $t['berat_bersih'] ?? '') }}" required>
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
                        <input type="number" step="0.01" value='0' name="biaya_penambah"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_penambah', $t['biaya_penambah'] ?? '') }}">
                    </x-field>
                    <x-field label="Biaya Pengurang">
                        <input type="number" step="0.01" value='0' name="biaya_pengurang"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_pengurang', $t['biaya_pengurang'] ?? '') }}">
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Freight">
                        <input type="number" step="0.01" value='0' name="biaya_freight"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_freight', $t['biaya_freight'] ?? '') }}">
                    </x-field>
                    <x-field label="Jenis Asuransi">
                        <select name="biaya_jenis_asuransi" class="w-full border rounded px-3 py-2" required>
                            @foreach ($opsAsuransi as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('biaya_jenis_asuransi', $t['biaya_jenis_asuransi'] ?? 'NONE') == $k ? 'selected' : '' }}>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Amount Asuransi">
                        <input type="number" step="0.01" value='0' name="biaya_asuransi"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_asuransi', $t['biaya_asuransi'] ?? '') }}">
                    </x-field>
                    <div class="grid grid-cols-1 gap-1">
                        <label class="text-sm">Voluntary Declaration</label>
                        <div class="flex items-center gap-3">
                            <input type="number" step="0.01" value='0' name="biaya_voluntary_amt"
                                placeholder="Amount" class="w-full border rounded px-3 py-2"
                                value="{{ old('biaya_voluntary_amt', $t['biaya_voluntary_amt'] ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    (function() {
        // Get form elements
        const ndpbmInput = document.querySelector('input[name="harga_ndpbm"]');
        const hargaBarangInput = document.querySelector('input[name="harga_barang"]');
        const nilaiPabeanInput = document.getElementById('harga_nilai_pabean');

        // Function to calculate nilai pabean
        function calculateNilaiPabean() {
            const ndpbm = parseFloat(ndpbmInput.value) || 0;
            const hargaBarang = parseFloat(hargaBarangInput.value) || 0;

            // Only calculate if both values are present
            if (ndpbm > 0 && hargaBarang > 0) {
                const nilaiPabean = ndpbm * hargaBarang;

                // Format as currency with 2 decimal places
                nilaiPabeanInput.value = nilaiPabean.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                // Clear the field if either value is missing or zero
                nilaiPabeanInput.value = '';
            }
        }

        // Add event listeners
        if (ndpbmInput && hargaBarangInput && nilaiPabeanInput) {
            ndpbmInput.addEventListener('input', calculateNilaiPabean);
            hargaBarangInput.addEventListener('input', calculateNilaiPabean);

            // Calculate on page load if both values exist and nilai pabean is empty
            if ((ndpbmInput.value || hargaBarangInput.value) && !nilaiPabeanInput.value) {
                calculateNilaiPabean();
            }
        }
    })();
</script>
