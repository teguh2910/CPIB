@php
    $draftItems = $draft['items'] ?? [];
    $totalCifDraft = collect($draftItems)->sum(fn($i) => (float) ($i['nilai_cif'] ?? 0));
@endphp

<div class="space-y-4" x-data="trxForm()">
    <div class="grid md:grid-cols-3 gap-4">
        <x-field label="Valuta">
            <input name="valuta" class="w-full border rounded px-3 py-2"
                value="{{ old('valuta', $draft['valuta'] ?? 'USD') }}" required>
        </x-field>
        <x-field label="Kurs (opsional)">
            <input type="number" step="0.0001" min="0" name="kurs" class="w-full border rounded px-3 py-2"
                value="{{ old('kurs', $draft['kurs'] ?? '') }}">
        </x-field>
        <x-field label="Incoterm">
            <input name="incoterm" class="w-full border rounded px-3 py-2"
                value="{{ old('incoterm', $draft['incoterm'] ?? '') }}">
        </x-field>

        <x-field label="FOB">
            <input type="number" step="0.01" min="0" name="fob" class="w-full border rounded px-3 py-2"
                x-model.number="fob">
        </x-field>
        <x-field label="Freight">
            <input type="number" step="0.01" min="0" name="freight" class="w-full border rounded px-3 py-2"
                x-model.number="freight">
        </x-field>
        <x-field label="Insurance">
            <input type="number" step="0.01" min="0" name="insurance" class="w-full border rounded px-3 py-2"
                x-model.number="insurance">
        </x-field>

        <x-field label="CIF (otomatis/dapat manual)">
            <input type="number" step="0.01" min="0" name="cif" class="w-full border rounded px-3 py-2"
                :value="cifCalc()" oninput="this.setAttribute('value', this.value)">
        </x-field>
        <x-field label="Cara Pembayaran">
            <select name="cara_pembayaran" class="w-full border rounded px-3 py-2">
                @foreach (['TT', 'LC', 'OpenAccount', 'Consignment', 'Lainnya'] as $opt)
                    <option value="{{ $opt }}" @selected(old('cara_pembayaran', $draft['cara_pembayaran'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </x-field>
        <x-field label="Negara Muatan">
            <input name="negara_muatan" class="w-full border rounded px-3 py-2"
                value="{{ old('negara_muatan', $draft['negara_muatan'] ?? '') }}">
        </x-field>
    </div>

    <div class="p-3 rounded border bg-gray-50 text-sm">
        <div><strong>Ringkasan:</strong></div>
        <div>Total CIF (dari Barang): {{ number_format($totalCifDraft, 2, ',', '.') }}</div>
        <div x-show="(fob||0)+(freight||0)+(insurance||0)>0">CIF dihitung (FOB+Freight+Insurance): <span
                x-text="format(cifCalc())"></span></div>
    </div>
</div>

<script>
    function trxForm() {
        return {
            fob: parseFloat(@json(old('fob', $draft['fob'] ?? 0))) || 0,
            freight: parseFloat(@json(old('freight', $draft['freight'] ?? 0))) || 0,
            insurance: parseFloat(@json(old('insurance', $draft['insurance'] ?? 0))) || 0,
            cifCalc() {
                return (this.fob || 0) + (this.freight || 0) + (this.insurance || 0);
            },
            format(n) {
                return new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(n || 0);
            }
        }
    }
</script>
