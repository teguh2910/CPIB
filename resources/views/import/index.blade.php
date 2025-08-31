@extends('layouts.app')
@section('title', 'Daftar Pemberitahuan Impor')

@section('content')
    <div class="bg-white rounded-2xl shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">List Pemberitahuan Impor Barang</h1>
            <div class="flex gap-2">
                <a href="{{ route('import.create') }}" class="px-3 py-2 rounded bg-black text-white text-sm">Buat Baru</a>
                <!-- <a href="{{ route('import.exportAll') }}" class="px-3 py-2 rounded bg-blue-600 text-white text-sm">Export Semua (Excel)</a> -->
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Nomor Aju</th>
                        <th class="py-2">Supplier</th>
                        <th class="py-2">Nama Kapal</th>
                        <th class="py-2">No. Voy</th>
                        <th class="py-2">ETA</th>
                        <th class="py-2">Status</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($importNotifications as $r)
                        <tr class="border-b">
                            <td class="py-2">{{ $r->nomor_aju ?? '-' }}</td>
                            <td class="py-2">{{ $r->name ?? '-' }}</td>
                            <td class="py-2">{{ $r->angkut_nama ?? '-' }}</td>
                            <td class="py-2">{{ $r->angkut_voy ?? '-' }}</td>
                            <td class="py-2">{{ $r->tanggal_tiba ?? '-' }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded text-xs border">{{ $r->status }}</span>
                            </td>
                            <td class="py-2">
                                <a href="{{ route('import.edit', $r->id) }}"
                                    class="px-2 py-1 bg-green-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200">Edit</a>
                                    <a href="{{ route('import.export', $r->id) }}"
                                    class="px-2 py-1 bg-green-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200">Export</a>
                                    <a href="{{ route('import.export.json', $r->id) }}"
                                    class="ml-2 px-2 py-1 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Kirim
                                    CIESA(under dev)</a>
                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
        </div>
    </div>
@endsection
