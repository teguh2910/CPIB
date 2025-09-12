@extends('layouts.app')
@section('title', 'Detail Master Pengirim & Penjual')

@section('content')
    <div class="bg-white rounded-2xl shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Detail Master Pengirim & Penjual</h1>
            <div class="flex gap-2">
                <a href="{{ route('parties.edit', $party) }}" class="px-3 py-2 rounded bg-blue-600 text-white text-sm">Edit</a>
                <a href="{{ route('parties.index') }}" class="px-3 py-2 border rounded">Kembali</a>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold mb-3">Informasi Dasar</h3>
                <div class="space-y-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis</label>
                        <p class="text-sm capitalize">{{ $party->type }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode</label>
                        <p class="text-sm font-mono">{{ $party->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <p class="text-sm">{{ $party->name }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-semibold mb-3">Informasi Tambahan</h3>
                <div class="space-y-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Negara</label>
                        <p class="text-sm">{{ config('import.negara')[$party->country] ?? ($party->country ?? '-') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <p class="text-sm">{{ $party->address ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dibuat</label>
                        <p class="text-sm">{{ $party->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Diubah</label>
                        <p class="text-sm">{{ $party->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
