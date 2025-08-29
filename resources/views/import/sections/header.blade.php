@php
    $kpOptions = config('import.kantor_pabean');
    $pibOptions = config('import.jenis_pib');
    $impOptions = config('import.jenis_impor');
    $payOptions = config('import.cara_pembayaran');
    $h = $draft ?? [];
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- Nomor Aju --}}
    <x-field label="Nomor Aju (Auto Generated)">
        <input class="w-full border rounded px-3 py-2 bg-gray-100"
            value="{{ $h['nomor_aju'] ?? 'Akan dibuat saat Save' }}" readonly>
        <input type="hidden" name="header[nomor_aju]" value="{{ $h['nomor_aju'] ?? '' }}">
        <div class="text-xs text-gray-500 mt-1">Nomor dibuat saat <em>Save & Next</em>.</div>
    </x-field>

    {{-- Kantor Pabean --}}
    <x-field label="Kantor Pabean">
    <select name="header[kantor_pabean]" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Kantor Pabean --</option>
            @foreach ($kpOptions as $val => $label)
                <option value="{{ $val }}" @if ((string) old('header.kantor_pabean', $h['kantor_pabean'] ?? '') == (string) $val) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </x-field>

    {{-- Jenis PIB --}}
    <x-field label="Jenis PIB">
    <select name="header[jenis_pib]" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Jenis PIB --</option>
            @foreach ($pibOptions as $val => $label)
                <option value="{{ $val }}" @if ((string) old('header.jenis_pib', $h['jenis_pib'] ?? '') == (string) $val) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </x-field>

    {{-- Jenis Impor --}}
    <x-field label="Jenis Impor">
    <select name="header[jenis_impor]" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Jenis Impor --</option>
            @foreach ($impOptions as $val => $label)
                <option value="{{ $val }}" @if ((string) old('header.jenis_impor', $h['jenis_impor'] ?? '') == (string) $val) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </x-field>

    {{-- Cara Pembayaran --}}
    <x-field label="Cara Pembayaran">
    <select name="header[cara_pembayaran]" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Cara Pembayaran --</option>
            @foreach ($payOptions as $val => $label)
                <option value="{{ $val }}" @if ((string) old('header.cara_pembayaran', $h['cara_pembayaran'] ?? '') == (string) $val) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </x-field>
</div>
