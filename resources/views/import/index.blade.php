@extends('layouts.app')
@section('title', 'Daftar Pemberitahuan Impor')

@section('content')
    <div class="bg-white rounded-2xl shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Daftar Pemberitahuan Impor</h1>
            <a href="{{ route('wizard.show', 'header') }}" class="px-3 py-2 rounded bg-black text-white text-sm">Buat Baru</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">ID</th>
                        <th class="py-2">Nomor Dokumen</th>
                        <th class="py-2">Tanggal</th>
                        <th class="py-2">Importir</th>
                        <th class="py-2">Status</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $r)
                        @php
                            $h = $r->header ?? [];
                            $e = $r->entitas ?? [];
                        @endphp
                        <tr class="border-b">
                            <td class="py-2">{{ $r->id }}</td>
                            <td class="py-2">{{ $h['nomor_dokumen'] ?? '-' }}</td>
                            <td class="py-2">{{ $h['tanggal'] ?? '-' }}</td>
                            <td class="py-2">{{ $e['importir_nama'] ?? '-' }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded text-xs border">{{ $r->status }}</span>
                            </td>
                            <td class="py-2">
                                <a href="{{ route('import.show', $r->id) }}" class="underline">Detail</a>
                                <span class="mx-1">|</span>
                                <a href="{{ route('import.edit', $r->id) }}" class="underline">Edit</a>
                                <span class="mx-1">|</span>
                                <a href="{{ route('import.export.pdf', $r->id) }}" class="underline">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $records->links() }}
        </div>
    </div>
@endsection
