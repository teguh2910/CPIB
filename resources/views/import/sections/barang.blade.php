@php
    $opsNegara = config('import.negara');
    $opsSatuan = config('import.satuan_barang');
    $opsKemasan = config('import.jenis_kemasan');
    $opsSpek = config('import.spesifikasi_lain');
    $opsKond = config('import.kondisi_barang');
    $opsKetBM = config('import.ket_bm');
    $opsKetPPN = config('import.ket_ppn');
    $opsKetPPH = config('import.ket_pph');

    // Data sudah diterima dari controller via compact
    // $barangData, $ndpbm, $dokList sudah tersedia

@endphp

<div class="space-y-6">
    {{-- Form Tambah Barang --}}
    <div class="bg-gray-50 border rounded-xl p-4">
        <h3 class="font-semibold mb-3">Tambah Barang</h3>

        <form method="POST" action="{{ route('import.barang.store') }}">
            @csrf
            <input type="hidden" name="ndpbm" value="{{ $ndpbm }}">
            <input type="hidden" name="seri" value="{{ $barangData->count() + 1 }}">

            <div class="grid md:grid-cols-2 gap-5">
                {{-- ===== Informasi Dasar ===== --}}
                <div class="space-y-3">
                    <h4 class="font-medium">Informasi Dasar</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <x-field label="POS Tarif">
                            <input name="pos_tarif" placeholder="HS Code" class="w-full border rounded px-3 py-2"
                                required>
                        </x-field>

                        <div>
                            <label class="text-sm">Pernyataan Lartas</label>
                            <div class="flex gap-5 mt-1">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="lartas" value="1" required> <span>Ya</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="lartas" value="0" required> <span>Tidak</span>
                                </label>
                            </div>
                        </div>

                        <x-field label="Kode Barang">
                            <input name="kode_barang" class="w-full border rounded px-3 py-2">
                        </x-field>

                        <x-field label="Uraian Jenis Barang">
                            <textarea name="uraian" class="w-full border rounded px-3 py-2" rows="2" required></textarea>
                        </x-field>

                        <div class="grid md:grid-cols-2 gap-3">
                            <x-field label="Spesifikasi Lain">
                                <input name="spesifikasi" class="w-full border rounded px-3 py-2">
                            </x-field>
                            <x-field label="Kondisi Barang">
                                <select name="kondisi" class="w-full border rounded px-3 py-2">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($opsKond as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </x-field>
                        </div>

                        <div class="grid md:grid-cols-2 gap-3">
                            <x-field label="Negara Asal">
                                <select name="negara_asal" class="w-full border rounded px-3 py-2">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($opsNegara as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </x-field>
                            <x-field label="Berat Bersih (Kg)">
                                <input type="number" step="0.001" name="berat_bersih"
                                    class="w-full border rounded px-3 py-2" required>
                            </x-field>
                        </div>
                    </div>
                </div>

                {{-- ===== Jumlah & Kemasan + Nilai & Keuangan ===== --}}
                <div class="space-y-3">
                    <h4 class="font-medium">Jumlah & Kemasan</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="grid md:grid-cols-2 gap-3">
                            <x-field label="Jumlah Satuan Barang">
                                <input type="number" step="0.000001" name="jumlah"
                                    class="w-full border rounded px-3 py-2" required>
                            </x-field>
                            <x-field label="Jenis Satuan">
                                <select name="satuan" class="w-full border rounded px-3 py-2">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($opsSatuan as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </x-field>
                        </div>

                        <div class="grid md:grid-cols-2 gap-3">
                            <x-field label="Jumlah Kemasan">
                                <input type="number" step="0.000001" name="jml_kemasan"
                                    class="w-full border rounded px-3 py-2">
                            </x-field>
                            <x-field label="Jenis Kemasan">
                                <select name="jenis_kemasan" class="w-full border rounded px-3 py-2">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($opsKemasan as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </x-field>
                        </div>
                    </div>

                    <h4 class="font-medium mt-4">Informasi Nilai & Keuangan</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="grid md:grid-cols-2 gap-3">
                            <x-field label="Nilai Barang (Amount)">
                                <input type="number" step="0.01" name="nilai_barang"
                                    class="w-full border rounded px-3 py-2" required>
                            </x-field>
                            <x-field label="FOB">
                                <input type="number" step="0.01" name="fob"
                                    class="w-full border rounded px-3 py-2">
                            </x-field>
                        </div>

                        <div class="grid md:grid-cols-2 gap-3">
                            <x-field label="Freight">
                                <input type="number" step="0.01" name="freight"
                                    class="w-full border rounded px-3 py-2">
                            </x-field>
                            <x-field label="Asuransi">
                                <input type="number" step="0.01" name="asuransi"
                                    class="w-full border rounded px-3 py-2">
                            </x-field>
                        </div>

                        <x-field label="Dokumen Fasilitas/Lartas">
                            <select name="dokumen_fasilitas" class="w-full border rounded px-3 py-2">
                                <option value="">-- Pilih Dokumen --</option>
                                @foreach ($dokList as $label)
                                    <option value="{{ $label }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>
                </div>
            </div>

            {{-- ===== Pungutan ===== --}}
            <div class="mt-6 grid md:grid-cols-3 gap-4">
                <div class="bg-white border rounded-xl p-4 space-y-2">
                    <h4 class="font-medium">BM (Bea Masuk)</h4>
                    <x-field label="Keterangan">
                        <select name="ket_bm" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsKetBM as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Tarif (%)">
                        <input type="number" step="0.01" name="tarif_bm"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                </div>

                <div class="bg-white border rounded-xl p-4 space-y-2">
                    <h4 class="font-medium">PPN</h4>
                    <x-field label="Tarif (11% / 12%)">
                        <select name="ppn_tarif" class="w-full border rounded px-3 py-2">
                            <option value="0">0%</option>
                            <option value="11">11%</option>
                            <option value="12">12%</option>
                        </select>
                    </x-field>
                    <x-field label="Keterangan">
                        <select name="ket_ppn" class="w-full border rounded px-3 py-2">
                            @foreach ($opsKetPPN as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>

                <div class="bg-white border rounded-xl p-4 space-y-2">
                    <h4 class="font-medium">PPh</h4>
                    <x-field label="Keterangan">
                        <select name="ket_pph" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsKetPPH as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Tarif (%)">
                        <input type="number" step="0.01" name="tarif_pph"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="px-3 py-2 rounded bg-black text-white text-sm">Simpan Barang</button>
                <a href="{{ route('import.barang.index') }}" class="px-3 py-2 rounded border text-sm">Batal</a>
            </div>
        </form>
    </div>

    {{-- Tabel List Barang --}}
    <div class="bg-white border rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2 px-3">Seri</th>
                        <th class="py-2 px-3">POS</th>
                        <th class="py-2 px-3">Uraian</th>
                        <th class="py-2 px-3">Jumlah</th>
                        <th class="py-2 px-3">Satuan</th>
                        <th class="py-2 px-3">Nilai</th>
                        <th class="py-2 px-3">Pabean Rp</th>
                        <th class="py-2 px-3 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangData as $barang)
                        <tr class="border-b align-top">
                            <td class="py-2 px-3">{{ $barang->seri }}</td>
                            <td class="py-2 px-3">{{ $barang->pos_tarif }}</td>
                            <td class="py-2 px-3">{{ $barang->uraian }}</td>
                            <td class="py-2 px-3">{{ number_format($barang->jumlah, 6) }}</td>
                            <td class="py-2 px-3">{{ $opsSatuan[$barang->satuan] ?? $barang->satuan }}</td>
                            <td class="py-2 px-3">{{ number_format($barang->nilai_barang, 2) }}</td>
                            <td class="py-2 px-3">{{ number_format($barang->nilai_pabean_rp, 2) }}</td>
                            <td class="py-2 px-3">
                                <a href="{{ route('import.barang.edit', $barang->id) }}"
                                    class="text-blue-600 underline text-xs">Edit</a>
                                <span class="mx-1">|</span>
                                <form method="POST" action="{{ route('import.barang.destroy', $barang->id) }}"
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
                            <td colspan="8" class="py-4 px-3 text-center text-gray-500">Belum ada item barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
