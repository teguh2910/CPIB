@php
    $opsValuta = config('import.jenis_valuta');
    $opsJenis = config('import.jenis_transaksi');
    $opsIncoterm = config('import.incoterm');
    $opsAsuransi = config('import.jenis_asuransi');
    $t = $transaksi[0] ?? [];
    $apiUrl = config('services.pelabuhan_api.url');
    $apiToken = config('services.pelabuhan_api.token');
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
                        <select name="kode_valuta" class="w-full border rounded px-3 py-2" required>
                            @foreach ($opsValuta as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('kode_valuta', $t['kode_valuta'] ?? 'USD') == $k ? 'selected' : 'USD' }}>
                                    {{ $k }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="NDPBM">
                        <input type="number" step="0.0001" name="ndpbm" class="w-full border rounded px-3 py-2"
                            value="{{ old('ndpbm', $t['ndpbm'] ?? '') }}" readonly required>
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Jenis Transaksi">
                        <select name="kode_jenis_nilai" class="w-full border rounded px-3 py-2" required>
                            @foreach ($opsJenis as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('kode_jenis_nilai', $t['kode_jenis_nilai'] ?? 'LAI') == $k ? 'selected' : 'LAI' }}>
                                    {{ $k }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Incoterm">
                        <select name="kode_incoterm" class="w-full border rounded px-3 py-2" required>
                            @foreach ($opsIncoterm as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('kode_incoterm', $t['kode_incoterm'] ?? 'CIF') == $k ? 'selected' : 'CIF' }}>
                                    {{ $k }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="CIF">
                        <input type="number" step="0.01" name="cif" class="w-full border rounded px-3 py-2"
                            value="{{ old('cif', $t['cif'] ?? '') }}" required>
                    </x-field>
                    <x-field label="Nilai Pabean (NDPBM × CIF)">
                        <input type="text" id="harga_nilai_pabean" name="harga_nilai_pabean"
                            class="w-full border rounded px-3 py-2 bg-gray-100"
                            value="{{ old('harga_nilai_pabean', $t['harga_nilai_pabean'] ?? '') }}" disabled>
                    </x-field>
                    <x-field label="FOB">
                        <input type="text" id='fob' name="fob"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('fob', $t['fob'] ?? '') }}">
                    </x-field>
                </div>
            </div>
        </div>

        {{-- BERAT --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Berat</h3>
            <div class="grid md:grid-cols-2 gap-3">
                <x-field label="Berat Kotor (KGM)">
                    <input type="number" step="0.001" name="bruto" class="w-full border rounded px-3 py-2"
                        value="{{ old('bruto', $t['bruto'] ?? '') }}" required>
                </x-field>
                <x-field label="Berat Bersih (KGM)">
                    <input type="number" step="0.001" name="netto" class="w-full border rounded px-3 py-2"
                        value="{{ old('netto', $t['netto'] ?? '') }}" required>
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
                        <input type="number" step="0.01" value='0' name="biaya_tambahan"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_tambahan', $t['biaya_tambahan'] ?? '') }}">
                    </x-field>
                    <x-field label="Biaya Pengurang">
                        <input type="number" step="0.01" value='0' name="biaya_pengurang"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('biaya_pengurang', $t['biaya_pengurang'] ?? '') }}">
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Freight">
                        <input type="number" step="0.01" value='0' name="freight"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('freight', $t['freight'] ?? '') }}">
                    </x-field>
                    <x-field label="Jenis Asuransi">
                        <select name="kode_asuransi" class="w-full border rounded px-3 py-2" required>
                            @foreach ($opsAsuransi as $k => $v)
                                <option value="{{ $k }}"
                                    {{ old('kode_asuransi', $t['kode_asuransi'] ?? 'NONE') == $k ? 'selected' : '' }}>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Amount Asuransi">
                        <input type="number" step="0.01" value='0' name="asuransi"
                            class="w-full border rounded px-3 py-2"
                            value="{{ old('asuransi', $t['asuransi'] ?? '') }}">
                    </x-field>
                    <div class="grid grid-cols-1 gap-1">
                        <label class="text-sm">Voluntary Declaration</label>
                        <div class="flex items-center gap-3">
                            <input type="number" step="0.01" value='0' name="vd"
                                placeholder="Amount" class="w-full border rounded px-3 py-2"
                                value="{{ old('vd', $t['vd'] ?? '') }}">
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
        const kodeValutaSelect = document.querySelector('select[name="kode_valuta"]');
        const ndpbmInput = document.querySelector('input[name="ndpbm"]');
        const cifInput = document.querySelector('input[name="cif"]');
        const nilaiPabeanInput = document.getElementById('harga_nilai_pabean');

        // Function to fetch kurs
        async function fetchKurs(kodeValuta) {
            if (!kodeValuta) return;

            try {
                const today = new Date().toISOString().split('T')[0]; // yyyy-mm-dd
                const response = await fetch(`/ajax/kurs?kode_valuta=${kodeValuta}&tanggal=${today}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.status === 'true' && data.data && data.data.length > 0) {
                    const nilaiKurs = data.data[0].nilaiKurs;
                    ndpbmInput.value = nilaiKurs;
                    calculateNilaiPabean(); // Recalculate nilai pabean after setting ndpbm
                } else {
                    console.error('Failed to fetch kurs:', data.message);
                }
            } catch (error) {
                console.error('Error fetching kurs:', error);
            }
        }

        // Function to calculate nilai pabean
        function calculateNilaiPabean() {
            const ndpbm = parseFloat(ndpbmInput.value) || 0;
            const cif = parseFloat(cifInput.value) || 0;

            // Only calculate if both values are present
            if (ndpbm > 0 && cif > 0) {
                const nilaiPabean = ndpbm * cif;

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
        if (kodeValutaSelect) {
            kodeValutaSelect.addEventListener('change', function() {
                fetchKurs(this.value);
            });
        }

        if (ndpbmInput && cifInput && nilaiPabeanInput) {
            ndpbmInput.addEventListener('input', calculateNilaiPabean);
            cifInput.addEventListener('input', calculateNilaiPabean);

            // Calculate on page load if both values exist and nilai pabean is empty
            if ((ndpbmInput.value || cifInput.value) && !nilaiPabeanInput.value) {
                calculateNilaiPabean();
            }
        }

        // Fetch kurs on page load if kode_valuta is set
        if (kodeValutaSelect && kodeValutaSelect.value && !ndpbmInput.value) {
            fetchKurs(kodeValutaSelect.value);
        }
    })();
</script>
