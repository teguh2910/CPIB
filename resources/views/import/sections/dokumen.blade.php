@php
    $d = $draft ?? [];
    $jenisOpts = config('import.jenis_dokumen');
    $existingDokumen = $d['items'] ?? [];
@endphp

<div class="space-y-4">
    {{-- FORM ADD DOKUMEN --}}
    <div class="bg-gray-50 border rounded-xl p-4">
        <h3 class="font-semibold mb-3">Tambah Dokumen</h3>
        <form method="POST" action="{{ route('import.dokumen.store') }}">
            @csrf
            <div class="grid md:grid-cols-4 gap-3">
                <x-field label="Seri">
                    <input type="number" name="seri" min="1" class="w-full border rounded px-3 py-2" required>
                </x-field>

                <x-field label="Jenis Dokumen">
                    <select name="jenis" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih jenis dokumen --</option>
                        @foreach ($jenisOpts as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </x-field>

                <x-field label="Nomor Dokumen">
                    <input name="nomor" class="w-full border rounded px-3 py-2" required>
                </x-field>

                <x-field label="Tanggal Dokumen">
                    <input type="date" name="tanggal" class="w-full border rounded px-3 py-2" required>
                </x-field>
            </div>

            <div class="mt-3">
                <button type="submit" class="px-3 py-2 rounded bg-black text-white text-sm">Tambah Dokumen</button>
            </div>
        </form>
    </div>

    {{-- TABEL LIST DOKUMEN --}}
    <div class="bg-white border rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2 px-3">Seri</th>
                        <th class="py-2 px-3">Jenis</th>
                        <th class="py-2 px-3">Nomor</th>
                        <th class="py-2 px-3">Tanggal</th>
                        <th class="py-2 px-3 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($existingDokumen as $index => $dokumen)
                        <tr class="border-b">
                            <td class="py-2 px-3">{{ $dokumen['seri'] }}</td>
                            <td class="py-2 px-3">{{ $jenisOpts[$dokumen['jenis']] ?? $dokumen['jenis'] }}</td>
                            <td class="py-2 px-3">{{ $dokumen['nomor'] }}</td>
                            <td class="py-2 px-3">{{ $dokumen['tanggal'] }}</td>
                            <td class="py-2 px-3">
                                <button type="button" class="text-blue-600 underline text-xs"
                                    onclick="editDokumen({{ $index }})">Edit</button>
                                <span class="mx-1">|</span>
                                <button type="button" class="text-red-600 underline text-xs"
                                    onclick="deleteDokumen({{ $index }})">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 px-3 text-center text-gray-500">Belum ada dokumen. Tambahkan
                                di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hidden input for items as JSON --}}
    <input type="hidden" name="items_json" value="{{ json_encode($existingDokumen) }}">
</div>

{{-- Modal untuk Edit Dokumen --}}
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="font-semibold mb-4">Edit Dokumen</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editIndex" name="index">

                <div class="space-y-3">
                    <x-field label="Seri">
                        <input type="number" id="editSeri" name="seri" min="1"
                            class="w-full border rounded px-3 py-2" required>
                    </x-field>

                    <x-field label="Jenis Dokumen">
                        <select id="editJenis" name="jenis" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih jenis dokumen --</option>
                            @foreach ($jenisOpts as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>

                    <x-field label="Nomor Dokumen">
                        <input id="editNomor" name="nomor" class="w-full border rounded px-3 py-2" required>
                    </x-field>

                    <x-field label="Tanggal Dokumen">
                        <input type="date" id="editTanggal" name="tanggal" class="w-full border rounded px-3 py-2"
                            required>
                    </x-field>
                </div>

                <div class="mt-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded bg-black text-white">Update</button>
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded border">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editDokumen(index) {
        const dokumen = @json($existingDokumen)[index];
        if (!dokumen) return;

        document.getElementById('editIndex').value = index;
        document.getElementById('editSeri').value = dokumen.seri;
        document.getElementById('editJenis').value = dokumen.jenis;
        document.getElementById('editNomor').value = dokumen.nomor;
        document.getElementById('editTanggal').value = dokumen.tanggal;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function deleteDokumen(index) {
        if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
            // Implementasi delete akan dilakukan melalui controller
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('import.dokumen.delete') }}';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            const indexInput = document.createElement('input');
            indexInput.type = 'hidden';
            indexInput.name = 'index';
            indexInput.value = index;

            form.appendChild(methodInput);
            form.appendChild(indexInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
