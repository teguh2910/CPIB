@php
    $kpOptions = config('import.kode_kantor');
    $pibOptions = config('import.kode_jenis_prosedur');
    $impOptions = config('import.kode_jenis_impor');
    $payOptions = config('import.kode_cara_bayar');
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
        <select id="kode_kantor" name="header[kode_kantor]" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Kantor Pabean --</option>
            @foreach ($kpOptions as $val => $label)
                <option value="{{ $val }}" @if ((string) old('header.kode_kantor', $h['kode_kantor'] ?? '') == (string) $val) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </x-field>

    {{-- Jenis PIB --}}
    <x-field label="Jenis PIB">
    <select name="header[kode_jenis_prosedur]" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Jenis PIB --</option>
            @foreach ($pibOptions as $val => $label)
                <option value="{{ $val }}" @if ((string) old('header.kode_jenis_prosedur', $h['kode_jenis_prosedur'] ?? '') == (string) $val) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </x-field>

    {{-- Jenis Impor --}}
    <x-field label="Jenis Impor">
    <select name="header[kode_jenis_impor]" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Jenis Impor --</option>
            @foreach ($impOptions as $val => $label)
                <option value="{{ $val }}" @if ((string) old('header.kode_jenis_impor', $h['kode_jenis_impor'] ?? '') == (string) $val) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </x-field>

    {{-- Cara Pembayaran --}}
    <x-field label="Cara Pembayaran">
    <select name="header[kode_cara_bayar]" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Pilih Cara Pembayaran --</option>
            @foreach ($payOptions as $val => $label)
                <option value="{{ $val }}" @if ((string) old('header.kode_cara_bayar', $h['kode_cara_bayar'] ?? '') == (string) $val) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </x-field>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#kode_kantor').select2({
            placeholder: '-- Pilih Kantor Pabean --',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
