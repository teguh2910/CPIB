@php
    $d = $draft ?? [];
    $jenisOpts = config('import.jenis_dokumen');
    $existingDokumen = $d['items'] ?? [];
@endphp

<div class="space-y-4">
    {{-- FORM ADD DOKUMEN (JS submission to avoid nested forms) --}}
    <div class="bg-gray-50 border rounded-xl p-4">
        <h3 class="font-semibold mb-3">Tambah Dokumen</h3>
        <div>
            @csrf
            <div class="grid md:grid-cols-4 gap-3">
                <x-field label="Seri">
                    <input type="number" id="dok_seri" name="seri" min="1" class="w-full border rounded px-3 py-2" required>
                </x-field>

                <x-field label="Jenis Dokumen">
                    <select id="dok_jenis" name="jenis" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih jenis dokumen --</option>
                        @foreach ($jenisOpts as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </x-field>

                <x-field label="Nomor Dokumen">
                    <input id="dok_nomor" name="nomor" class="w-full border rounded px-3 py-2" required>
                </x-field>

                <x-field label="Tanggal Dokumen">
                    <input type="date" id="dok_tanggal" name="tanggal" class="w-full border rounded px-3 py-2" required>
                </x-field>
            </div>

            <div class="mt-3">
                <button type="button" id="addDokumenBtn" class="px-3 py-2 rounded bg-black text-white text-sm">Tambah Dokumen</button>
            </div>
        </div>
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
                    @foreach ($dokument as $dok)
                        <tr class="border-b">
                            <td class="py-2 px-3">{{ $dok->seri }}</td>
                            <td class="py-2 px-3">{{ $jenisOpts[$dok->jenis] ?? $dok->jenis }}</td>
                            <td class="py-2 px-3">{{ $dok->nomor }}</td>
                            <td class="py-2 px-3">{{ strftime('%d-%m-%Y', strtotime($dok->tanggal)) }}</td>
                            <td class="py-2 px-3">
                                <button type="button" class="text-blue-600 underline text-xs"
                                    onclick="editDokumen({{ $dok->id }})">Edit</button>
                                <span class="mx-1">|</span>
                                <button type="button" class="text-red-600 underline text-xs"
                                    onclick="deleteDokumen({{ $dok->id }})">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                        <tr>
                            <td colspan="5" class="py-4 px-3 text-center text-gray-500">Belum ada dokumen. Tambahkan
                                di atas.</td>
                            </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hidden input for items as JSON --}}
    <input type="hidden" name="items_json" value="{{ json_encode($existingDokumen) }}">
</div>

<script>
    (function(){
        const addBtn = document.getElementById('addDokumenBtn');
        const itemsInput = document.querySelector('input[name="items_json"]');
        const tbody = document.querySelector('table.w-full tbody');
        const url = '{{ route('dokumen.store') }}';
        const csrf = '{{ csrf_token() }}';

        function getItems() {
            try {
                return JSON.parse(itemsInput.value || '[]');
            } catch (e) {
                return [];
            }
        }

        function setItems(items) {
            itemsInput.value = JSON.stringify(items);
        }

        function renderRow(dokumen, index) {
            const jenisOpts = @json($jenisOpts);
            const tr = document.createElement('tr');
            tr.className = 'border-b';
            tr.innerHTML = `
                <td class="py-2 px-3">${dokumen.seri}</td>
                <td class="py-2 px-3">${jenisOpts[dokumen.jenis] ?? dokumen.jenis}</td>
                <td class="py-2 px-3">${dokumen.nomor}</td>
                <td class="py-2 px-3">${dokumen.tanggal}</td>
                <td class="py-2 px-3">
                    <button type="button" class="text-blue-600 underline text-xs" onclick="editDokumen(${index})">Edit</button>
                    <span class="mx-1">|</span>
                    <button type="button" class="text-red-600 underline text-xs" onclick="deleteDokumen(${index})">Hapus</button>
                </td>
            `;
            return tr;
        }

        function appendRow(dokumen) {
            const items = getItems();
            const idx = items.length;
            items.push(dokumen);
            setItems(items);
            // remove empty row if present
            const emptyRow = tbody.querySelector('tr td[colspan="5"]');
            if (emptyRow) emptyRow.parentElement.remove();
            tbody.appendChild(renderRow(dokumen, idx));
        }

        addBtn?.addEventListener('click', async function(){
            const seri = document.getElementById('dok_seri').value;
            const jenis = document.getElementById('dok_jenis').value;
            const nomor = document.getElementById('dok_nomor').value;
            const tanggal = document.getElementById('dok_tanggal').value;

            if (!seri || !jenis || !nomor || !tanggal) {
                alert('Lengkapi semua field dokumen');
                return;
            }

            // Submit via a temporary form to ensure the POST reaches the controller
            try {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.style.display = 'none';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrf;
                form.appendChild(tokenInput);

                ['seri','jenis','nomor','tanggal'].forEach(function(name){
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = ({seri,jenis,nomor,tanggal})[name];
                    form.appendChild(inp);
                });

                document.body.appendChild(form);
                form.submit();
            } catch (err) {
                console.error(err);
                alert('Terjadi error saat menyimpan dokumen (cek console)');
            }
        });
    })();
</script>

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

