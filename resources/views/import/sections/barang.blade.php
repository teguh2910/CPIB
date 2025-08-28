@php
  $b = $draft ?? [];
  $itemsPref = $b['items'] ?? [];

  $opsNegara = config('import.negara');
  $opsSatuan = config('import.satuan_barang');
  $opsKemasan= config('import.jenis_kemasan');
  $opsSpek   = config('import.spesifikasi_lain');
  $opsKond   = config('import.kondisi_barang');
  $opsKetBM  = config('import.ket_bm');
  $opsKetPPN = config('import.ket_ppn');
  $opsKetPPH = config('import.ket_pph');

  $ndpbm = (string)($allDraft['transaksi']['harga']['ndpbm'] ?? 0);

  // Ambil daftar dokumen untuk dropdown fasilitas/lartas
  $dokList = collect($allDraft['dokumen']['items'] ?? [])->map(function($d){
    $jenis = config('import.jenis_dokumen')[$d['jenis'] ?? ''] ?? ($d['jenis'] ?? '');
    $tgl   = $d['tanggal'] ?? '';
    $no    = $d['nomor'] ?? '';
    return trim("$jenis - $no ($tgl)");
  })->values()->all();
@endphp

<div x-data="barangCrud()" x-init="init()" class="space-y-6">
  {{-- Form Tambah/Edit --}}
  <div class="bg-gray-50 border rounded-xl p-4">
    <h3 class="font-semibold mb-3" x-text="editingIndex===null ? 'Tambah Barang' : 'Edit Barang #'+(editingIndex+1)"></h3>

    <div class="grid md:grid-cols-2 gap-5">
      {{-- ===== Informasi Dasar ===== --}}
      <div class="space-y-3">
        <h4 class="font-medium">Informasi Dasar</h4>
        <div class="grid grid-cols-1 gap-3">
          <x-field label="POS Tarif">
            <input x-model="form.pos_tarif" placeholder="HS Code" class="w-full border rounded px-3 py-2" required>
          </x-field>

          <div>
            <label class="text-sm">Pernyataan Lartas</label>
            <div class="flex gap-5 mt-1">
              <label class="inline-flex items-center gap-2">
                <input type="radio" value="1" x-model.number="form.lartas"> <span>Ya</span>
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" value="0" x-model.number="form.lartas"> <span>Tidak</span>
              </label>
            </div>
          </div>

          <x-field label="Kode Barang">
            <input x-model="form.kode_barang" class="w-full border rounded px-3 py-2">
          </x-field>

          <x-field label="Uraian Jenis Barang">
            <textarea x-model="form.uraian" class="w-full border rounded px-3 py-2" rows="2" required></textarea>
          </x-field>

          <div class="grid md:grid-cols-2 gap-3">
            <x-field label="Spesifikasi Lain">
              <input x-ref="speks" class="w-full border rounded px-3 py-2">
            </x-field>
            <x-field label="Kondisi Barang">
              <select x-ref="kondisi" class="w-full border rounded px-3 py-2 select2-basic">
                <option value="">-- Pilih --</option>
                @foreach($opsKond as $k=>$v)
                <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </x-field>
          </div>

          <div class="grid md:grid-cols-2 gap-3">
            <x-field label="Negara Asal">
              <select x-ref="negara" class="w-full border rounded px-3 py-2 select2-basic">
                <option value="">-- Pilih --</option>
                @foreach($opsNegara as $k=>$v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </x-field>
            <x-field label="Berat Bersih (Kg)">
              <input inputmode="decimal" x-model="form.berat_bersih" class="w-full border rounded px-3 py-2" required>
            </x-field>
          </div>
        </div>
      </div>

      {{-- ===== Jumlah & Kemasan + Nilai & Keuangan (atas) ===== --}}
      <div class="space-y-3">
        <h4 class="font-medium">Jumlah & Kemasan</h4>
        <div class="grid grid-cols-1 gap-3">
          <div class="grid md:grid-cols-2 gap-3">
            <x-field label="Jumlah Satuan Barang">
              <input inputmode="decimal" x-model="form.jumlah" class="w-full border rounded px-3 py-2" required>
            </x-field>
            <x-field label="Jenis Satuan">
              <select x-ref="satuan" class="w-full border rounded px-3 py-2 select2-basic">
                <option value="">-- Pilih --</option>
                @foreach($opsSatuan as $k=>$v)
                <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </x-field>
          </div>

          <div class="grid md:grid-cols-2 gap-3">
            <x-field label="Jumlah Kemasan">
              <input inputmode="decimal" x-model="form.jml_kemasan" class="w-full border rounded px-3 py-2">
            </x-field>
            <x-field label="Jenis Kemasan">
              <select x-ref="jkemasan" class="w-full border rounded px-3 py-2 select2-basic">
                <option value="">-- Pilih --</option>
                @foreach($opsKemasan as $k=>$v)
                <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </x-field>
          </div>
        </div>

        <h4 class="font-medium mt-4">Informasi Nilai & Keuangan</h4>
        <div class="grid grid-cols-1 gap-3">
          <div class="grid md:grid-cols-2 gap-3">
            <x-field label="Nilai Barang (Amount)">
              <input inputmode="decimal" x-model="form.nilai_barang" class="w-full border rounded px-3 py-2" required>
            </x-field>
            <x-field label="FOB">
              <input inputmode="decimal" x-model="form.fob" class="w-full border rounded px-3 py-2">
            </x-field>
          </div>

          <div class="grid md:grid-cols-2 gap-3">
            <x-field label="Freight">
              <input inputmode="decimal" x-model="form.freight" class="w-full border rounded px-3 py-2">
            </x-field>
            <x-field label="Asuransi">
              <input inputmode="decimal" x-model="form.asuransi" class="w-full border rounded px-3 py-2">
            </x-field>
          </div>

          <div class="grid md:grid-cols-2 gap-3">
            <x-field label="Harga Satuan (Nilai/Jumlah)">
              <input :value="fmt(hargaSatuan())" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
            </x-field>
            <x-field label="Nilai Pabean Rupiah (NDPBM × Nilai)">
              <input :value="fmt(nilaiPabeanRp())" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
            </x-field>
          </div>

          <x-field label="Dokumen Fasilitas/Lartas">
            <select x-ref="dokfas" class="w-full border rounded px-3 py-2 select2-basic">
              <option value="">-- Pilih Dokumen --</option>
              @foreach($dokList as $label)
                <option value="{{ $label }}">{{ $label }}</option>
              @endforeach
            </select>
          </x-field>
        </div>
      </div>
    </div>

    {{-- ===== Pungutan ===== --}}
    <div class="mt-6 grid md:grid-cols-3 gap-4">
      <div class="bg-white border rounded-xl p-4 space-y-2">
        <h4 class="font-medium">BM (Bea Masuk)</h4>
        <x-field label="Keterangan">
          <select x-ref="ketbm" class="w-full border rounded px-3 py-2 select2-basic">
            <option value="">-- Pilih --</option>
            @foreach($opsKetBM as $k=>$v) <option value="{{ $k }}">{{ $v }}</option> @endforeach
          </select>
        </x-field>
        <x-field label="Tarif (%)">
          <input inputmode="decimal" x-model="form.tarif_bm" class="w-full border rounded px-3 py-2">
        </x-field>
        <x-field label="Bayar BM (Rp)">
          <input :value="fmt(bayarBM())" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
        </x-field>
      </div>

      <div class="bg-white border rounded-xl p-4 space-y-2">
        <h4 class="font-medium">PPN</h4>
        <x-field label="Tarif (11% / 12%)">
          <select x-model.number="form.ppn_tarif" class="w-full border rounded px-3 py-2">
            <option :value="0">0%</option>
            <option :value="11">11%</option>
            <option :value="12">12%</option>
          </select>
        </x-field>
        <x-field label="Keterangan">
          <select x-ref="ketppn" class="w-full border rounded px-3 py-2 select2-basic">
            @foreach($opsKetPPN as $k=>$v) <option value="{{ $k }}">{{ $v }}</option> @endforeach
          </select>
        </x-field>
        <x-field label="Bayar PPN (Rp)">
          <input :value="fmt(bayarPPN())" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
        </x-field>
      </div>

      <div class="bg-white border rounded-xl p-4 space-y-2">
        <h4 class="font-medium">PPH</h4>
        <x-field label="Keterangan">
          <select x-ref="ketpph" class="w-full border rounded px-3 py-2 select2-basic">
            <option value="">-- Pilih --</option>
            @foreach($opsKetPPH as $k=>$v) <option value="{{ $k }}">{{ $v }}</option> @endforeach
          </select>
        </x-field>
        <x-field label="Tarif (%)">
          <input inputmode="decimal" x-model="form.tarif_pph" class="w-full border rounded px-3 py-2">
        </x-field>
        <x-field label="Bayar PPH (Rp)">
          <input :value="fmt(bayarPPH())" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
        </x-field>
      </div>
    </div>

    <div class="mt-4 flex gap-2">
      <button type="button" class="px-3 py-2 rounded bg-black text-white text-sm" @click="save()">Simpan Item</button>
      <button type="button" class="px-3 py-2 rounded border text-sm" @click="resetForm()">Bersihkan</button>
    </div>
  </div>

  {{-- Tabel List Barang --}}
  <div class="bg-white border rounded-xl">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left border-b">
            <th class="py-2 px-3">Seri</th>
            <th class="py-2 px-3">POS</th>
            <th class="py-2 px-3">Uraian</th>
            <th class="py-2 px-3">Jumlah</th>
            <th class="py-2 px-3">Satuan</th>
            <th class="py-2 px-3">Nilai</th>
            <th class="py-2 px-3">Pabean Rp</th>
            <th class="py-2 px-3 w-28">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="(row, idx) in items" :key="idx">
            <tr class="border-b align-top">
              <td class="py-2 px-3" x-text="row.seri"></td>
              <td class="py-2 px-3" x-text="row.pos_tarif"></td>
              <td class="py-2 px-3" x-text="row.uraian"></td>
              <td class="py-2 px-3" x-text="row.jumlah"></td>
              <td class="py-2 px-3" x-text="row.satuan"></td>
              <td class="py-2 px-3" x-text="fmt(row.nilai_barang)"></td>
              <td class="py-2 px-3" x-text="fmt(row.nilai_pabean_rp)"></td>
              <td class="py-2 px-3">
                <button type="button" class="text-blue-600 underline text-xs" @click="edit(idx)">Edit</button>
                <span class="mx-1">|</span>
                <button type="button" class="text-red-600 underline text-xs" @click="remove(idx)">Hapus</button>
              </td>
            </tr>
          </template>
          <tr x-show="items.length===0">
            <td colspan="8" class="py-4 px-3 text-center text-gray-500">Belum ada item barang.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- Hidden inputs agar terkirim --}}
  <div class="hidden">
    <template x-for="(row, idx) in items" :key="'h-'+idx">
      <div>
        <template x-for="(v,k) in row" :key="k">
          <input type="hidden" :name="`items[${idx}][${k}]`" :value="v">
        </template>
      </div>
    </template>
  </div>

  {{-- Prefill --}}
  <textarea id="prefill_barang" class="hidden">@json($itemsPref)</textarea>
  <input type="hidden" id="ndpbm_global" value="{{ $ndpbm }}">
