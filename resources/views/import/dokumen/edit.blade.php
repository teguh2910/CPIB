@extends('layouts.app')

@section('title', 'Edit Dokumen')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Dokumen</h1>
                    <p class="text-gray-600 mt-1">Ubah informasi dokumen</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                        <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dokumen.update', $dokumen->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid md:grid-cols-2 gap-6">
                        <x-field label="Seri">
                            <input type="number" name="seri" value="{{ old('seri', $dokumen->seri) }}" min="1"
                                class="w-full border rounded px-3 py-2" required>
                        </x-field>

                        <x-field label="Jenis Dokumen">
                            <select name="jenis" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih jenis dokumen --</option>
                                @foreach (config('import.jenis_dokumen') as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('jenis', $dokumen->jenis) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </x-field>

                        <x-field label="Nomor Dokumen">
                            <input type="text" name="nomor" value="{{ old('nomor', $dokumen->nomor) }}"
                                class="w-full border rounded px-3 py-2" required>
                        </x-field>

                        <x-field label="Tanggal Dokumen">
                            <input type="date" name="tanggal"
                                value="{{ old('tanggal') ?: date('Y-m-d', strtotime($dokumen->tanggal)) }}"
                                class="w-full border rounded px-3 py-2" required>
                        </x-field>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="px-6 py-2 bg-black text-white rounded hover:bg-gray-800">
                            Update Dokumen
                        </button>
                        <a href="{{ route('import.create', ['step' => 'dokumen']) }}"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
