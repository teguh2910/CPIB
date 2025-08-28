@php
  $t = $draft ?? [];

  $harga = $t['harga'] ?? [];
  $biaya = $t['biaya'] ?? [];
  $berat = $t['berat'] ?? [];

  $opsValuta   = config('import.jenis_valuta');
  $opsJenis    = config('import.jenis_transaksi');
  $opsIncoterm = config('import.incoterm');
  $opsAsuransi = config('import.jenis_asuransi');
@endphp

<div x-data="trxForm()" x-init="init()" class="grid md:grid-cols-2 gap-4">
  {{-- ====== KOLOM KIRI ====== --}}
  <div class="space-y-4">
    {{-- HARGA --}}
    <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
      <h3 class="font-semibold">Harga</h3>
      <div class="grid grid-cols-1 gap-3">
        <div class="grid md:grid-cols-2 gap-3">
          <x-field label="Jenis Valuta">
            <select name="harga[valuta]" class="w-full border rounded px-3 py-2 select2"
                    data-selected="{{ old('harga.valuta', $harga['valuta'] ?? '') }}" required>
              <option value="">-- Pilih --</option>
              @foreach($opsValuta as $k=>$v)
                <option value="{{ $k }}" @selected(old('harga.valuta', $harga['valuta'] ?? '') === $k)>{{ $v }}</option>
              @endforeach
            </select>
          </x-field>
          <x-field label="NDPBM">
            <input x-model="ndpbm" inputmode="decimal" name="harga[ndpbm]" class="w-full border rounded px-3 py-2"
                   value="{{ old('harga.ndpbm', $harga['ndpbm'] ?? '') }}" required>
          </x-field>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
          <x-field label="Jenis Transaksi">
            <select name="harga[jenis]" class="w-full border rounded px-3 py-2 select2"
                    data-selected="{{ old('harga.jenis', $harga['jenis'] ?? '') }}" required>
              <option value="">-- Pilih --</option>
              @foreach($opsJenis as $k=>$v)
                <option value="{{ $k }}" @selected(old('harga.jenis', $harga['jenis'] ?? '') === $k)>{{ $v }}</option>
              @endforeach
            </select>
          </x-field>
          <x-field label="Incoterm">
            <select name="harga[incoterm]" class="w-full border rounded px-3 py-2 select2"
                    data-selected="{{ old('harga.incoterm', $harga['incoterm'] ?? '') }}" required>
              <option value="">-- Pilih --</option>
              @foreach($opsIncoterm as $k=>$v)
                <option value="{{ $k }}" @selected(old('harga.incoterm', $harga['incoterm'] ?? '') === $k)>{{ $v }}</option>
              @endforeach
            </select>
          </x-field>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
          <x-field label="Harga Barang">
            <input x-model="hargaBarang" inputmode="decimal" name="harga[harga_barang]" class="w-full border rounded px-3 py-2"
                   value="{{ old('harga.harga_barang', $harga['harga_barang'] ?? '') }}" required>
          </x-field>
          <x-field label="Nilai Pabean (NDPBM × Harga)">
            <input x-model="nilaiPabeanDisplay" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
            <input type="hidden" name="harga[nilai_pabean]" :value="nilaiPabean"> {{-- submit ke backend --}}
          </x-field>
        </div>
      </div>
    </div>

    {{-- BERAT --}}
    <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
      <h3 class="font-semibold">Berat</h3>
      <div class="grid md:grid-cols-2 gap-3">
        <x-field label="Berat Kotor (KGM)">
          <input inputmode="decimal" name="berat[kotor]" class="w-full border rounded px-3 py-2"
                 value="{{ old('berat.kotor', $berat['kotor'] ?? '') }}" required>
        </x-field>
        <x-field label="Berat Bersih (KGM)">
          <input inputmode="decimal" name="berat[bersih]" class="w-full border rounded px-3 py-2"
                 value="{{ old('berat.bersih', $berat['bersih'] ?? '') }}" required>
        </x-field>
      </div>
    </div>
  </div>

  {{-- ====== KOLOM KANAN ====== --}}
  <div class="space-y-4">
    {{-- BIAYA LAINNYA --}}
    <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
      <h3 class="font-semibold">Biaya Lainnya</h3>
      <div class="grid grid-cols-1 gap-3">
        <div class="grid md:grid-cols-2 gap-3">
          <x-field label="Biaya Penambah">
            <input inputmode="decimal" name="biaya[penambah]" class="w-full border rounded px-3 py-2"
                   value="{{ old('biaya.penambah', $biaya['penambah'] ?? '') }}">
          </x-field>
          <x-field label="Biaya Pengurang">
            <input inputmode="decimal" name="biaya[pengurang]" class="w-full border rounded px-3 py-2"
                   value="{{ old('biaya.pengurang', $biaya['pengurang'] ?? '') }}">
          </x-field>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
          <x-field label="Freight">
            <input inputmode="decimal" name="biaya[freight]" class="w-full border rounded px-3 py-2"
                   value="{{ old('biaya.freight', $biaya['freight'] ?? '') }}">
          </x-field>
          <x-field label="Jenis Asuransi">
            <select name="biaya[jenis_asuransi]" class="w-full border rounded px-3 py-2 select2"
                    data-selected="{{ old('biaya.jenis_asuransi', $biaya['jenis_asuransi'] ?? 'NONE') }}" required>
              @foreach($opsAsuransi as $k=>$v)
                <option value="{{ $k }}" @selected(old('biaya.jenis_asuransi', $biaya['jenis_asuransi'] ?? 'NONE') === $k)>{{ $v }}</option>
              @endforeach
            </select>
          </x-field>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
          <x-field label="Amount Asuransi">
            <input inputmode="decimal" name="biaya[asuransi]" class="w-full border rounded px-3 py-2"
                   value="{{ old('biaya.asuransi', $biaya['asuransi'] ?? '') }}">
          </x-field>
          <div class="grid grid-cols-1 gap-1">
            <label class="text-sm">Voluntary Declaration</label>
            <div class="flex items-center gap-3">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="biaya[voluntary_on]" value="1"
                       @checked(old('biaya.voluntary_on', $biaya['voluntary_on'] ?? false))>
                <span class="text-sm">Aktif</span>
              </label>
              <input inputmode="decimal" name="biaya[voluntary_amt]" placeholder="Amount"
                     class="w-full border rounded px-3 py-2"
                     value="{{ old('biaya.voluntary_amt', $biaya['voluntary_amt'] ?? '') }}">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function trxForm(){
  return {
    ndpbm: '{{ old('harga.ndpbm', $harga['ndpbm'] ?? '') }}',
    hargaBarang: '{{ old('harga.harga_barang', $harga['harga_barang'] ?? '') }}',
    get nilaiPabean(){
      const a = parseFloat((this.ndpbm||'').toString().replace(/,/g,'')) || 0;
      const b = parseFloat((this.hargaBarang||'').toString().replace(/,/g,'')) || 0;
      return +(a * b).toFixed(2);
    },
    get nilaiPabeanDisplay(){
      return this.nilaiPabean.toLocaleString(undefined, {maximumFractionDigits: 2});
    },
    init(){
      // Select2 init & set value
      $('.select2').select2({ width:'100%' });
      $('.select2').each(function(){
        var v = $(this).data('selected');
        if(v!==undefined && v!==null && v!==''){ $(this).val(String(v)).trigger('change'); }
      });
    }
  }
}
</script>
@endpush
