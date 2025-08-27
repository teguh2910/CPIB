<div class="grid md:grid-cols-3 gap-4">
    <x-field label="Invoice No">
        <input name="invoice_no" class="w-full border rounded px-3 py-2"
            value="{{ old('invoice_no', $draft['invoice_no'] ?? '') }}" required>
    </x-field>
    <x-field label="Invoice Tgl">
        <input type="date" name="invoice_tgl" class="w-full border rounded px-3 py-2"
            value="{{ old('invoice_tgl', $draft['invoice_tgl'] ?? '') }}" required>
    </x-field>
    <div></div>

    <x-field label="Packing List No">
        <input name="packing_list_no" class="w-full border rounded px-3 py-2"
            value="{{ old('packing_list_no', $draft['packing_list_no'] ?? '') }}">
    </x-field>
    <x-field label="Packing List Tgl">
        <input type="date" name="packing_list_tgl" class="w-full border rounded px-3 py-2"
            value="{{ old('packing_list_tgl', $draft['packing_list_tgl'] ?? '') }}">
    </x-field>
    <div></div>

    <x-field label="B/L / AWB No">
        <input name="bl_awb_no" class="w-full border rounded px-3 py-2"
            value="{{ old('bl_awb_no', $draft['bl_awb_no'] ?? '') }}">
    </x-field>
    <x-field label="B/L / AWB Tgl">
        <input type="date" name="bl_awb_tgl" class="w-full border rounded px-3 py-2"
            value="{{ old('bl_awb_tgl', $draft['bl_awb_tgl'] ?? '') }}">
    </x-field>
    <div></div>

    <x-field label="L/C No">
        <input name="lc_no" class="w-full border rounded px-3 py-2" value="{{ old('lc_no', $draft['lc_no'] ?? '') }}">
    </x-field>
    <x-field label="L/C Tgl">
        <input type="date" name="lc_tgl" class="w-full border rounded px-3 py-2"
            value="{{ old('lc_tgl', $draft['lc_tgl'] ?? '') }}">
    </x-field>
</div>
