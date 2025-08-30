@php
    $p = $draft ?? [];

    $opsCara = config('import.cara_pengangkutan');
    $opsSarana = config('import.sarana_angkut');
    $opsNegara = config('import.negara');
    $opsPort = config('import.pelabuhan');
    $opsTps = config('import.tps');

    $bc = $p['bc11'] ?? [];
    $ang = $p['angkut'] ?? [];
    $pel = $p['pelabuhan'] ?? [];
    $tps = $p['tps'] ?? [];
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- ====== KOLOM KIRI ====== --}}
    <div class="space-y-4">
        {{-- Section BC 1.1 --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">BC 1.1</h3>
            <div class="grid grid-cols-1 gap-3">
                <x-field label="Nomor Tutup PU BC.1.1">
                    <input name="bc11[no_tutup_pu]" class="w-full border rounded px-3 py-2"
                        value="{{ old('bc11.no_tutup_pu', $bc['no_tutup_pu'] ?? '') }}">
                </x-field>
                <div>
                    <label class="block text-sm mb-1">Nomor Pos</label>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="number" name="bc11[pos_1]" class="w-full border rounded px-3 py-2"
                            value="{{ old('bc11.pos_1', $bc['pos_1'] ?? '') }}" placeholder="Pos">
                        <input type="number" name="bc11[pos_2]" class="w-full border rounded px-3 py-2"
                            value="{{ old('bc11.pos_2', $bc['pos_2'] ?? '') }}" placeholder="Subpos">
                        <input type="number" name="bc11[pos_3]" class="w-full border rounded px-3 py-2"
                            value="{{ old('bc11.pos_3', $bc['pos_3'] ?? '') }}" placeholder="Sub-subpos">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Pengangkutan --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Pengangkutan</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Cara Pengangkutan">
                        <select name="angkut[cara]" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih Cara --</option>
                            @foreach ($opsCara as $k => $v)
                                <option value="{{ $k }}" @if ((string) old('angkut.cara', $ang['cara'] ?? '') === (string) $k) selected @endif>
                                    {{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>

                    <x-field label="Nama Sarana Angkut">
                        <input type="text" name="angkut[nama]" class="w-full border rounded px-3 py-2"
                            value="{{ old('angkut.nama', $ang['nama'] ?? '') }}">
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Nomor Voy/Flight">
                        <input name="angkut[voy]" class="w-full border rounded px-3 py-2"
                            value="{{ old('angkut.voy', $ang['voy'] ?? '') }}">
                    </x-field>

                    <x-field label="Bendera">
                        <select name="angkut[bendera]" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih Negara --</option>
                            @foreach ($opsNegara as $k => $v)
                                <option value="{{ $k }}" @if ((string) old('angkut.bendera', $ang['bendera'] ?? '') === (string) $k) selected @endif>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <x-field label="Perkiraan Tanggal Tiba (ETA)">
                    <input type="date" name="angkut[eta]" class="w-full border rounded px-3 py-2"
                        value="{{ old('angkut.eta', $ang['eta'] ?? '') }}">
                </x-field>
            </div>
        </div>
    </div>

    {{-- ====== KOLOM KANAN ====== --}}
    <div class="space-y-4">
        {{-- Section Pelabuhan & Tempat Penimbunan --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Pelabuhan & Tempat Penimbunan</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Pelabuhan Muat">
                        <select name="pelabuhan[muat]" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsPort as $k => $v)
                                <option value="{{ $k }}" @if ((string) old('pelabuhan.muat', $pel['muat'] ?? '') === (string) $k) selected @endif>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Pelabuhan Transit">
                        <select name="pelabuhan[transit]" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsPort as $k => $v)
                                <option value="{{ $k }}" @if ((string) old('pelabuhan.transit', $pel['transit'] ?? '') === (string) $k) selected @endif>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Pelabuhan Tujuan">
                        <select name="pelabuhan[tujuan]" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsPort as $k => $v)
                                <option value="{{ $k }}" @if ((string) old('pelabuhan.tujuan', $pel['tujuan'] ?? '') === (string) $k) selected @endif>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Tempat Penimbunan">
                        <select name="tps[kode]" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsTps as $k => $v)
                                <option value="{{ $k }}" @if ((string) old('tps.kode', $tps['kode'] ?? '') === (string) $k) selected @endif>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
            </div>
        </div>
    </div>
</div>
