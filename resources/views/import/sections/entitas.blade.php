@php
    $e = $draft ?? [];
    $negaraOpts = config('import.negara');
    $pengirimSelected = $e['pengirim']['party_id'] ?? null;
    $penjualSelected = $e['penjual']['party_id'] ?? null;
    $statusOpts = config('import.status_importir');

    // Get parties for dropdown - handle potential errors
    try {
        $pengirimParties = \App\Models\Party::where('type', 'pengirim')->get();
        $penjualParties = \App\Models\Party::where('type', 'penjual')->get();
    } catch (\Exception $ex) {
        $pengirimParties = collect([]);
        $penjualParties = collect([]);
    }
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- ====== KOLOM KIRI ====== --}}
    <div class="space-y-4">
        {{-- IMPORTIR --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Importir</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="NPWP">
                        <input name="importir[npwp]" class="w-full border rounded px-3 py-2"
                            value="{{ old('importir.npwp', $e['importir']['npwp'] ?? '001065305305500') }}" required>
                    </x-field>
                    <x-field label="NITKU">
                        <input name="importir[nitku]" class="w-full border rounded px-3 py-2"
                            value="{{ old('importir.nitku', $e['importir']['nitku'] ?? '001065305305500000000') }}">
                    </x-field>
                </div>
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Nama">
                        <input name="importir[nama]" class="w-full border rounded px-3 py-2"
                            value="{{ old('importir.nama', $e['importir']['nama'] ?? 'AISIN INDONESIA') }}" required>
                    </x-field>
                    <x-field label="API/NIB">
                        <input name="importir[api_nib]" class="w-full border rounded px-3 py-2"
                            value="{{ old('importir.api_nib', $e['importir']['api_nib'] ?? '8120006810093') }}">
                    </x-field>
                </div>
                <x-field label="Alamat">
                    <input name="importir[alamat]" class="w-full border rounded px-3 py-2"
                        value="{{ old('importir.alamat', $e['importir']['alamat'] ?? 'KAWASAN INDUSTRI EJIP PLOT 5J 000/000 SUKARESMI, CIKARANG SELATAN, BEKASI, JAWA BARAT') }}"
                        required>
                </x-field>
                <x-field label="Status">
                    <select name="importir[status]" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Status --</option>
                        @foreach ($statusOpts as $k => $v)
                            <option value="{{ $k }}" @if ((string) old('importir.status', $e['importir']['status'] ?? 'MITA') === (string) $k) selected @endif>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </x-field>
            </div>
        </div>

        {{-- NPWP PEMUSATAN (opsional) --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">NPWP Pemusatan</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="NPWP">
                        <input name="pemusatan[npwp]" class="w-full border rounded px-3 py-2"
                            value="{{ old('pemusatan.npwp', $e['pemusatan']['npwp'] ?? '001065305305500') }}">
                    </x-field>
                    <x-field label="NITKU">
                        <input name="pemusatan[nitku]" class="w-full border rounded px-3 py-2"
                            value="{{ old('pemusatan.nitku', $e['pemusatan']['nitku'] ?? '001065305305500000000') }}">
                    </x-field>
                </div>
                <x-field label="Nama">
                    <input name="pemusatan[nama]" class="w-full border rounded px-3 py-2"
                        value="{{ old('pemusatan.nama', $e['pemusatan']['nama'] ?? 'AISIN INDONESIA') }}">
                </x-field>
                <x-field label="Alamat">
                    <input name="pemusatan[alamat]" class="w-full border rounded px-3 py-2"
                        value="{{ old('pemusatan.alamat', $e['pemusatan']['alamat'] ?? 'KAWASAN INDUSTRI EJIP PLOT 5J 000/000 SUKARESMI, CIKARANG SELATAN, BEKASI, JAWA BARAT') }}">
                </x-field>
            </div>
        </div>
    </div>

    {{-- ====== KOLOM KANAN ====== --}}
    <div class="space-y-4">
        {{-- PEMILIK BARANG (opsional) --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Pemilik Barang</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="NPWP">
                        <input name="pemilik[npwp]" class="w-full border rounded px-3 py-2"
                            value="{{ old('pemilik.npwp', $e['pemilik']['npwp'] ?? '001065305305500') }}">
                    </x-field>
                    <x-field label="NITKU">
                        <input name="pemilik[nitku]" class="w-full border rounded px-3 py-2"
                            value="{{ old('pemilik.nitku', $e['pemilik']['nitku'] ?? '001065305305500000000') }}">
                    </x-field>
                </div>
                <x-field label="Nama">
                    <input name="pemilik[nama]" class="w-full border rounded px-3 py-2"
                        value="{{ old('pemilik.nama', $e['pemilik']['nama'] ?? 'AISIN INDONESIA') }}">
                </x-field>
                <x-field label="Alamat">
                    <input name="pemilik[alamat]" class="w-full border rounded px-3 py-2"
                        value="{{ old('pemilik.alamat', $e['pemilik']['alamat'] ?? 'KAWASAN INDUSTRI EJIP PLOT 5J 000/000 SUKARESMI, CIKARANG SELATAN, BEKASI, JAWA BARAT') }}">
                </x-field>
            </div>
        </div>

        {{-- PENGIRIM BARANG --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Pengirim Barang</h3>
            <div class="grid grid-cols-1 gap-3">

                {{-- Select Pengirim --}}
                <x-field label="Nama (Master)">
                    <select name="pengirim[party_id]" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Pengirim --</option>
                        @foreach ($pengirimParties as $party)
                            <option value="{{ $party->id }}" @if ($pengirimSelected == $party->id) selected @endif>
                                {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                </x-field>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Alamat">
                        <input name="pengirim[alamat]" id="pengirim_alamat" class="w-full border rounded px-3 py-2"
                            value="{{ old('pengirim.alamat', $e['pengirim']['alamat'] ?? '') }}" required>
                    </x-field>
                    <x-field label="Negara">
                        <select name="pengirim[negara]" id="pengirim_negara" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih Negara --</option>
                            @foreach ($negaraOpts as $k => $v)
                                <option value="{{ $k }}" @selected(old('pengirim.negara', $e['pengirim']['negara'] ?? '') === $k)>{{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
            </div>
        </div>

        {{-- PENJUAL BARANG --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Penjual Barang</h3>
            <div class="grid grid-cols-1 gap-3">

                {{-- Select Penjual --}}
                <x-field label="Nama (Master)">
                    <select name="penjual[party_id]" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Penjual --</option>
                        @foreach ($penjualParties as $party)
                            <option value="{{ $party->id }}" @if ($penjualSelected == $party->id) selected @endif>
                                {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                </x-field>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Alamat">
                        <input name="penjual[alamat]" id="penjual_alamat" class="w-full border rounded px-3 py-2"
                            value="{{ old('penjual.alamat', $e['penjual']['alamat'] ?? '') }}" required>
                    </x-field>
                    <x-field label="Negara">
                        <select name="penjual[negara]" id="penjual_negara" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih Negara --</option>
                            @foreach ($negaraOpts as $k => $v)
                                <option value="{{ $k }}" @selected(old('penjual.negara', $e['penjual']['negara'] ?? '') === $k)>{{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
            </div>
        </div>
    </div>
</div>
