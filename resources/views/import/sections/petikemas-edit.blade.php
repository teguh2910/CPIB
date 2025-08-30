@extends('layouts.app')

@section('title', 'Edit Peti Kemas')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Peti Kemas</h1>
                    <p class="text-gray-600 mt-1">Ubah informasi peti kemas</p>
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

                <form action="{{ route('petikemas.update', $petikemas->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid md:grid-cols-2 gap-6">
                        <x-field label="Seri">
                            <input type="number" name="seri" value="{{ old('seri', $petikemas->seri) }}" min="1"
                                class="w-full border rounded px-3 py-2" required>
                        </x-field>

                        <x-field label="Nomor Container">
                            <input type="text" name="nomor" value="{{ old('nomor', $petikemas->nomor) }}"
                                placeholder="MSCU1234567" class="w-full border rounded px-3 py-2"
                                style="text-transform:uppercase" required>
                        </x-field>

                        <x-field label="Ukuran">
                            <select name="ukuran" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih Ukuran --</option>
                                @foreach (config('import.ukuran_petikemas') as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('ukuran', $petikemas->ukuran) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </x-field>

                        <x-field label="Jenis Muatan">
                            <select name="jenis_muatan" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih Jenis Muatan --</option>
                                @foreach (config('import.jenis_muatan_petikemas') as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('jenis_muatan', $petikemas->jenis_muatan) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </x-field>

                        <x-field label="Tipe">
                            <select name="tipe" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach (config('import.tipe_petikemas') as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('tipe', $petikemas->tipe) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </x-field>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="{{ route('import.index') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