</div>

@push('scripts')
<script>
function barangCrud(){
  return {
    items: [],
    editingIndex: null,
    ndpbm: 0,
    form: {
      seri: 1,
      // dasar
      pos_tarif:'', lartas:0, kode_barang:'', uraian:'', spesifikasi:'', kondisi:'', negara_asal:'', berat_bersih:'',
      // jumlah & kemasan
      jumlah:'', satuan:'', jml_kemasan:'', jenis_kemasan:'',
      // nilai
      nilai_barang:'', fob:'', freight:'', asuransi:'', harga_satuan:'', nilai_pabean_rp:'', dokumen_fasilitas:'',
      // pungutan
      ket_bm:'', tarif_bm:'', bayar_bm:'',
      ppn_tarif: 11, ket_ppn:'PPN', bayar_ppn:'',
      ket_pph:'', tarif_pph:'', bayar_pph:''
    },

    // helpers angka
    num(v){ const n = parseFloat((v??'').toString().replace(/,/g,'')); return isNaN(n)?0:n; },
    fmt(n){ return (n??0).toLocaleString(undefined,{maximumFractionDigits:2}); },

    // rumus
    hargaSatuan(){ const jml=this.num(this.form.jumlah); const val=this.num(this.form.nilai_barang); return jml>0 ? +(val/jml).toFixed(6) : 0; },
    nilaiPabeanRp(){ const nd=this.num(this.ndpbm); const val=this.num(this.form.nilai_barang); return +(nd*val).toFixed(2); },
    dpp(){ // dasar perhitungan pajak sederhana → pakai Nilai Pabean Rp
      return this.nilaiPabeanRp();
    },
    bayarBM(){ const t=this.num(this.form.tarif_bm); return +((t/100)*this.dpp()).toFixed(2); },
    bayarPPN(){ const t=(+this.form.ppn_tarif||0); return +((t/100)*this.dpp()).toFixed(2); },
    bayarPPH(){ const t=this.num(this.form.tarif_pph); return +((t/100)*this.dpp()).toFixed(2); },

    save(){
      // sinkron dari select2 refs
      const d = this;
      ['speks','kondisi','negara','satuan','jkemasan','dokfas','ketbm','ketppn','ketpph'].forEach(ref=>{
        if (d.$refs[ref]) {
          const v = d.$refs[ref].value || '';
          if (ref==='speks') d.form.spesifikasi = v;
          if (ref==='kondisi') d.form.kondisi = v;
          if (ref==='negara') d.form.negara_asal = v;
          if (ref==='satuan') d.form.satuan = v;
          if (ref==='jkemasan') d.form.jenis_kemasan = v;
          if (ref==='dokfas') d.form.dokumen_fasilitas = v;
          if (ref==='ketbm') d.form.ket_bm = v;
          if (ref==='ketppn') d.form.ket_ppn = v;
          if (ref==='ketpph') d.form.ket_pph = v;
        }
      });

      // hitung final
      this.form.harga_satuan   = this.hargaSatuan();
      this.form.nilai_pabean_rp= this.nilaiPabeanRp();
      this.form.bayar_bm       = this.bayarBM();
      this.form.bayar_ppn      = this.bayarPPN();
      this.form.bayar_pph      = this.bayarPPH();

      // guard minimal
      if (!this.form.pos_tarif || !this.form.uraian || !this.form.negara_asal) { alert('Lengkapi POS, Uraian, Negara Asal.'); return; }
      if (this.num(this.form.jumlah)<=0 || !this.form.satuan) { alert('Lengkapi Jumlah & Satuan.'); return; }
      if (this.num(this.form.nilai_barang)<0) { alert('Nilai Barang tidak valid.'); return; }

      const payload = JSON.parse(JSON.stringify(this.form));
      if (this.editingIndex===null) this.items.push(payload);
      else this.items[this.editingIndex] = payload;

      this.reseq(); this.resetForm();
    },

    edit(i){
      this.editingIndex = i;
      this.form = JSON.parse(JSON.stringify(this.items[i]));
      Alpine.nextTick(()=>{ // sync select2
        const set = (r,val)=>{ if(this.$refs[r]) { $(this.$refs[r]).val(val||'').trigger($.fn.select2?'change':'input'); } };
        set('speks', this.form.spesifikasi);
        set('kondisi', this.form.kondisi);
        set('negara', this.form.negara_asal);
        set('satuan', this.form.satuan);
        set('jkemasan', this.form.jenis_kemasan);
        set('dokfas', this.form.dokumen_fasilitas);
        set('ketbm', this.form.ket_bm);
        set('ketppn', this.form.ket_ppn);
        set('ketpph', this.form.ket_pph);
      });
    },

    remove(i){ this.items.splice(i,1); this.reseq(); if(this.editingIndex===i) this.resetForm(); },

    resetForm(){
      const nextSeri = this.items.length+1;
      this.editingIndex = null;
      this.form = Object.assign({}, this.form, {
        seri: nextSeri, pos_tarif:'', lartas:0, kode_barang:'', uraian:'', spesifikasi:'', kondisi:'', negara_asal:'', berat_bersih:'',
        jumlah:'', satuan:'', jml_kemasan:'', jenis_kemasan:'',
        nilai_barang:'', fob:'', freight:'', asuransi:'', harga_satuan:'', nilai_pabean_rp:'', dokumen_fasilitas:'',
        ket_bm:'', tarif_bm:'', bayar_bm:'',
        ppn_tarif: 11, ket_ppn:'PPN', bayar_ppn:'',
        ket_pph:'', tarif_pph:'', bayar_pph:''
      });
      Alpine.nextTick(()=>{
        ['speks','kondisi','negara','satuan','jkemasan','dokfas','ketbm','ketppn','ketpph'].forEach(ref=>{
          if (this.$refs[ref]) $(this.$refs[ref]).val('').trigger($.fn.select2?'change':'input');
        });
      });
    },

    reseq(){ this.items.forEach((it,i)=> it.seri = i+1); },

    init(){
      // ndpbm dari hidden
      this.ndpbm = document.getElementById('ndpbm_global')?.value || 0;

      // prefill
      try {
        const pre = JSON.parse(document.getElementById('prefill_barang').value || '[]');
        this.items = Array.isArray(pre) ? pre : [];
      } catch { this.items = []; }
      this.reseq(); this.resetForm();

      // init select2
      Alpine.nextTick(()=>{
        if ($.fn.select2) {
          $('.select2-basic').each(function(){ if (!$(this).data('select2')) $(this).select2({ width:'100%' }); });
        }
        // bind change → model
        const bind = (ref, key) => { if (this.$refs[ref]) $(this.$refs[ref]).on('change', () => { this.form[key] = this.$refs[ref].value || ''; }); };
        bind('speks','spesifikasi'); bind('kondisi','kondisi'); bind('negara','negara_asal');
        bind('satuan','satuan'); bind('jkemasan','jenis_kemasan'); bind('dokfas','dokumen_fasilitas');
        // ket BM/PPN/PPH
        bind('ketbm','ket_bm'); bind('ketppn','ket_ppn'); bind('ketpph','ket_pph');
      });
    }
  }
}

// Fallback global Select2
$(function(){
  if ($.fn.select2) {
    $('.select2-basic').each(function(){ if (!$(this).data('select2')) $(this).select2({ width:'100%' }); });
  }
});
</script>
@endpush
