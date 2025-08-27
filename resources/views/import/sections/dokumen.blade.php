@php
    $d = $draft ?? [];
    $jenisOpts = config('import.jenis_dokumen');
@endphp

<div x-data="dokumenCrud()" x-init="init()" class="space-y-4">
    {{-- FORM ADD/EDIT --}}
    <div class="bg-gray-50 border rounded-xl p-4">
        <h3 class="font-semibold mb-3"
            x-text="editingIndex===null ? 'Tambah Dokumen' : 'Edit Dokumen #'+(editingIndex+1)"></h3>
        <div class="grid md:grid-cols-4 gap-3">
            <x-field label="Seri">
                <input type="number" readonly min="1" placeholder="Auto Generate" x-model.number="form.seri"
                    class="w-full border rounded px-3 py-2">
            </x-field>

            <x-field label="Jenis Dokumen">
                <select x-ref="jenisSelect" class="w-full border rounded px-3 py-2 select2-basic">
                    <option value="">-- Pilih jenis dokumen --</option>
                    @foreach ($jenisOpts as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </x-field>

            <x-field label="Nomor Dokumen">
                <input x-model="form.nomor" class="w-full border rounded px-3 py-2">
            </x-field>

            <x-field label="Tanggal Dokumen">
                <input type="date" x-model="form.tanggal" class="w-full border rounded px-3 py-2">
            </x-field>
        </div>

        <div class="mt-3 flex gap-2">
            <button type="button" class="px-3 py-2 rounded bg-black text-white text-sm" @click="save()">Simpan</button>
            <button type="button" class="px-3 py-2 rounded border text-sm" @click="resetForm()">Bersihkan</button>
        </div>
    </div>

    {{-- TABEL LIST --}}
    <div class="bg-white border rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2 px-3">Seri</th>
                        <th class="py-2 px-3">Jenis</th>
                        <th class="py-2 px-3">Nomor</th>
                        <th class="py-2 px-3">Tanggal</th>
                        <th class="py-2 px-3 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, idx) in items" :key="idx">
                        <tr class="border-b">
                            <td class="py-2 px-3" x-text="row.seri"></td>
                            <td class="py-2 px-3" x-text="jenisLabel(row.jenis)"></td>
                            <td class="py-2 px-3" x-text="row.nomor"></td>
                            <td class="py-2 px-3" x-text="row.tanggal"></td>
                            <td class="py-2 px-3">
                                <button type="button" class="text-blue-600 underline text-xs"
                                    @click="edit(idx)">Edit</button>
                                <span class="mx-1">|</span>
                                <button type="button" class="text-red-600 underline text-xs"
                                    @click="remove(idx)">Hapus</button>
                            </td>
                        </tr>
                    </template>



                    <tr x-show="items.length===0">
                        <td colspan="5" class="py-4 px-3 text-center text-gray-500">Belum ada dokumen. Tambahkan di
                            atas.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hidden input for items as JSON --}}
    <input type="hidden" name="items_json" :value="JSON.stringify(items)">

    {{-- Script to pass prefill data to Alpine --}}
    <script>
        window.dokumenPrefillData = @json($d['items'] ?? []);
    </script>
</div>

