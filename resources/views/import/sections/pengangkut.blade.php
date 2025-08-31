@php
    $opsCara = config('import.cara_pengangkutan');
    // Normalize ETA value for date input (YYYY-MM-DD)
    $etaRaw = $ang['eta'] ?? ($pengan['angkut_eta'] ?? null);
    $etaValue = '';
    if (!empty($etaRaw)) {
        // Try to extract ISO date fragment first
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $etaRaw, $m)) {
            $etaValue = $m[1];
        } else {
            try {
                $etaValue = \Carbon\Carbon::parse($etaRaw)->format('Y-m-d');
            } catch (\Exception $ex) {
                // fallback to raw value if parsing fails
                $etaValue = $etaRaw;
            }
        }
    }
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- ====== KOLOM KIRI ====== --}}
    <div class="space-y-4">
        {{-- Section BC 1.1 --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">BC 1.1</h3>
            <div class="grid grid-cols-3 gap-3">
                <x-field label="kode_tutup_pu">
                    <select name="kode_tutup_pu" class="w-full border rounded px-3 py-2">
                        <option value="">-- Pilih Kode --</option>
                        <option value="11" selected>BC 1.1</option>
                        <option value="12">BC 1.2</option>
                        <option value="14">BC 1.4</option>
                    </select>
                </x-field>
                <x-field label="nomor_bc_11">
                    <input name="nomor_bc_11" class="w-full border rounded px-3 py-2"
                        value="{{ old('nomor_bc_11', $pengangkut[0]->nomor_bc_11 ?? '') }}">
                </x-field>
                <x-field label="tanggal_bc_11">
                    <input type="date" name="tanggal_bc_11" class="w-full border rounded px-3 py-2"
                        value="{{ old('tanggal_bc_11', $pengangkut[0]->tanggal_bc_11 ?? '') }}">
                </x-field>
                </div>
                <div>
                    <label class="block text-sm mb-1">nomor_pos & nomor_sub_pos</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="nomor_pos" class="w-full border rounded px-3 py-2"
                            value="{{ old('nomor_pos', $pengangkut[0]->nomor_pos ?? '') }}" placeholder="nomor_pos">
                        <input type="number" name="nomor_sub_pos" class="w-full border rounded px-3 py-2"
                            value="{{ old('nomor_sub_pos', $pengangkut[0]->nomor_sub_pos ?? '') }}" placeholder="Subpos">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Pengangkutan --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Pengangkutan</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Cara Pengangkutan">
                        <select name="angkut_cara" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih Cara --</option>
                            @foreach ($opsCara as $k => $v)
                                <option value="{{ $k }}" @if ((string) old('angkut_cara', $pengangkut[0]->angkut_cara ?? '') === (string) $k) selected @endif>
                                    {{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>

                    <x-field label="Nama Sarana Angkut">
                        <input type="text" name="angkut_nama" class="w-full border rounded px-3 py-2"
                            value="{{ old('angkut_nama', $pengangkut[0]->angkut_nama ?? '') }}">
                    </x-field>
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Nomor Voy/Flight">
                        <input name="angkut_voy" class="w-full border rounded px-3 py-2"
                            value="{{ old('angkut_voy', $pengangkut[0]->angkut_voy ?? '') }}">
                    </x-field>

                    <x-field label="Bendera">
                        <select name="angkut_bendera" id="angkut_bendera" class="w-full border rounded px-3 py-2 negara-select">
                            <option value="">-- Pilih Negara --</option>
                            @if (!empty($ang['bendera']) || !empty($pengangkut[0]->angkut_bendera))
                                <option value="{{ $ang['bendera'] ?? $pengangkut[0]->angkut_bendera }}" selected>
                                    {{ $ang['bendera'] ?? $pengangkut[0]->angkut_bendera }}
                                </option>
                            @endif
                        </select>
                    </x-field>
                </div>

                <x-field label="Perkiraan Tanggal Tiba (ETA)">
                    <input type="date" name="tanggal_tiba" class="w-full border rounded px-3 py-2"
                        value="{{ old('tanggal_tiba', $pengangkut[0]->tanggal_tiba ?? $etaValue) }}">
                </x-field>
            </div>
        </div>
    </div>

    {{-- ====== KOLOM KANAN ====== --}}
    <div class="space-y-4">
        {{-- Section Pelabuhan & Tempat Penimbunan --}}
        <div class="bg-gray-50 border rounded-xl p-4 mt-4 space-y-3">
            <h3 class="font-semibold">Pelabuhan & Tempat Penimbunan</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-4 gap-3">
                    <x-field label="Pelabuhan Muat">
                        <select name="kode_pelabuhan_muat" id="kode_pelabuhan_muat" class="w-full border rounded px-3 py-2 pelabuhan-select">
                            <option value="">-- Pilih --</option>
                            @if (!empty($pel['muat']) || !empty($pengangkut[0]->kode_pelabuhan_muat))
                                <option value="{{ $pel['muat'] ?? $pengangkut[0]->kode_pelabuhan_muat }}" selected>
                                    {{ $pel['muat'] ?? $pengangkut[0]->kode_pelabuhan_muat }}
                                </option>
                            @endif
                        </select>
                    </x-field>
                    <x-field label="Pelabuhan Transit">
                        <select name="kode_pelabuhan_transit" id="kode_pelabuhan_transit" class="w-full border rounded px-3 py-2 pelabuhan-select">
                            <option value="">-- Pilih --</option>
                            @if (!empty($pel['transit']) || !empty($pengangkut[0]->kode_pelabuhan_transit))
                                <option value="{{ $pel['transit'] ?? $pengangkut[0]->kode_pelabuhan_transit }}" selected>
                                    {{ $pel['transit'] ?? $pengangkut[0]->kode_pelabuhan_transit }}
                                </option>
                            @endif
                        </select>
                    </x-field>
                    <x-field label="Pelabuhan Tujuan">
                        <select name="kode_pelabuhan_tujuan" id="kode_pelabuhan_tujuan" class="w-full border rounded px-3 py-2 pelabuhan-tujuan-select">
                            <option value="">-- Pilih --</option>
                            @if (!empty($pel['tujuan']) || !empty($pengangkut[0]->kode_pelabuhan_tujuan))
                                <option value="{{ $pel['tujuan'] ?? $pengangkut[0]->kode_pelabuhan_tujuan }}" selected>
                                    {{ $pel['tujuan'] ?? $pengangkut[0]->kode_pelabuhan_tujuan }}
                                </option>
                            @endif
                        </select>
                    </x-field>
                    <x-field label="Tempat Penimbunan">
                        <select name="kode_tps" id="kode_tps" class="w-full border rounded px-3 py-2 tps-select">
                            <option value="">-- Pilih --</option>
                            @if (!empty($tps['kode']) || !empty($pengangkut[0]->kode_tps))
                                <option value="{{ $tps['kode'] ?? $pengangkut[0]->kode_tps }}" selected>
                                    {{ $tps['kode'] ?? $pengangkut[0]->kode_tps }}
                                </option>
                            @endif
                        </select>
                    </x-field>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {

    // Initialize Select2 for pelabuhan selects
    $('.pelabuhan-select').select2({
        placeholder: '-- Pilih Pelabuhan --',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: '{{ route("ajax.pelabuhan.search") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function(data) {
                if ((data.status === 'OK' || data.status === true) && data.data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.kodePelabuhan,
                                text: item.namaPelabuhan + ' (' + item.kodePelabuhan + ')'
                            };
                        })
                    };
                }
                return { results: [] };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: function (item) {
            if (item.loading) return item.text;
            return item.text;
        },
        templateSelection: function (item) {
            return item.text || item.id;
        }
    });

    // Initialize Select2 for TPS selects
    $('.tps-select').select2({
        placeholder: '-- Pilih Tempat Penimbunan --',
        allowClear: true,
        minimumInputLength: 0, // Allow search from first character
        ajax: {
            url: '/ajax/tps/search',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    q: params.term || '' // search term
                };
            },
            processResults: function(data) {
                if ((data.status === 'OK' || data.status === true) && data.data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.kodeGudang,
                                text: item.namaGudang + ' (' + item.kodeGudang + ')'
                            };
                        })
                    };
                }
                return { results: [] };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: function (item) {
            if (item.loading) return item.text;
            return item.text;
        },
        templateSelection: function (item) {
            return item.text || item.id;
        }
    });

    // Initialize Select2 for negara selects
    $('.negara-select').select2({
        placeholder: '-- Pilih Negara --',
        allowClear: true,
        minimumInputLength: 0, // Allow search from first character
        ajax: {
            url: '{{ route("ajax.negara.search") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    q: params.term || '' // search term
                };
            },
            processResults: function(data) {
                if ((data.status === 'OK' || data.status === true) && data.data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.kodeNegara,
                                text: item.namaNegara + ' (' + item.kodeNegara + ')'
                            };
                        })
                    };
                }
                return { results: [] };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: function (item) {
            if (item.loading) return item.text;
            return item.text;
        },
        templateSelection: function (item) {
            return item.text || item.id;
        }
    });

    // Initialize Select2 for pelabuhan tujuan selects
    console.log('Initializing Pelabuhan Tujuan Select2...');
    $('.pelabuhan-tujuan-select').select2({
        placeholder: '-- Pilih Pelabuhan Tujuan --',
        allowClear: true,
        minimumInputLength: 0, // Allow search from first character
        ajax: {
            url: '{{ route("ajax.pelabuhan-tujuan.search") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    q: params.term || '' // search term
                };
            },
            processResults: function(data) {
                console.log('Pelabuhan Tujuan - API Response:', data);
                if ((data.status === true) && data.data) {
                    console.log('Pelabuhan Tujuan - Processing', data.data.length, 'results');
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.kodePelabuhan,
                                text: item.namaPelabuhan + ' (' + item.kodePelabuhan + ')'
                            };
                        })
                    };
                }
                console.log('Pelabuhan Tujuan - No valid data or status check failed');
                return { results: [] };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: function (item) {
            if (item.loading) return item.text;
            return item.text;
        },
        templateSelection: function (item) {
            return item.text || item.id;
        }
    });

    // Update TPS and Pelabuhan Tujuan options when kode kantor changes
    $('#kode_kantor').on('change', function() {
        console.log('Kode kantor changed, clearing selections...');
        // Clear current TPS selection
        $('#kode_tps').val(null).trigger('change');
        // Clear current Pelabuhan Tujuan selection
        $('#kode_pelabuhan_tujuan').val(null).trigger('change');
    });

    // Handle form submission to ensure selected values are preserved
    $('form').on('submit', function() {
        $('.pelabuhan-select, .tps-select, .pelabuhan-tujuan-select, .negara-select').each(function() {
            var selectedValue = $(this).val();
            if (selectedValue) {
                // Ensure the selected option exists in the DOM
                if ($(this).find('option[value="' + selectedValue + '"]').length === 0) {
                    $(this).append('<option value="' + selectedValue + '" selected>' + selectedValue + '</option>');
                }
            }
        });
    });
});
</script>
@endpush
