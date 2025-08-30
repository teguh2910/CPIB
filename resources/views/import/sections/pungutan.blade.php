<div class="space-y-6">
    {{-- Form Pungutan --}}
    <div class="bg-gray-50 border rounded-xl p-4">
        <h3 class="font-semibold mb-3">Perhitungan Pungutan</h3>

        <div class="grid md:grid-cols-1 gap-4 mt-4">
            <x-field label="Bea Masuk">
                <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                    value="{{ number_format($BM, 2, ',', '.') }}" readonly>
                <input type="hidden" name="bea_masuk" value="{{ $BM }}">
            </x-field>
            <x-field label="PPN">
                <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                    value="{{ number_format($PPN, 2, ',', '.') }}" readonly>
                <input type="hidden" name="ppn" value="{{ $PPN }}">
            </x-field>
            <x-field label="PPh">
                <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                    value="{{ number_format($PPH, 2, ',', '.') }}" readonly>
                <input type="hidden" name="pph" value="{{ $PPH }}">
            </x-field>
        </div>

        <div class="mt-3 text-right">
            <div class="inline-flex items-center gap-2 font-semibold text-lg">
                <span>Total Pungutan:</span>
                <input type="text" class="w-48 border rounded px-3 py-2 bg-blue-50 text-right font-bold"
                    value="{{ number_format($total, 2, ',', '.') }}" readonly>
                <input type="hidden" name="total" value="{{ $total }}">
            </div>
        </div>
    </div>

    {{-- Informasi Perhitungan --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <h4 class="font-semibold mb-2 text-blue-800">Informasi Perhitungan</h4>
        <div class="text-sm text-blue-700 space-y-1">
            <p><strong>Bea Masuk:</strong> CIF × (BM%)</p>
            <p><strong>PPN:</strong> (CIF + Bea Masuk) × (PPN%)</p>
            <p><strong>PPh:</strong> (CIF + Bea Masuk) × (PPh%)</p>
            <p><strong>Total Pungutan:</strong> Bea Masuk + PPN + PPh</p>
        </div>
    </div>
</div>
