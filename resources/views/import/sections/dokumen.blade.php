@php
    $d = $draft ?? [];
    $jenisOpts = config('import.jenis_dokumen');
    $fasilitasOpts = config('import.kode_fasilitas');
    $existingDokumen = $d['items'] ?? [];
@endphp

<div class="space-y-4">
    {{-- FORM ADD DOKUMEN (JS submission to avoid nested forms) --}}
    <div class="bg-gray-50 border rounded-xl p-4">
        <h3 class="font-semibold mb-3">Tambah Dokumen</h3>
        <div>
            @csrf
            <div class="grid md:grid-cols-2 gap-3">
                <x-field label="Seri">
                    <input type="text" id="dok_seri" name="seri" readonly
                        class="w-full border rounded px-3 py-2 bg-gray-100" required>
                </x-field>
                <x-field label="Jenis Dokumen">
                    <select id="dok_kode" name="kode_dokumen" class="w-full border rounded px-3 py-2" required>
                        @foreach ($jenisOpts as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </x-field>

                <x-field label="Nomor Dokumen">
                    <input id="dok_nomor" name="nomor_dokumen" class="w-full border rounded px-3 py-2" required>
                </x-field>

                <x-field label="Tanggal Dokumen">
                    <input type="date" id="dok_tanggal" name="tanggal_dokumen" class="w-full border rounded px-3 py-2"
                        required>
                </x-field>
                <x-field label="Kode Fasilitas">
                    <select id="dok_fasilitas" name="kode_fasilitas" class="w-full border rounded px-3 py-2" required>
                        @foreach ($fasilitasOpts as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>

            <div class="mt-3">
                <button type="button" id="addDokumenBtn" class="px-3 py-2 rounded bg-black text-white text-sm">Tambah
                    Dokumen</button>
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
                        <th class="py-2 px-3">kode_dokumen</th>
                        <th class="py-2 px-3">nomor_dokumen</th>
                        <th class="py-2 px-3">tanggal_dokumen</th>
                        <th class="py-2 px-3">kode_fasilitas</th>
                        <th class="py-2 px-3 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dokument as $dok)
                        <tr class="border-b">
                            <td class="py-2 px-3">{{ $dok->seri }}</td>
                            <td class="py-2 px-3">{{ $jenisOpts[$dok->kode_dokumen] ?? $dok->kode_dokumen }}</td>
                            <td class="py-2 px-3">{{ $dok->nomor_dokumen }}</td>
                            <td class="py-2 px-3">{{ strftime('%d-%m-%Y', strtotime($dok->tanggal_dokumen)) }}</td>
                            <td class="py-2 px-3">{{ $dok->kode_fasilitas }}</td>
                            <td class="py-2 px-3">
                                <button type="button" class="text-blue-600 underline text-xs"
                                    onclick="editDokumen({{ $dok->id }})">Edit</button>
                                <span class="mx-1">|</span>
                                <button type="button" class="text-red-600 underline text-xs"
                                    onclick="deleteDokumen({{ $dok->id }})">Hapus</button>
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

<script>
    (function() {
        const addBtn = document.getElementById('addDokumenBtn');
        const itemsInput = document.querySelector('input[name="items_json"]');
        const tbody = document.querySelector('table.w-full tbody');
        const url = '/dokumen';
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

        function generateNextSeri() {
            const existingDokumen = @json($dokument);
            let maxSeri = 0;
            existingDokumen.forEach(dok => {
                if (dok.seri > maxSeri) {
                    maxSeri = dok.seri;
                }
            });
            return maxSeri + 1;
        }

        function updateSeriField() {
            const nextSeri = generateNextSeri();
            document.getElementById('dok_seri').value = nextSeri;
        }

        // Initialize Select2
        $('#dok_kode').select2({
            placeholder: '-- Pilih jenis dokumen --',
            allowClear: true,
            width: '100%'
        });

        $('#dok_fasilitas').select2({
            placeholder: '-- Pilih kode fasilitas --',
            allowClear: true,
            width: '100%'
        });

        function renderRow(dokumen, index) {
            const jenisOpts = @json($jenisOpts);
            const tr = document.createElement('tr');
            tr.className = 'border-b';
            tr.innerHTML = `
                <td class="py-2 px-3">${dokumen.seri}</td>
                <td class="py-2 px-3">${jenisOpts[dokumen.kode_dokumen] ?? dokumen.kode_dokumen}</td>
                <td class="py-2 px-3">${dokumen.nomor_dokumen}</td>
                <td class="py-2 px-3">${dokumen.tanggal_dokumen}</td>
                <td class="py-2 px-3">${dokumen.kode_fasilitas}</td>
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
            // Update seri field after adding
            updateSeriField();
        }

        // Initialize seri field on page load
        updateSeriField();

        // Edit Dokumen Function
        window.editDokumen = function(id) {
            // Redirect to edit page
            window.location.href = '{{ route('dokumen.edit', ':id') }}'.replace(':id', id);
        };

        // Delete Dokumen Function
        window.deleteDokumen = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/dokumen/' + id;
                form.style.display = 'none';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                form.appendChild(tokenInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        };

        addBtn?.addEventListener('click', async function() {
            const seri = document.getElementById('dok_seri').value;
            const kodeDokumen = document.getElementById('dok_kode').value;
            const nomor_dokumen = document.getElementById('dok_nomor').value;
            const tanggal_dokumen = document.getElementById('dok_tanggal').value;
            const kodeFasilitas = document.getElementById('dok_fasilitas').value;
            // console.log(seri, kodeDokumen, nomor_dokumen, tanggal_dokumen, kodeFasilitas);
            if (!kodeDokumen || !nomor_dokumen || !tanggal_dokumen ) {
                alert('Lengkapi semua field dokumen (Kode Dokumen, Nomor Dokumen, Tanggal Dokumen, dan Kode Fasilitas)');
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

                ['seri', 'kode_dokumen', 'nomor_dokumen', 'tanggal_dokumen', 'kode_fasilitas'].forEach(function(name) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = ({
                        seri,
                        kode_dokumen: kodeDokumen,
                        nomor_dokumen,
                        tanggal_dokumen,
                        kode_fasilitas: kodeFasilitas
                    })[name];
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
