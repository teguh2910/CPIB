<div class="space-y-4" x-data="containerForm()">
    <div class="grid md:grid-cols-4 gap-4">
        <x-field label="Jenis Kemasan">
            <input name="jenis" class="w-full border rounded px-3 py-2" value="{{ old('jenis', $draft['jenis'] ?? '') }}"
                required>
        </x-field>
        <x-field label="Jumlah">
            <input type="number" min="0" name="jumlah" class="w-full border rounded px-3 py-2"
                value="{{ old('jumlah', $draft['jumlah'] ?? '') }}" required>
        </x-field>
        <x-field label="Total Bruto (kg)">
            <input type="number" min="0" step="0.001" name="total_bruto"
                class="w-full border rounded px-3 py-2" value="{{ old('total_bruto', $draft['total_bruto'] ?? '') }}">
        </x-field>
        <x-field label="Total Netto (kg)">
            <input type="number" min="0" step="0.001" name="total_netto"
                class="w-full border rounded px-3 py-2" value="{{ old('total_netto', $draft['total_netto'] ?? '') }}">
        </x-field>
    </div>

    <div class="flex items-center justify-between">
        <h3 class="font-medium">Peti Kemas (Opsional)</h3>
        <button type="button" class="px-3 py-1 border rounded" @click="add()">+ Tambah Container</button>
    </div>

    <template x-for="(c, idx) in containers" :key="idx">
        <div class="grid md:grid-cols-4 gap-3 border p-3 rounded">
            <x-field label="No. Container">
                <input :name="`containers[${idx}][nomor]`" class="w-full border rounded px-2 py-1" x-model="c.nomor">
            </x-field>
            <x-field label="Ukuran">
                <select :name="`containers[${idx}][ukuran]`" class="w-full border rounded px-2 py-1" x-model="c.ukuran">
                    <option>20GP</option>
                    <option>40GP</option>
                    <option>40HC</option>
                    <option>45HC</option>
                    <option>Lainnya</option>
                </select>
            </x-field>
            <x-field label="Seal No">
                <input :name="`containers[${idx}][seal_no]`" class="w-full border rounded px-2 py-1"
                    x-model="c.seal_no">
            </x-field>
            <div class="flex items-end">
                <button type="button" class="text-red-600 text-xs underline" @click="remove(idx)">Hapus</button>
            </div>
        </div>
    </template>

    <textarea id="prefill_containers" class="hidden">@json(old('containers', $draft['containers'] ?? []))</textarea>
</div>

<script>
    function containerForm() {
        return {
            containers: [],
            init() {
                try {
                    const pre = JSON.parse(document.getElementById('prefill_containers').value || '[]');
                    this.containers = Array.isArray(pre) ? pre : [];
                } catch {
                    this.containers = [];
                }
            },
            add() {
                this.containers.push({
                    nomor: '',
                    ukuran: '20GP',
                    seal_no: ''
                });
            },
            remove(i) {
                this.containers.splice(i, 1);
            },
        }
    }
</script>
