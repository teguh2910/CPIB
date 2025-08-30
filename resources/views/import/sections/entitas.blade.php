@php
    $e = $draft ?? [];
    // Also expose a normalized entitas array from the session draft for backward compatibility
    $_sessionDraft = session('import_draft', []);
    $ent = $_sessionDraft['entitas'] ?? [];
    $negaraOpts = config('import.negara');
    $pengirimSelected = $e['pengirim']['party_id'] ?? null;
    $penjualSelected = $e['penjual']['party_id'] ?? null;
    $statusOpts = config('import.kode_status');

    // Get parties for dropdown - handle potential errors
    try {
        $pengirimParties = \App\Models\Party::where('type', 'pengirim')->get();
        $penjualParties = \App\Models\Party::where('type', 'penjual')->get();
    } catch (\Exception $ex) {
        $pengirimParties = collect([]);
        $penjualParties = collect([]);
    }
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- ====== KOLOM KIRI ====== --}}
    <div class="space-y-4">
        {{-- IMPORTIR --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Importir</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <input type="hidden" name="kode_entitas" value="1">
                    <input type="hidden" name="kode_jenis_identitas" value="6">
                    <x-field label="NPWP">
                        <input name="importir[nomor_identitas]" class="w-full border rounded px-3 py-2"
                            value="{{ old('importir.nomor_identitas', $e['importir']['nomor_identitas'] ?? '001065305305500') }}" required>
                    </x-field>
                    <x-field label="NITKU">
                        <input name="importir[nitku]" class="w-full border rounded px-3 py-2"
                            value="{{ old('importir.nitku', $e['importir']['nitku'] ?? '001065305305500000000') }}">
                    </x-field>
                </div>
                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Nama">
                        <input name="importir[nama_identitas]" class="w-full border rounded px-3 py-2"
                            value="{{ old('importir.nama_identitas', $e['importir']['nama_identitas'] ?? 'AISIN INDONESIA') }}" required>
                    </x-field>
                    <x-field label="API/NIB">
                        <input name="importir[nib_identitas]" class="w-full border rounded px-3 py-2"
                            value="{{ old('importir.nib_identitas', $e['importir']['nib_identitas'] ?? '8120006810093') }}">
                    </x-field>
                </div>
                <x-field label="Alamat">
                    <textarea name="importir[alamat_identitas]" class="w-full border rounded px-3 py-2"
                        required>{{ old('importir.alamat_identitas', $e['importir']['alamat_identitas'] ?? 'KAWASAN INDUSTRI EJIP PLOT 5J 000/000 SUKARESMI, CIKARANG SELATAN, BEKASI, JAWA BARAT') }}</textarea>
                    </x-field>
                
                <x-field label="Status">
                    <select name="importir[kode_status]" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Status --</option>
                        @foreach ($statusOpts as $k => $v)
                            <option value="{{ $k }}" @if ((string) old('importir.kode_status', $e['importir']['kode_status'] ?? 'MITA') === (string) $k) selected @endif>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </x-field>
            </div>
        </div>

        {{-- NPWP PEMUSATAN --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">NPWP Pemusatan</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <input type="hidden" name="kode_entitas2" value="11">
                    <input type="hidden" name="kode_jenis_identitas2" value="6">
                    <x-field label="NPWP">
                        <input name="pemusatan[nomor_identitas2]" class="w-full border rounded px-3 py-2"
                            value="{{ old('pemusatan.nomor_identitas2', $e['pemusatan']['nomor_identitas2'] ?? '001065305305500') }}">
                    </x-field>
                    <x-field label="NITKU">
                        <input name="pemusatan[nitku]" class="w-full border rounded px-3 py-2"
                            value="{{ old('pemusatan.nitku', $e['pemusatan']['nitku'] ?? '001065305305500000000') }}">
                    </x-field>
                </div>
                <x-field label="Nama">
                    <input name="pemusatan[nama_identitas2]" class="w-full border rounded px-3 py-2"
                        value="{{ old('pemusatan.nama_identitas2', $e['pemusatan']['nama_identitas2'] ?? 'AISIN INDONESIA') }}">
                </x-field>
                <x-field label="Alamat">
                    <textarea name="pemusatan[alamat_identitas2]" class="w-full border rounded px-3 py-2"
                        required>{{ old('pemusatan.alamat_identitas2', $e['pemusatan']['alamat_identitas2'] ?? 'KAWASAN INDUSTRI EJIP PLOT 5J 000/000 SUKARESMI, CIKARANG SELATAN, BEKASI, JAWA BARAT') }}</textarea>
                </x-field>
            </div>
        </div>
    </div>

    {{-- ====== KOLOM KANAN ====== --}}
    <div class="space-y-4">
        {{-- PEMILIK BARANG --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Pemilik Barang</h3>
            <div class="grid grid-cols-1 gap-3">
                <div class="grid md:grid-cols-2 gap-3">
                    <input type="hidden" name="kode_entitas3" value="7">
                    <input type="hidden" name="kode_jenis_identitas3" value="6">
                    <x-field label="NPWP">
                        <input name="pemilik[nomor_identitas3]" class="w-full border rounded px-3 py-2"
                            value="{{ old('pemilik.nomor_identitas3', $e['pemilik']['nomor_identitas3'] ?? '001065305305500') }}">
                    </x-field>
                    <x-field label="NITKU">
                        <input name="pemilik[nitku]" class="w-full border rounded px-3 py-2"
                            value="{{ old('pemilik.nitku', $e['pemilik']['nitku'] ?? '001065305305500000000') }}">
                    </x-field>
                </div>
                <x-field label="Nama">
                    <input name="pemilik[nama_identitas3]" class="w-full border rounded px-3 py-2"
                        value="{{ old('pemilik.nama_identitas3', $e['pemilik']['nama_identitas3'] ?? 'AISIN INDONESIA') }}">
                </x-field>
                <x-field label="Alamat">
                    <textarea name="pemilik[alamat_identitas3]" class="w-full border rounded px-3 py-2"
                        required>{{ old('pemilik.alamat_identitas3', $e['pemilik']['alamat_identitas3'] ?? 'KAWASAN INDUSTRI EJIP PLOT 5J 000/000 SUKARESMI, CIKARANG SELATAN, BEKASI, JAWA BARAT') }}</textarea>
                </x-field>
            </div>
        </div>

        {{-- PENGIRIM BARANG --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Pengirim Barang</h3>
            <div class="grid grid-cols-1 gap-3">

                {{-- Select Pengirim --}}
                <x-field label="Nama (Master)">
                    <input type="hidden" name="kode_entitas4" value="9">
                    <select id="pengirim_party_id" name="pengirim[nama_identitas4]" class="w-full border rounded px-3 py-2"
                        required>
                        <option value="">-- Pilih Pengirim --</option>
                        @foreach ($pengirimParties as $party)
                            <option value="{{ $party->id }}" @if (($entitas_pengirim[0]['nama_identitas'] ?? null) == $party->id) selected @endif>
                                {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                </x-field>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Alamat">
                        <textarea name="pengirim[alamat_identitas4]" id="pengirim_alamat" class="w-full border rounded px-3 py-2"
                            required>{{ old('pengirim.alamat_identitas4', $e['pengirim']['alamat_identitas4'] ?? ($ent['alamat_identitas4'] ?? '')) }}</textarea>
                    </x-field>
                    <x-field label="Negara">
                        <select name="pengirim[kode_negara]" id="pengirim_negara" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih Negara --</option>
                            @foreach ($negaraOpts as $k => $v)
                                <option value="{{ $k }}" @selected(old('pengirim.kode_negara', $e['pengirim']['kode_negara'] ?? ($ent['kode_negara'] ?? '')) === $k)>{{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
            </div>
        </div>

        {{-- PENJUAL BARANG --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">
            <h3 class="font-semibold">Penjual Barang</h3>
            <div class="grid grid-cols-1 gap-3">

                {{-- Select Penjual --}}
                <x-field label="Nama (Master)">
                    <input type="hidden" name="kode_entitas5" value="10">
                    <select id="penjual_party_id" name="penjual[nama_identitas5]" class="w-full border rounded px-3 py-2"
                        required>
                        <option value="">-- Pilih Penjual --</option>
                        @foreach ($penjualParties as $party)
                            <option value="{{ $party->id }}" @if (($entitas_penjual[0]['nama_identitas'] ?? null) == $party->id) selected @endif>
                                {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                </x-field>

                <div class="grid md:grid-cols-2 gap-3">
                    <x-field label="Alamat">
                        <textarea name="penjual[alamat_identitas5]" id="penjual_alamat" class="w-full border rounded px-3 py-2"
                            required>{{ old('penjual.alamat_identitas5', $e['penjual']['alamat_identitas5'] ?? ($ent['penjual_alamat'] ?? '')) }}</textarea>
                    </x-field>
                    <x-field label="Negara">
                        <select name="penjual[kode_negara2]" id="penjual_negara" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih Negara --</option>
                            @foreach ($negaraOpts as $k => $v)
                                <option value="{{ $k }}" @selected(old('penjual.kode_negara2', $e['penjual']['kode_negara2'] ?? ($ent['penjual_kode_negara2'] ?? '')) === $k)>{{ $v }}
                                </option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            // Build maps for pengirim and penjual from server-side collections
            const pengirimMap = {
                @foreach ($pengirimParties as $p)
                    "{{ $p->id }}": {
                        alamat: {!! json_encode($p->address ?? '') !!},
                        negara: {!! json_encode($p->country ?? '') !!}
                    },
                @endforeach
            };

            const penjualMap = {
                @foreach ($penjualParties as $p)
                    "{{ $p->id }}": {
                        alamat: {!! json_encode($p->address ?? '') !!},
                        negara: {!! json_encode($p->country ?? '') !!}
                    },
                @endforeach
            };

            function applyPartyToFields(prefix, map, selectedId) {
                const alamatEl = document.querySelector(`#${prefix}_alamat`);
                const negaraEl = document.querySelector(`#${prefix}_negara`);
                if (!alamatEl || !negaraEl) return;
                const data = map[selectedId];
                if (data) {
                    alamatEl.value = data.alamat || '';
                    negaraEl.value = data.negara || '';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const pengirimSel = document.getElementById('pengirim_party_id');
                const penjualSel = document.getElementById('penjual_party_id');
                if (pengirimSel) {
                    pengirimSel.addEventListener('change', function() {
                        applyPartyToFields('pengirim', pengirimMap, this.value);
                    });
                    // apply initial value
                    applyPartyToFields('pengirim', pengirimMap, pengirimSel.value);
                }
                if (penjualSel) {
                    penjualSel.addEventListener('change', function() {
                        applyPartyToFields('penjual', penjualMap, this.value);
                    });
                    applyPartyToFields('penjual', penjualMap, penjualSel.value);
                }
            });
        })();
    </script>
@endpush
