@extends('layouts.app')
@section('title', 'Tambah Master')

@section('content')
    <div class="bg-white rounded-2xl shadow p-4">
        <h1 class="text-xl font-semibold mb-4">Tambah Master Pengirim/Penjual</h1>
        <form method="POST" action="{{ route('parties.store') }}" class="space-y-4">
            @csrf
            @include('parties._form', ['party' => null, 'countries' => $countries])
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('parties.index') }}" class="px-3 py-2 border rounded">Batal</a>
                <button class="px-3 py-2 rounded bg-black text-white">Simpan</button>
            </div>
        </form>
    </div>
@endsection
