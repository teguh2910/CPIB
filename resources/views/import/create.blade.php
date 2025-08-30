@extends('layouts.app')

@section('title', 'Create Import Notification')

@php
    $steps = ['header', 'entitas', 'dokumen', 'pengangkut', 'kemasan', 'transaksi', 'barang', 'pungutan', 'pernyataan'];
    $current = request('step', 'header');
    $stepIndex = array_search($current, $steps, true);

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

    // Initialize draft data from session or old input
    $draft = session('import_draft', []);
    if (empty($draft) && old()) {
        // Reconstruct draft from old input
        foreach ($steps as $step) {
            $draft[$step] = old($step, []);
        }
    }
@endphp

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800">Create Import Notification</h1>
                    <p class="text-gray-600 mt-1">Fill in the details for the new import notification - Step
                        {{ $stepIndex + 1 }} of {{ count($steps) }}</p>
                </div>

                <!-- Progress Indicator -->
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progress</span>
                        <span class="text-sm text-gray-500">{{ $stepIndex + 1 }} of {{ count($steps) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                            style="width: {{ (($stepIndex + 1) / count($steps)) * 100 }}%"></div>
                    </div>
                    <div class="flex justify-between mt-2">
                        @foreach ($steps as $index => $step)
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                                    {{ $index <= $stepIndex ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                    {{ $index + 1 }}
                                </div>
                                <span class="text-xs mt-1 {{ $index <= $stepIndex ? 'text-blue-600' : 'text-gray-500' }}">
                                    {{ $labels[$step] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Step Navigation -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($steps as $index => $step)
                            <a href="{{ route('import.create') }}?step={{ $step }}"
                                class="px-3 py-2 rounded text-sm border transition-colors
                                {{ $current === $step ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                {{ $labels[$step] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('import.store') }}" method="POST" class="p-6">
                    @csrf

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

                    <!-- Step Content -->
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ $labels[$current] }}</h2>
                        @include('import.sections.' . $current, ['draft' => $draft[$current] ?? []])
                    </div>

                    <!-- Hidden step indicator -->
                    <input type="hidden" name="current_step" value="{{ $current }}">

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div>
                            @php
                                $prevStep = $stepIndex > 0 ? $steps[$stepIndex - 1] : null;
                            @endphp
                            @if ($prevStep)
                                <a href="{{ route('import.create') }}?step={{ $prevStep }}"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">
                                    Previous
                                </a>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            @php
                                $nextStep = $stepIndex < count($steps) - 1 ? $steps[$stepIndex + 1] : null;
                            @endphp

                            @if ($nextStep)
                                @if ($current === 'dokumen' || $current === 'kemasan' || $current === 'barang')
                                    <a href="{{ route('import.create') }}?step={{ $nextStep }}"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 inline-block">
                                        Continue
                                    </a>
                                @else
                                    <button type="submit" name="action" value="save_continue" id="saveContinueBtn"
                                        data-next-step="{{ $nextStep }}"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                        Save & Continue
                                    </button>
                                @endif
                            @else
                                <button type="submit" name="action" value="submit"
                                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200">
                                    Create Import Notification
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
