<div class="grid md:grid-cols-2 gap-4">
    <x-field label="Dinyatakan oleh">
        <input name="declared_by" class="w-full border rounded px-3 py-2"
            value="{{ old('declared_by', $draft['declared_by'] ?? '') }}" required>
    </x-field>
    <x-field label="Jabatan (opsional)">
        <input name="jabatan" class="w-full border rounded px-3 py-2"
            value="{{ old('jabatan', $draft['jabatan'] ?? '') }}">
    </x-field>
    <x-field class="md:col-span-2" label="Tempat & Tanggal">
        <input name="place_date" class="w-full border rounded px-3 py-2"
            value="{{ old('place_date', $draft['place_date'] ?? '') }}" required>
    </x-field>
    <x-field class="md:col-span-2" label="Path TTD (opsional)">
        <input name="ttd_image_path" class="w-full border rounded px-3 py-2"
            value="{{ old('ttd_image_path', $draft['ttd_image_path'] ?? '') }}"
            placeholder="public/img/sign/user/xxx/ttd.jpg">
    </x-field>
    <div class="md:col-span-2 flex items-center gap-2">
        <input id="agree" type="checkbox" name="agree" value="1" class="w-4 h-4"
            {{ old('agree', $draft['agree'] ?? false) ? 'checked' : '' }}>
        <label for="agree" class="text-sm">Saya menyatakan data di atas benar.</label>
    </div>
</div>
