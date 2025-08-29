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
                <form method="POST" action="{{ route('import.kemasan.store') }}">
                    @csrf
                    <div class="grid md:grid-cols-4 gap-3">
                        <x-field label="Seri">
                            <input type="number" name="seri" min="1" class="w-full border rounded px-3 py-2"
                                required>
                        </x-field>
                        <x-field label="Jumlah">
                            <input type="number" name="jumlah" min="0" step="0.0001"
                                class="w-full border rounded px-3 py-2" required>
                        </x-field>
                        <x-field label="Jenis">
                            <select name="jenis_kemasan" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach ($opsJenisKemasan as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="Merek">
                            <input name="merek" class="w-full border rounded px-3 py-2">
                        </x-field>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="px-3 py-2 rounded bg-black text-white text-sm">Tambah
                            Kemasan</button>
                    </div>
                </form>
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
                            @forelse($kemasanData as $item)
                                <tr class="border-b">
                                    <td class="py-2 px-3">{{ $item->seri }}</td>
                                    <td class="py-2 px-3">{{ $item->jumlah }}</td>
                                    <td class="py-2 px-3">{{ $opsJenisKemasan[$item->jenis] ?? $item->jenis }}</td>
                                    <td class="py-2 px-3">{{ $item->merek }}</td>
                                    <td class="py-2 px-3">
                                        <a href="{{ route('import.kemasan.edit', $item->id) }}"
                                            class="text-blue-600 underline text-xs">Edit</a>
                                        <span class="mx-1">|</span>
                                        <form method="POST" action="{{ route('import.kemasan.destroy', $item->id) }}"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 underline text-xs"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                        </form>
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
                <form method="POST" action="{{ route('import.petikemas.store') }}">
                    @csrf
                    <div class="grid md:grid-cols-5 gap-3">
                        <x-field label="Seri">
                            <input type="number" name="seri" min="1" class="w-full border rounded px-3 py-2"
                                required>
                        </x-field>
                        <x-field label="No. Cont">
                            <input name="nomor" placeholder="MSCU1234567" class="w-full border rounded px-3 py-2"
                                style="text-transform:uppercase" required>
                        </x-field>
                        <x-field label="Ukuran">
                            <select name="ukuran" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsUkuran as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="Jenis Muatan">
                            <select name="jenis_muatan" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsJenisMuatan as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field label="Tipe">
                            <select name="tipe" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($opsTipe as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="px-3 py-2 rounded bg-black text-white text-sm">Tambah Peti
                            Kemas</button>
                    </div>
                </form>
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
                            @forelse($petiKemasData as $item)
                                <tr class="border-b">
                                    <td class="py-2 px-3">{{ $item->seri }}</td>
                                    <td class="py-2 px-3">{{ $item->nomor }}</td>
                                    <td class="py-2 px-3">{{ $opsUkuran[$item->ukuran] ?? $item->ukuran }}</td>
                                    <td class="py-2 px-3">
                                        {{ $opsJenisMuatan[$item->jenis_muatan] ?? $item->jenis_muatan }}</td>
                                    <td class="py-2 px-3">{{ $opsTipe[$item->tipe] ?? $item->tipe }}</td>
                                    <td class="py-2 px-3">
                                        <a href="{{ route('import.petikemas.edit', $item->id) }}"
                                            class="text-blue-600 underline text-xs">Edit</a>
                                        <span class="mx-1">|</span>
                                        <form method="POST"
                                            action="{{ route('import.petikemas.destroy', $item->id) }}"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 underline text-xs"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                        </form>
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
