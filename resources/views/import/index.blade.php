@extends('layouts.app')
@section('title', 'Daftar Pemberitahuan Impor')

@section('content')
    <div class="bg-white rounded-2xl shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Daftar Pemberitahuan Impor</h1>
            <a href="{{ route('import.create') }}" class="px-3 py-2 rounded bg-black text-white text-sm">Buat Baru</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Nomor Aju</th>
                        <th class="py-2">Supplier</th>
                        <th class="py-2">Status</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($importNotifications as $r)
                        <tr class="border-b">
                            <td class="py-2">{{ $r->headerRecord->nomor_aju ?? '-' }}</td>
                            <td class="py-2">{{ $r->entitasRecord->pengirimParty->name ?? '-' }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded text-xs border">{{ $r->status }}</span>
                            </td>
                            <td class="py-2">
                                <a href="{{ route('import.edit', $r->id) }}"
                                    class="px-2 py-1 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition duration-200">Edit</a>
                                <a href=""
                                    class="px-2 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200">Kirim
                                    CIESA</a>
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
            {{ $importNotifications->links() }}
        </div>
    </div>
@endsection
