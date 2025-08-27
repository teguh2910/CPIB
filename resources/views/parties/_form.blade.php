@props(['party' => null, 'countries' => []])

@php
    $isEdit = (bool) $party;
@endphp

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1">Jenis</label>
        <select name="type" class="w-full border rounded px-3 py-2" required>
            @foreach (['pengirim' => 'Pengirim', 'penjual' => 'Penjual'] as $k => $v)
                <option value="{{ $k }}" @selected(old('type', $party->type ?? '') === $k)>{{ $v }}</option>
            @endforeach
        </select>
        @error('type')
            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1">Kode (unik)</label>
        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code', $party->code ?? '') }}"
            required>
        @error('code')
            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1">Nama</label>
        <input name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $party->name ?? '') }}"
            required>
        @error('name')
            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1">Negara</label>
        <select name="country" class="w-full border rounded px-3 py-2">
            <option value="">-- Pilih --</option>
            @foreach ($countries as $code => $label)
                <option value="{{ $code }}" @selected(old('country', $party->country ?? '') === $code)>{{ $label }}</option>
            @endforeach
        </select>
        @error('country')
            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">Alamat</label>
        <input name="address" class="w-full border rounded px-3 py-2"
            value="{{ old('address', $party->address ?? '') }}">
        @error('address')
            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>
