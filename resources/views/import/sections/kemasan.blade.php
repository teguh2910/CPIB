@php
    $k = $draft ?? [];
    $opsJenisKemasan = config('import.jenis_kemasan');
    $opsUkuran = config('import.ukuran_petikemas');
    $opsJenisMuatan = config('import.jenis_muatan_petikemas');
    $opsTipe = config('import.tipe_petikemas');

    $prefKemasan = $k['kemasan'] ?? [];
    $prefPetiKemas = $k['petikemas'] ?? [];
@endphp

<div x-data="kemasanCrud()" x-init="init()" class="space-y-6">
    <div class="grid md:grid-cols-2 gap-4">
        {{-- ================== KOLOM KIRI: KEMASAN ================== --}}
        <div class="space-y-3">
            <div class="bg-gray-50 border rounded-xl p-4">
                <h3 class="font-semibold mb-3"
                    x-text="kemasan.editingIndex===null ? 'Tambah Kemasan' : ('Edit Kemasan #'+(kemasan.editingIndex+1))">
                </h3>
                <div class="grid md:grid-cols-4 gap-3">
                    <x-field label="Seri">
                        <input type="number" min="1" x-model.number="kemasan.form.seri"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                    <x-field label="Jumlah">
                        <input type="number" min="0" step="0.0001" x-model="kemasan.form.jumlah"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                    <x-field label="Jenis">
                        <select x-ref="jenisKemasanSelect" class="w-full border rounded px-3 py-2 select2-basic">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach ($opsJenisKemasan as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Merek">
                        <input x-model="kemasan.form.merek" class="w-full border rounded px-3 py-2">
                    </x-field>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="button" class="px-3 py-2 rounded bg-black text-white text-sm"
                        @click="kemasan.save()">Simpan</button>
                    <button type="button" class="px-3 py-2 rounded border text-sm"
                        @click="kemasan.resetForm()">Bersihkan</button>
                </div>
            </div>

            <div class="bg-white border rounded-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 px-3">Seri</th>
                                <th class="py-2 px-3">Jumlah</th>
                                <th class="py-2 px-3">Jenis</th>
                                <th class="py-2 px-3">Merek</th>
                                <th class="py-2 px-3 w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, idx) in kemasan.items" :key="'k-' + idx">
                                <tr class="border-b">
                                    <td class="py-2 px-3" x-text="row.seri"></td>
                                    <td class="py-2 px-3" x-text="row.jumlah"></td>
                                    <td class="py-2 px-3" x-text="labelKemasan(row.jenis)"></td>
                                    <td class="py-2 px-3" x-text="row.merek || '-'"></td>
                                    <td class="py-2 px-3">
                                        <button type="button" class="text-blue-600 underline text-xs"
                                            @click="kemasan.edit(idx)">Edit</button>
                                        <span class="mx-1">|</span>
                                        <button type="button" class="text-red-600 underline text-xs"
                                            @click="kemasan.remove(idx)">Hapus</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="kemasan.items.length===0">
                                <td colspan="5" class="py-4 px-3 text-center text-gray-500">Belum ada kemasan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============== KOLOM KANAN: PETI KEMAS ============== --}}
        <div class="space-y-3">
            <div class="bg-gray-50 border rounded-xl p-4">
                <h3 class="font-semibold mb-3"
                    x-text="petikemas.editingIndex===null ? 'Tambah Peti Kemas' : ('Edit Peti Kemas #'+(petikemas.editingIndex+1))">
                </h3>
                <div class="grid md:grid-cols-5 gap-3">
                    <x-field label="Seri">
                        <input type="number" min="1" x-model.number="petikemas.form.seri"
                            class="w-full border rounded px-3 py-2">
                    </x-field>
                    <x-field label="No. Cont">
                        <input x-model="petikemas.form.nomor" placeholder="MSCU1234567"
                            class="w-full border rounded px-3 py-2" style="text-transform:uppercase">
                    </x-field>
                    <x-field label="Ukuran">
                        <select x-ref="ukuranSelect" class="w-full border rounded px-3 py-2 select2-basic">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsUkuran as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Jenis Muatan">
                        <select x-ref="jenisMuatanSelect" class="w-full border rounded px-3 py-2 select2-basic">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsJenisMuatan as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Tipe">
                        <select x-ref="tipeSelect" class="w-full border rounded px-3 py-2 select2-basic">
                            <option value="">-- Pilih --</option>
                            @foreach ($opsTipe as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="button" class="px-3 py-2 rounded bg-black text-white text-sm"
                        @click="petikemas.save()">Simpan</button>
                    <button type="button" class="px-3 py-2 rounded border text-sm"
                        @click="petikemas.resetForm()">Bersihkan</button>
                </div>
            </div>

            <div class="bg-white border rounded-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 px-3">Seri</th>
                                <th class="py-2 px-3">Nomor</th>
                                <th class="py-2 px-3">Ukuran</th>
                                <th class="py-2 px-3">Jenis Muatan</th>
                                <th class="py-2 px-3">Tipe</th>
                                <th class="py-2 px-3 w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, idx) in petikemas.items" :key="'c-' + idx">
                                <tr class="border-b">
                                    <td class="py-2 px-3" x-text="row.seri"></td>
                                    <td class="py-2 px-3" x-text="row.nomor"></td>
                                    <td class="py-2 px-3" x-text="labelUkuran(row.ukuran)"></td>
                                    <td class="py-2 px-3" x-text="labelJenisMuatan(row.jenis_muatan)"></td>
                                    <td class="py-2 px-3" x-text="labelTipe(row.tipe)"></td>
                                    <td class="py-2 px-3">
                                        <button type="button" class="text-blue-600 underline text-xs"
                                            @click="petikemas.edit(idx)">Edit</button>
                                        <span class="mx-1">|</span>
                                        <button type="button" class="text-red-600 underline text-xs"
                                            @click="petikemas.remove(idx)">Hapus</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="petikemas.items.length===0">
                                <td colspan="6" class="py-4 px-3 text-center text-gray-500">Belum ada peti kemas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden inputs agar ikut terkirim ke backend --}}
    <div class="hidden">
        {{-- kemasan --}}
        <template x-for="(row, idx) in kemasan.items" :key="'hk-' + idx">
            <div>
                <input type="hidden" :name="`kemasan[${idx}][seri]`" :value="row.seri">
                <input type="hidden" :name="`kemasan[${idx}][jumlah]`" :value="row.jumlah">
                <input type="hidden" :name="`kemasan[${idx}][jenis]`" :value="row.jenis">
                <input type="hidden" :name="`kemasan[${idx}][merek]`" :value="row.merek">
            </div>
        </template>
        {{-- peti kemas --}}
        <template x-for="(row, idx) in petikemas.items" :key="'hc-' + idx">
            <div>
                <input type="hidden" :name="`petikemas[${idx}][seri]`" :value="row.seri">
                <input type="hidden" :name="`petikemas[${idx}][nomor]`" :value="row.nomor">
                <input type="hidden" :name="`petikemas[${idx}][ukuran]`" :value="row.ukuran">
                <input type="hidden" :name="`petikemas[${idx}][jenis_muatan]`" :value="row.jenis_muatan">
                <input type="hidden" :name="`petikemas[${idx}][tipe]`" :value="row.tipe">
            </div>
        </template>
    </div>

    {{-- Prefill --}}
    <textarea id="prefill_kemasan" class="hidden">@json($prefKemasan)</textarea>
    <textarea id="prefill_petikemas" class="hidden">@json($prefPetiKemas)</textarea>
