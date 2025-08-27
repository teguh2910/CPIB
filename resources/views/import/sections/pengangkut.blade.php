<div class="grid md:grid-cols-3 gap-4">
    <x-field label="Moda">
        <select name="moda" class="w-full border rounded px-3 py-2" required>
            @foreach (['Laut', 'Udara', 'Darat'] as $m)
                <option value="{{ $m }}" @selected(old('moda', $draft['moda'] ?? '') === $m)>{{ $m }}</option>
            @endforeach
        </select>
    </x-field>
    <x-field label="Voy/Flight">
        <input name="voy_flight" class="w-full border rounded px-3 py-2"
            value="{{ old('voy_flight', $draft['voy_flight'] ?? '') }}">
    </x-field>
    <x-field label="Nama Kapal/Pesawat">
        <input name="nama_kapal_pesawat" class="w-full border rounded px-3 py-2"
            value="{{ old('nama_kapal_pesawat', $draft['nama_kapal_pesawat'] ?? '') }}">
    </x-field>

    <x-field label="Pelabuhan Muat">
        <input name="pelabuhan_muat" class="w-full border rounded px-3 py-2"
            value="{{ old('pelabuhan_muat', $draft['pelabuhan_muat'] ?? '') }}">
    </x-field>
    <x-field label="Pelabuhan Transit">
        <input name="pelabuhan_transit" class="w-full border rounded px-3 py-2"
            value="{{ old('pelabuhan_transit', $draft['pelabuhan_transit'] ?? '') }}">
    </x-field>
    <x-field label="Pelabuhan Bongkar">
        <input name="pelabuhan_bongkar" class="w-full border rounded px-3 py-2"
            value="{{ old('pelabuhan_bongkar', $draft['pelabuhan_bongkar'] ?? '') }}">
    </x-field>

    <x-field label="ETD">
        <input type="date" name="etd" class="w-full border rounded px-3 py-2"
            value="{{ old('etd', $draft['etd'] ?? '') }}">
    </x-field>
    <x-field label="ETA">
        <input type="date" name="eta" class="w-full border rounded px-3 py-2"
            value="{{ old('eta', $draft['eta'] ?? '') }}">
    </x-field>
</div>