{{-- Alpine & Select2 sudah di-include di layout sebelumnya --}}
@push('scripts')
    <script>
        function dokumenCrud() {
            return {
                items: [],
                form: {
                    seri: 1,
                    jenis: '',
                    nomor: '',
                    tanggal: ''
                },
                editingIndex: null,
                jenisOpts: @json($jenisOpts),

                init() {
                    // Prefill list from window variable
                    try {
                        const pre = window.dokumenPrefillData || [];
                        this.items = Array.isArray(pre) ? pre : [];
                    } catch (e) {
                        console.error('Error loading prefill data:', e);
                        this.items = [];
                    }
                    this.resequence();
                    this.updateHiddenInput();
                    this.resetForm();

                    // Init Select2 dengan guard supaya error tidak memutus Alpine
                    this.$nextTick(() => {
                        try {
                            const $sel = $(this.$refs.jenisSelect);
                            if (window.jQuery && $.fn.select2 && !$sel.data('select2')) {
                                $sel.select2({
                                    width: '100%'
                                });
                            }
                            // sinkronisasi nilai ke form
                            $sel.off('change.dokumen').on('change.dokumen', () => {
                                this.form.jenis = $sel.val() || '';
                            });
                            // set nilai awal
                            $sel.val(this.form.jenis || '').trigger($.fn.select2 ? 'change' : 'input');
                        } catch (e) {
                            console.error('Init Select2 gagal:', e);
                        }
                    });

                    // Add event listener for Save & Next button
                    this.$nextTick(() => {
                        const saveNextBtn = document.getElementById('save-next-btn');
                        if (saveNextBtn) {
                            saveNextBtn.addEventListener('click', (e) => {
                                // Update hidden input with current items
                                const hiddenInput = this.$el.querySelector('input[name="items_json"]');
                                if (hiddenInput) {
                                    hiddenInput.value = JSON.stringify(this.items);
                                }

                                if (this.items.length === 0) {
                                    if (this.form.jenis && this.form.nomor && this.form.tanggal) {
                                        // Auto save current form data
                                        this.save();
                                        // Update hidden input again after save
                                        setTimeout(() => {
                                            if (hiddenInput) {
                                                hiddenInput.value = JSON.stringify(this.items);
                                            }
                                        }, 10);
                                    } else {
                                        alert(
                                            'Tambahkan minimal 1 dokumen dengan mengisi form dan klik Simpan, atau isi form lengkap untuk auto-save.'
                                        );
                                        e.preventDefault();
                                        return false;
                                    }
                                }
                            });
                        }
                    });
                },

                jenisLabel(code) {
                    return this.jenisOpts[code] || code || '-';
                },

                resequence() {
                    this.items.forEach((it, i) => it.seri = i + 1);
                },

                edit(i) {
                    this.editingIndex = i;
                    this.form = JSON.parse(JSON.stringify(this.items[i] || {
                        seri: 1,
                        jenis: '',
                        nomor: '',
                        tanggal: ''
                    }));
                    this.$nextTick(() => {
                        const $sel = $(this.$refs.jenisSelect);
                        $sel.val(this.form.jenis || '');
                        ($.fn.select2 ? $sel.trigger('change') : $sel.trigger('input'));
                    });
                },

                remove(i) {
                    this.items.splice(i, 1);
                    this.resequence();
                    this.updateHiddenInput();
                    if (this.editingIndex === i) {
                        this.resetForm();
                    }
                },

                save() {
                    // Fallback: kalau Select2/change belum sempat set this.form.jenis, ambil langsung dari DOM
                    if (!this.form.jenis && this.$refs.jenisSelect) {
                        this.form.jenis = this.$refs.jenisSelect.value || '';
                    }

                    if (!this.form.jenis || !this.form.nomor || !this.form.tanggal) {
                        alert('Lengkapi Jenis, Nomor, dan Tanggal.');
                        return;
                    }

                    const payload = {
                        seri: parseInt(this.form.seri || (this.items.length + 1), 10),
                        jenis: this.form.jenis,
                        nomor: (this.form.nomor || '').trim(),
                        tanggal: this.form.tanggal
                    };

                    if (this.editingIndex === null) this.items.push(payload);
                    else this.items[this.editingIndex] = payload;

                    this.resequence();
                    this.updateHiddenInput();
                    this.resetForm();
                },

                resetForm() {
                    this.editingIndex = null;
                    this.form = {
                        seri: (this.items.length + 1),
                        jenis: '',
                        nomor: '',
                        tanggal: ''
                    };
                    this.$nextTick(() => {
                        const $sel = $(this.$refs.jenisSelect);
                        $sel.val('').trigger($.fn.select2 ? 'change' : 'input');
                    });
                },

                // Update hidden input whenever items change
                updateHiddenInput() {
                    this.$nextTick(() => {
                        const hiddenInput = this.$el.querySelector('input[name="items_json"]');
                        if (hiddenInput) {
                            hiddenInput.value = JSON.stringify(this.items);
                        }
                    });
                },
            }
        }

        // Fallback global: kalau ada .select2-basic yang belum ke-init, init sekarang (tidak dobel-init).
        $(function() {
            if ($.fn.select2) {
                $('.select2-basic').each(function() {
                    const $el = $(this);
                    if (!$el.data('select2')) {
                        $el.select2({
                            width: '100%'
                        });
                    }
                });
            }
        });
    </script>
@endpush
