@php
    $k = $draft ?? [];
    $opsJenisKemasan = config('import.jenis_kemasan');
    $opsUkuran = config('import.ukuran_petikemas');
    $opsJenisMuatan = config('import.jenis_muatan_petikemas');
    $opsTipe = config('import.tipe_petikemas');

    // Use draft data instead of database queries
    $kemasanData = collect($k['kemasan'] ?? []);
    $petiKemasData = collect($k['peti_kemas'] ?? []);
@endphp

<div class="space-y-6">
    <div class="grid md:grid-cols-2 gap-4">
        {{-- ================== KOLOM KIRI: KEMASAN ================== --}}
        <div class="space-y-3">
            <div class="bg-gray-50 border rounded-xl p-4">
                <h3 class="font-semibold mb-3">Tambah Kemasan</h3>
                <div>
                    @csrf
                    <div class="grid md:grid-cols-4 gap-3">
                        <x-field label="Seri">
                            <input type="number" id="kem_seri" name="seri" readonly
                                class="w-full border rounded px-3 py-2 bg-gray-100" required>
                        </x-field>
                        <x-field label="Jumlah">
                            <input type="number" id="kem_jumlah" name="jumlah" min="0" step="0.0001"
                                class="w-full border rounded px-3 py-2" required>
                        </x-field>
                        <x-field label="Jenis">
                            <select id="kem_jenis" name="jenis_kemasan" class="w-full border rounded px-3 py-2"
                                required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach ($opsJenisKemasan as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="Merek">
                            <input id="kem_merek" name="merek" class="w-full border rounded px-3 py-2">
                        </x-field>
                    </div>
                    <div class="mt-3">
                        <button type="button" id="addKemasanBtn"
                            class="px-3 py-2 rounded bg-black text-white text-sm">Tambah
                            Kemasan</button>
                    </div>
                </div>
            </div>

            {{-- TABEL KEMASAN --}}
            <div class="bg-white border rounded-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 px-3">Seri</th>
                                <th class="py-2 px-3">Jumlah</th>
                                <th class="py-2 px-3">Jenis</th>
                                <th class="py-2 px-3">Merek</th>
                                <th class="py-2 px-3 w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kemasan as $item)
                                <tr class="border-b">
                                    <td class="py-2 px-3">{{ $item->seri }}</td>
                                    <td class="py-2 px-3">{{ $item->jumlah }}</td>
                                    <td class="py-2 px-3">{{ $opsJenisKemasan[$item->jenis] ?? $item->jenis }}</td>
                                    <td class="py-2 px-3">{{ $item->merek }}</td>
                                    <td class="py-2 px-3">
                                        <button type="button" class="text-blue-600 underline text-xs"
                                            onclick="editKemasan({{ $item->id }})">Edit</button>
                                        <span class="mx-1">|</span>
                                        <button type="button" class="text-red-600 underline text-xs"
                                            onclick="deleteKemasan({{ $item->id }})">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 px-3 text-center text-gray-500">Belum ada kemasan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============== KOLOM KANAN: PETI KEMAS ============== --}}
        <div class="space-y-3">
            <div class="bg-gray-50 border rounded-xl p-4">
                <h3 class="font-semibold mb-3">Tambah Peti Kemas</h3>
                <div>
                    @csrf
                    <div class="grid md:grid-cols-5 gap-3">
                        <x-field label="Seri">
                            <input type="number" id="peti_seri" name="seri" readonly
                                class="w-full border rounded px-3 py-2 bg-gray-100" required>
                        </x-field>
                        <x-field label="No. Cont">
                            <input id="peti_nomor" name="nomor" placeholder="MSCU1234567"
                                class="w-full border rounded px-3 py-2" style="text-transform:uppercase" required>
                        </x-field>
                        <x-field label="Ukuran">
                            <select id="peti_ukuran" name="ukuran" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsUkuran as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="Jenis Muatan">
                            <select id="peti_jenis_muatan" name="jenis_muatan" class="w-full border rounded px-3 py-2"
                                required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsJenisMuatan as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="Tipe">
                            <select id="peti_tipe" name="tipe" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsTipe as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>
                    <div class="mt-3">
                        <button type="button" id="addPetiKemasBtn"
                            class="px-3 py-2 rounded bg-black text-white text-sm">Tambah Peti
                            Kemas</button>
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 px-3">Seri</th>
                                <th class="py-2 px-3">Nomor</th>
                                <th class="py-2 px-3">Ukuran</th>
                                <th class="py-2 px-3">Jenis Muatan</th>
                                <th class="py-2 px-3">Tipe</th>
                                <th class="py-2 px-3 w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($petiKemas as $item)
                                <tr class="border-b">
                                    <td class="py-2 px-3">{{ $item->seri }}</td>
                                    <td class="py-2 px-3">{{ $item->nomor }}</td>
                                    <td class="py-2 px-3">{{ $opsUkuran[$item->ukuran] ?? $item->ukuran }}</td>
                                    <td class="py-2 px-3">
                                        {{ $opsJenisMuatan[$item->jenis_muatan] ?? $item->jenis_muatan }}</td>
                                    <td class="py-2 px-3">{{ $opsTipe[$item->tipe] ?? $item->tipe }}</td>
                                    <td class="py-2 px-3">
                                        <button type="button" class="text-blue-600 underline text-xs"
                                            onclick="editPetiKemas({{ $item->id }})">Edit</button>
                                        <span class="mx-1">|</span>
                                        <button type="button" class="text-red-600 underline text-xs"
                                            onclick="deletePetiKemas({{ $item->id }})">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 px-3 text-center text-gray-500">Belum ada peti
                                        kemas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>

<script>
    (function() {
        // Function to generate next seri number for kemasan
        function generateNextKemasanSeri() {
            const existingKemasan = @json($kemasan ?? []);
            let maxSeri = 0;
            existingKemasan.forEach(item => {
                if (item.seri > maxSeri) {
                    maxSeri = item.seri;
                }
            });
            return maxSeri + 1;
        }

        // Function to generate next seri number for peti kemas
        function generateNextPetiKemasSeri() {
            const existingPetiKemas = @json($petiKemas ?? []);
            let maxSeri = 0;
            existingPetiKemas.forEach(item => {
                if (item.seri > maxSeri) {
                    maxSeri = item.seri;
                }
            });
            return maxSeri + 1;
        }

        // Initialize seri fields on page load
        document.getElementById('kem_seri').value = generateNextKemasanSeri();
        document.getElementById('peti_seri').value = generateNextPetiKemasSeri();

        // Kemasan Form Handler
        const addKemasanBtn = document.getElementById('addKemasanBtn');
        const kemasanUrl = '{{ route('kemasan.store') }}';
        const kemasanCsrf = '{{ csrf_token() }}';

        addKemasanBtn?.addEventListener('click', function() {
            const seri = document.getElementById('kem_seri').value;
            const jumlah = document.getElementById('kem_jumlah').value;
            const jenis_kemasan = document.getElementById('kem_jenis').value;
            const merek = document.getElementById('kem_merek').value;

            if (!seri || !jumlah || !jenis_kemasan) {
                alert('Lengkapi semua field kemasan yang wajib diisi');
                return;
            }

            // Submit via a temporary form to ensure the POST reaches the controller
            try {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = kemasanUrl;
                form.style.display = 'none';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = kemasanCsrf;
                form.appendChild(tokenInput);

                const fields = {
                    seri,
                    jumlah,
                    jenis_kemasan,
                    merek
                };
                Object.keys(fields).forEach(function(name) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = fields[name];
                    form.appendChild(inp);
                });

                document.body.appendChild(form);
                form.submit();
            } catch (err) {
                console.error(err);
                alert('Terjadi error saat menyimpan kemasan (cek console)');
            }
        });

        // Peti Kemas Form Handler
        const addPetiKemasBtn = document.getElementById('addPetiKemasBtn');
        const petiKemasUrl = '{{ route('petikemas.store') }}';
        const petiKemasCsrf = '{{ csrf_token() }}';

        addPetiKemasBtn?.addEventListener('click', function() {
            const seri = document.getElementById('peti_seri').value;
            const nomor = document.getElementById('peti_nomor').value;
            const ukuran = document.getElementById('peti_ukuran').value;
            const jenis_muatan = document.getElementById('peti_jenis_muatan').value;
            const tipe = document.getElementById('peti_tipe').value;

            if (!seri || !nomor || !ukuran || !jenis_muatan || !tipe) {
                alert('Lengkapi semua field peti kemas yang wajib diisi');
                return;
            }

            // Submit via a temporary form to ensure the POST reaches the controller
            try {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = petiKemasUrl;
                form.style.display = 'none';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = petiKemasCsrf;
                form.appendChild(tokenInput);

                const fields = {
                    seri,
                    nomor,
                    ukuran,
                    jenis_muatan,
                    tipe
                };
                Object.keys(fields).forEach(function(name) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = fields[name];
                    form.appendChild(inp);
                });

                document.body.appendChild(form);
                form.submit();
            } catch (err) {
                console.error(err);
                alert('Terjadi error saat menyimpan peti kemas (cek console)');
            }
        });

        // Edit and Delete functions for kemasan
        window.editKemasan = function(id) {
            window.location.href = '{{ route('kemasan.edit', ':id') }}'.replace(':id', id);
        };

        window.deleteKemasan = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus kemasan ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('kemasan') }}/' + id;
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

        // Edit and Delete functions for peti kemas
        window.editPetiKemas = function(id) {
            window.location.href = '{{ route('petikemas.edit', ':id') }}'.replace(':id', id);
        };

        window.deletePetiKemas = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus peti kemas ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('petikemas') }}/' + id;
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
    })();
</script>
