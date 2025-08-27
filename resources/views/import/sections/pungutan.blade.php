@php
    $draftItems = $draft['items'] ?? [];
    $totalCifDraft = collect($draftItems)->sum(fn($i) => (float) ($i['nilai_cif'] ?? 0));
    $cifTrans = (float) ($draft['cif'] ?? 0);
    $basisCif = $cifTrans > 0 ? $cifTrans : $totalCifDraft;
@endphp

<div class="space-y-3" x-data="pungutanForm({{ $basisCif ?? 0 }})">
    <div class="grid md:grid-cols-3 gap-4">
        <x-field label="BM (%)">
            <input type="number" step="0.01" min="0" name="bm_percent" class="w-full border rounded px-3 py-2"
                x-model.number="bm">
        </x-field>
        <x-field label="PPN (%)">
            <input type="number" step="0.01" min="0" name="ppn_percent"
                class="w-full border rounded px-3 py-2" x-model.number="ppn">
        </x-field>
        <x-field label="PPh (%)">
            <input type="number" step="0.01" min="0" name="pph_percent"
                class="w-full border rounded px-3 py-2" x-model.number="pph">
        </x-field>
    </div>

    <div class="grid md:grid-cols-4 gap-4">
        <x-field label="Basis CIF">
            <input class="w-full border rounded px-3 py-2" :value="format(cif)" readonly>
        </x-field>
        <x-field label="Bea Masuk">
            <input name="bea_masuk" class="w-full border rounded px-3 py-2" :value="format(beaMasuk())" readonly>
        </x-field>
        <x-field label="PPN">
            <input name="ppn" class="w-full border rounded px-3 py-2" :value="format(ppnVal())" readonly>
        </x-field>
        <x-field label="PPh">
            <input name="pph" class="w-full border rounded px-3 py-2" :value="format(pphVal())" readonly>
        </x-field>
    </div>

    <div class="text-right font-semibold">
        Total Pungutan: <input name="total_pungutan" class="w-40 border rounded px-2 py-1 text-right"
            :value="format(total())" readonly>
    </div>
</div>

<script>
    function pungutanForm(cifBasis) {
        return {
            cif: parseFloat(cifBasis || 0),
            bm: parseFloat(@json(old('bm_percent', $draft['bm_percent'] ?? 0))) || 0,
            ppn: parseFloat(@json(old('ppn_percent', $draft['ppn_percent'] ?? 0))) || 0,
            pph: parseFloat(@json(old('pph_percent', $draft['pph_percent'] ?? 0))) || 0,
            beaMasuk() {
                return this.cif * (this.bm / 100);
            },
            ppnVal() {
                return (this.cif + this.beaMasuk()) * (this.ppn / 100);
            },
            pphVal() {
                return (this.cif + this.beaMasuk()) * (this.pph / 100);
            },
            total() {
                return this.beaMasuk() + this.ppnVal() + this.pphVal();
            },
            format(n) {
                return new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(n || 0);
            },
        }
    }
</script>
