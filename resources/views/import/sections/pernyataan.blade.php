<div class="bg-gray-50 p-4 rounded-lg mb-6 border">
    <h3 class="font-semibold text-lg mb-3">Pernyataan</h3>
    <p class="text-sm leading-relaxed mb-4">
        Dengan ini saya menyatakan:
    </p>
    <ol class="text-sm leading-relaxed list-decimal list-inside space-y-2 mb-4">
        <li>bertanggung jawab atas kebenaran hal-hal yang diberitahukan dalam dokumen ini dan keabsahan dokumen
            pelengkap pabean yang menjadi dasar pembuatan dokumen ini; dan</li>
        <li>apabila dalam jangka waktu paling lambat 1 (satu) hari setelah tanggal pemberitahuan kesiapan barang, saya
            tidak hadir untuk menyaksikan pemeriksaan fisik, maka saya menguasakan penyaksian kepada pengusaha TPS
            dengan resiko dan biaya menjadi tanggung jawab saya.</li>
    </ol>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <x-field label="Nama Pernyataan">
        <input name="nama_pernyataan" class="w-full border rounded px-3 py-2"
            value="{{ old('nama_pernyataan', $draft['nama_pernyataan'] ?? 'Herlina Trisnawati') }}" required>
    </x-field>
    <x-field label="Jabatan Pernyataan">
        <input name="jabatan_pernyataan" class="w-full border rounded px-3 py-2"
            value="{{ old('jabatan_pernyataan', $draft['jabatan_pernyataan'] ?? 'Direktur') }}">
    </x-field>
    <x-field label="Kota Pernyataan">
        <input name="kota_pernyataan" class="w-full border rounded px-3 py-2"
            value="{{ old('kota_pernyataan', $draft['kota_pernyataan'] ?? 'Bekasi') }}" required>
    </x-field>
    <x-field label="Tanggal Pernyataan">
        <input type="date" name="tanggal_pernyataan" class="w-full border rounded px-3 py-2"
            value="{{ old('tanggal_pernyataan', $draft['tanggal_pernyataan'] ?? date('Y-m-d')) }}" required>
    </x-field>

</div>
