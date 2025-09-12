@extends('layouts.app')
@section('title', 'Master Pengirim & Penjual')

@section('content')
    <div class="bg-white rounded-2xl shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Master Pengirim & Penjual</h1>
            <div class="flex gap-2">
                <a href="{{ route('parties.template') }}" class="px-3 py-2 rounded bg-blue-600 text-white text-sm">Download Template</a>
                <form method="POST" action="{{ route('parties.upload-excel') }}" enctype="multipart/form-data" class="flex gap-2">
                    @csrf
                    <input type="file" name="excel_file" accept=".xlsx,.xls" required
                        class="text-sm border rounded px-2 py-1">
                    <button type="submit" class="px-3 py-2 rounded bg-green-600 text-white text-sm">Upload Excel</button>
                </form>
                <a href="{{ route('parties.create') }}" class="px-3 py-2 rounded bg-black text-white text-sm">Tambah Baru</a>
            </div>
        </div>

        <form method="GET" class="mb-3 grid md:grid-cols-4 gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari kode/nama/alamat..."
                class="border rounded px-3 py-2">
            <select name="type" class="border rounded px-3 py-2">
                <option value="">Semua Jenis</option>
                <option value="pengirim" @selected($type === 'pengirim')>Pengirim</option>
                <option value="penjual" @selected($type === 'penjual')>Penjual</option>
            </select>
            <div class="md:col-span-2 flex gap-2">
                <button class="px-3 py-2 border rounded">Filter</button>
                <a href="{{ route('parties.index') }}" class="px-3 py-2 border rounded">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Jenis</th>
                        <th class="py-2">Kode</th>
                        <th class="py-2">Nama</th>
                        <th class="py-2">Negara</th>
                        <th class="py-2">Alamat</th>
                        <th class="py-2 w-32"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parties as $p)
                        <tr class="border-b align-top">
                            <td class="py-2 capitalize">{{ $p->type }}</td>
                            <td class="py-2 font-mono">{{ $p->code }}</td>
                            <td class="py-2">{{ $p->name }}</td>
                            <td class="py-2">{{ config('import.negara')[$p->country] ?? ($p->country ?? '-') }}</td>
                            <td class="py-2">{{ $p->address }}</td>
                            <td class="py-2">
                                <div class="flex gap-2">
                                    <a href="{{ route('parties.edit', $p) }}" class="text-blue-600 underline">Edit</a>
                                    <form method="POST" action="{{ route('parties.destroy', $p) }}"
                                        onsubmit="return confirm('Hapus master ini?');">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $parties->links() }}
        </div>
    </div>
@endsection
