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
        <input type="file" name="file" required class="border rounded px-2 py-1 text-sm" />
        <button type="submit" name="action" value="import"
            class="px-3 py-1 bg-green-600 text-white rounded text-sm">Upload Excel
            (CSV)</button>
        <a href="{{ route('barang.template') }}" class="ml-2 text-sm text-blue-600 underline">Download Template
            CSV</a>
    </div>
@endif

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
                        <td class="py-2 px-3">{{ number_format($barang->jumlah, 0) }}</td>
                        <td class="py-2 px-3">{{ $opsSatuan[$barang->satuan] ?? $barang->satuan }}</td>
                        <td class="py-2 px-3">{{ number_format($barang->nilai_barang, 0) }}</td>
                        <td class="py-2 px-3">{{ number_format($barang->nilai_pabean_rp, 0) }}</td>
                        <td class="py-2 px-3">
                            <a href="{{ route('barang.edit', $barang->id) }}"
                                class="text-blue-600 underline text-xs">Edit</a>
                            <span class="mx-1">|</span>
                            <button type="button" class="text-red-600 underline text-xs"
                                onclick="deleteBarang({{ $barang->id }})">Hapus</button>
                        </td>
                    </tr>
                @empty
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
