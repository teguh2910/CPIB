@php
    // Calculate CIF basis
    $cifBasis = $cifBasis ?? 0;
@endphp

<div class="space-y-6">
    {{-- Form Edit Pungutan --}}
    <div class="bg-gray-50 border rounded-xl p-4">
        <h3 class="font-semibold mb-3">Edit Perhitungan Pungutan</h3>

        <form method="POST" action="{{ route('import.pungutan.update', $pungutan->id) }}">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-3 gap-4">
                <x-field label="BM (%)">
                    <input type="number" step="0.01" min="0" name="bm_percent"
                        value="{{ old('bm_percent', $pungutan->bm_percent ?? '') }}"
                        class="w-full border rounded px-3 py-2" required>
                </x-field>
                <x-field label="PPN (%)">
                    <input type="number" step="0.01" min="0" name="ppn_percent"
                        value="{{ old('ppn_percent', $pungutan->ppn_percent ?? '') }}"
                        class="w-full border rounded px-3 py-2" required>
                </x-field>
                <x-field label="PPh (%)">
                    <input type="number" step="0.01" min="0" name="pph_percent"
                        value="{{ old('pph_percent', $pungutan->pph_percent ?? '') }}"
                        class="w-full border rounded px-3 py-2" required>
                </x-field>
            </div>

            <div class="grid md:grid-cols-4 gap-4 mt-4">
                <x-field label="Basis CIF">
                    <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                        value="{{ number_format($cifBasis, 2, ',', '.') }}" readonly>
                </x-field>
                <x-field label="Bea Masuk">
                    <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                        value="{{ number_format($pungutan->bea_masuk ?? 0, 2, ',', '.') }}" readonly>
                </x-field>
                <x-field label="PPN">
                    <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                        value="{{ number_format($pungutan->ppn ?? 0, 2, ',', '.') }}" readonly>
                </x-field>
                <x-field label="PPh">
                    <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                        value="{{ number_format($pungutan->pph ?? 0, 2, ',', '.') }}" readonly>
                </x-field>
            </div>

            <div class="mt-4 text-right">
                <div class="inline-flex items-center gap-2 font-semibold text-lg">
                    <span>Total Pungutan:</span>
                    <input type="text" class="w-48 border rounded px-3 py-2 bg-blue-50 text-right font-bold"
                        value="{{ number_format($pungutan->total_pungutan ?? 0, 2, ',', '.') }}" readonly>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Update Pungutan
                </button>
                <a href="{{ route('import.pungutan.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Batal
                </a>
                <form method="POST" action="{{ route('import.pungutan.destroy', $pungutan->id) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus data pungutan ini?')">
                        Hapus
                    </button>
                </form>
            </div>
        </form>
    </div>

    {{-- Informasi Perhitungan --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <h4 class="font-semibold mb-2 text-blue-800">Informasi Perhitungan</h4>
        <div class="text-sm text-blue-700 space-y-1">
            <p><strong>Basis CIF:</strong> Nilai CIF dari transaksi atau total nilai pabean barang</p>
            <p><strong>Bea Masuk:</strong> CIF × (BM % ÷ 100)</p>
            <p><strong>PPN:</strong> (CIF + Bea Masuk) × (PPN % ÷ 100)</p>
            <p><strong>PPh:</strong> (CIF + Bea Masuk) × (PPh % ÷ 100)</p>
            <p><strong>Total Pungutan:</strong> Bea Masuk + PPN + PPh</p>
        </div>
    </div>
</div>