</div>

@push('scripts')
    <script>
        function kemasanCrud() {
            return {
                // KEMASAN
                kemasan: {
                    items: [],
                    form: {
                        seri: 1,
                        jumlah: '',
                        jenis: '',
                        merek: ''
                    },
                    editingIndex: null,
                    save() {
                        if (!this.form.jumlah || !this.form.jenis) {
                            alert('Lengkapi Jumlah & Jenis.');
                            return;
                        }
                        const payload = {
                            seri: parseInt(this.form.seri || (this.items.length + 1), 10),
                            jumlah: this.form.jumlah,
                            jenis: this.form.jenis,
                            merek: (this.form.merek || '').trim(),
                        };
                        if (this.editingIndex === null) this.items.push(payload);
                        else this.items[this.editingIndex] = payload;
                        this.reseq();
                        this.resetForm();
                    },
                    edit(i) {
                        this.editingIndex = i;
                        this.form = JSON.parse(JSON.stringify(this.items[i]));
                        Alpine.nextTick(() => {
                            const $s = $(Alpine.$data(document.querySelector('[x-data]')).$refs.jenisKemasanSelect);
                            $s.val(this.form.jenis || '').trigger($.fn.select2 ? 'change' : 'input');
                        });
                    },
                    remove(i) {
                        this.items.splice(i, 1);
                        this.reseq();
                        if (this.editingIndex === i) this.resetForm();
                    },
                    resetForm() {
                        this.editingIndex = null;
                        this.form = {
                            seri: (this.items.length + 1),
                            jumlah: '',
                            jenis: '',
                            merek: ''
                        };
                        Alpine.nextTick(() => {
                            const $s = $(Alpine.$data(document.querySelector('[x-data]')).$refs.jenisKemasanSelect);
                            $s.val('').trigger($.fn.select2 ? 'change' : 'input');
                        });
                    },
                    reseq() {
                        this.items.forEach((it, i) => it.seri = i + 1);
                    },
                },

                // PETI KEMAS
                petikemas: {
                    items: [],
                    form: {
                        seri: 1,
                        nomor: '',
                        ukuran: '',
                        jenis_muatan: '',
                        tipe: ''
                    },
                    editingIndex: null,
                    save() {
                        // fallback uppercase + simple guard
                        this.form.nomor = (this.form.nomor || '').toUpperCase().replace(/\s+/g, '');
                        if (!this.form.nomor || !this.form.ukuran || !this.form.jenis_muatan || !this.form.tipe) {
                            alert('Lengkapi Nomor, Ukuran, Jenis Muatan, dan Tipe.');
                            return;
                        }
                        const nomorRegex = /^[A-Z]{4}\d{7}$/;
                        if (!nomorRegex.test(this.form.nomor)) {
                            alert('Nomor kontainer harus 4 huruf besar diikuti 7 digit (contoh: MSCU1234567).');
                            return;
                        }
                        const payload = {
                            seri: parseInt(this.form.seri || (this.items.length + 1), 10),
                            nomor: this.form.nomor,
                            ukuran: this.form.ukuran,
                            jenis_muatan: this.form.jenis_muatan,
                            tipe: this.form.tipe,
                        };
                        if (this.editingIndex === null) this.items.push(payload);
                        else this.items[this.editingIndex] = payload;
                        this.reseq();
                        this.resetForm();
                    },
                    edit(i) {
                        this.editingIndex = i;
                        this.form = JSON.parse(JSON.stringify(this.items[i]));
                        Alpine.nextTick(() => {
                            const d = Alpine.$data(document.querySelector('[x-data]'));
                            $(d.$refs.ukuranSelect).val(this.form.ukuran || '').trigger($.fn.select2 ? 'change' :
                                'input');
                            $(d.$refs.jenisMuatanSelect).val(this.form.jenis_muatan || '').trigger($.fn.select2 ?
                                'change' : 'input');
                            $(d.$refs.tipeSelect).val(this.form.tipe || '').trigger($.fn.select2 ? 'change' :
                                'input');
                        });
                    },
                    remove(i) {
                        this.items.splice(i, 1);
                        this.reseq();
                        if (this.editingIndex === i) this.resetForm();
                    },
                    resetForm() {
                        this.editingIndex = null;
                        this.form = {
                            seri: (this.items.length + 1),
                            nomor: '',
                            ukuran: '',
                            jenis_muatan: '',
                            tipe: ''
                        };
                        Alpine.nextTick(() => {
                            const d = Alpine.$data(document.querySelector('[x-data]'));
                            $(d.$refs.ukuranSelect).val('').trigger($.fn.select2 ? 'change' : 'input');
                            $(d.$refs.jenisMuatanSelect).val('').trigger($.fn.select2 ? 'change' : 'input');
                            $(d.$refs.tipeSelect).val('').trigger($.fn.select2 ? 'change' : 'input');
                        });
                    },
                    reseq() {
                        this.items.forEach((it, i) => it.seri = i + 1);
                    },
                },

                // LABEL helper
                optsKemasan: @json($opsJenisKemasan),
                optsUkuran: @json($opsUkuran),
                optsMuatan: @json($opsJenisMuatan),
                optsTipe: @json($opsTipe),

                labelKemasan(v) {
                    return this.optsKemasan[v] || v || '-';
                },
                labelUkuran(v) {
                    return this.optsUkuran[v] || v || '-';
                },
                labelJenisMuatan(v) {
                    return this.optsMuatan[v] || v || '-';
                },
                labelTipe(v) {
                    return this.optsTipe[v] || v || '-';
                },

                init() {
                    // Prefill
                    try {
                        const a = JSON.parse(document.getElementById('prefill_kemasan').value || '[]');
                        this.kemasan.items = Array.isArray(a) ? a : [];
                    } catch {
                        this.kemasan.items = [];
                    }
                    try {
                        const b = JSON.parse(document.getElementById('prefill_petikemas').value || '[]');
                        this.petikemas.items = Array.isArray(b) ? b : [];
                    } catch {
                        this.petikemas.items = [];
                    }

                    this.kemasan.reseq();
                    this.kemasan.resetForm();
                    this.petikemas.reseq();
                    this.petikemas.resetForm();

                    // Init Select2 all
                    Alpine.nextTick(() => {
                        if ($.fn.select2) {
                            $('.select2-basic').each(function() {
                                const $el = $(this);
                                if (!$el.data('select2')) $el.select2({
                                    width: '100%'
                                });
                            });
                        }
                        // Bind perubahan select -> model
                        const d = this;
                        $(this.$refs.jenisKemasanSelect).on('change', function() {
                            d.kemasan.form.jenis = this.value || '';
                        });
                        $(this.$refs.ukuranSelect).on('change', function() {
                            d.petikemas.form.ukuran = this.value || '';
                        });
                        $(this.$refs.jenisMuatanSelect).on('change', function() {
                            d.petikemas.form.jenis_muatan = this.value || '';
                        });
                        $(this.$refs.tipeSelect).on('change', function() {
                            d.petikemas.form.tipe = this.value || '';
                        });
                    });
                }
            }
        }

        // Fallback global: ensure select2
        $(function() {
            if ($.fn.select2) {
                $('.select2-basic').each(function() {
                    if (!$(this).data('select2')) $(this).select2({
                        width: '100%'
                    });
                });
            }
        });
    </script>
@endpush
