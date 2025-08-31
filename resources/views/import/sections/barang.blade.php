{{-- Header dengan tombol tambah --}}
<div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Daftar Barang</h3>
    <a href="{{ route('barang.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
        + Tambah Barang
    </a>

</div>

{{-- Upload CSV / Excel (CSV) --}}
@php $showUpload = $showUpload ?? true; @endphp
@if ($showUpload)
    <div class="mb-4">
        @if (session('import_success'))
            <div class="p-2 bg-green-100 text-green-800 rounded mb-2 text-sm">{{ session('import_success') }}</div>
        @endif
        @if (session('import_errors'))
            <div class="p-2 bg-yellow-100 text-yellow-800 rounded mb-2 text-sm">
                <strong>Beberapa baris gagal diimpor:</strong>
                <ul class="list-disc pl-5 mt-1 text-xs">
                    @foreach (session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @csrf
        <input type="file" name="file" class="border border-gray-300 rounded px-2 py-1 text-sm" />
        <button type="submit" name="action" value="import"
            class="px-3 py-1 bg-red-600 text-white rounded text-sm">Upload Excel
            </button>
        <a href="{{ url('/template.xlsx') }}" class="ml-2 px-3 py-1 bg-yellow-600 text-white rounded text-sm">Download Template
            </a>
        <!-- <button type="submit" name="action" value="updateAll"
            class="ml-2 px-3 py-1 bg-green-600 text-white rounded text-sm">Generate Pungutan</button>
        <span id="update-all-status" class="ml-3 text-sm"></span> -->
    </div>
@endif

{{-- Tabel List Barang --}}
<div class="bg-white border rounded-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2 px-3">Seri</th>
                    <th class="py-2 px-3">HS</th>
                    <th class="py-2 px-3">uraian</th>
                    <th class="py-2 px-3">Jumlah</th>
                    <th class="py-2 px-3">Satuan</th>
                    <th class="py-2 px-3">CIF</th>
                    <th class="py-2 px-3">CIF Rupiah</th>
                    <th class="py-2 px-3 w-28">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangData as $barang)
                    <tr class="border-b align-top">
                        <td class="py-2 px-3">{{ $barang->seri_barang }}</td>
                        <td class="py-2 px-3">{{ $barang->hs }}</td>
                        <td class="py-2 px-3">{{ $barang->uraian }}</td>
                        <td class="py-2 px-3">{{ number_format($barang->jumlah_satuan, 0) }}</td>
                        <td class="py-2 px-3">{{ $opsSatuan[$barang->kode_satuan] ?? $barang->kode_satuan }}</td>
                        <td class="py-2 px-3">{{ number_format($barang->cif, 0) }}</td>
                        <td class="py-2 px-3">{{ number_format($barang->cif_rupiah, 0) }}</td>
                        <td class="py-2 px-3">
                            <a href="{{ route('barang.edit', $barang->id) }}"
                                class="text-blue-600 underline text-xs">Edit</a>
                            <span class="mx-1">|</span>
                            <button type="button" class="text-red-600 underline text-xs"
                                onclick="deleteBarang({{ $barang->id }})">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 px-3 text-center text-gray-500">Belum ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Delete function for barang
    window.deleteBarang = function(id) {
        if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url('barang') }}/' + id;
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
</script>
