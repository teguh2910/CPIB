@extends('layouts.app')

@section('title', 'Wizard Pemberitahuan Impor')

@php
    $labels = [
        'header' => 'Header',
        'entitas' => 'Entitas',
        'dokumen' => 'Dokumen',
        'pengangkut' => 'Pengangkut',
        'kemasan' => 'Kemasan & Peti Kemas',
        'transaksi' => 'Transaksi',
        'barang' => 'Barang',
        'pungutan' => 'Pungutan',
        'pernyataan' => 'Pernyataan',
    ];
@endphp

@section('content')
    <div class="bg-white rounded-2xl shadow p-4">
        {{-- Tabs --}}
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach ($steps as $s)
                <a href="{{ route('wizard.show', $s) }}"
                    class="px-3 py-2 rounded text-sm border
                    {{ $current === $s ? 'bg-black text-white' : 'bg-white hover:bg-gray-50' }}">
                    {{ $labels[$s] ?? ucfirst($s) }}
                </a>
            @endforeach
        </div>

        {{-- Form per-step --}}
        <form method="POST" action="{{ route('wizard.store', $current) }}" class="space-y-4">
            @csrf
            @if ($errors->any())
                <div class="p-3 bg-red-50 text-red-700 rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- SECTION FIELDS --}}
            @include('import.sections.' . $current, ['draft' => $draft[$current] ?? []])

            {{-- NAV BUTTONS --}}
            @php
                $idx = array_search($current, $steps, true);
                $prev = $idx > 0 ? $steps[$idx - 1] : null;
                $next = $idx < count($steps) - 1 ? $steps[$idx + 1] : null;
            @endphp

            <div class="flex items-center justify-between pt-4 border-t">
                <div>
                    @if ($prev)
                        <a href="{{ route('wizard.show', $prev) }}" class="px-4 py-2 rounded border">Previous</a>
                    @endif
                </div>
                <div class="flex gap-2">
                    @if ($next)
                        <button type="submit" class="px-4 py-2 rounded bg-black text-white">Save & Next</button>
                    @else
                        <button type="submit" class="px-4 py-2 rounded bg-emerald-600 text-white">Submit</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
