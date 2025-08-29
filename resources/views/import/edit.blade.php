@extends('layouts.app')
@section('title', 'Edit Pemberitahuan Impor')

@section('content')
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-semibold">Edit Pemberitahuan Impor #{{ $importNotification->id }}</h1>
            <a href="{{ route('import.index') }}" class="px-3 py-2 border rounded text-sm">Kembali</a>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6">
            <div class="flex flex-wrap border-b">
                <button type="button" class="tab-button active px-4 py-2 border-b-2 border-black font-medium text-sm"
                    data-tab="header">
                    Header
                </button>
                <button type="button"
                    class="tab-button px-4 py-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700"
                    data-tab="entitas">
                    Entitas
                </button>
                <button type="button"
                    class="tab-button px-4 py-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700"
                    data-tab="dokumen">
                    Dokumen
                </button>
                <button type="button"
                    class="tab-button px-4 py-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700"
                    data-tab="pengangkut">
                    Pengangkut
                </button>
                <button type="button"
                    class="tab-button px-4 py-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700"
                    data-tab="kemasan">
                    Kemasan
                </button>
                <button type="button"
                    class="tab-button px-4 py-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700"
                    data-tab="transaksi">
                    Transaksi
                </button>
                <button type="button"
                    class="tab-button px-4 py-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700"
                    data-tab="barang">
                    Barang
                </button>
                <button type="button"
                    class="tab-button px-4 py-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700"
                    data-tab="pungutan">
                    Pungutan
                </button>
                <button type="button"
                    class="tab-button px-4 py-2 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700"
                    data-tab="pernyataan">
                    Pernyataan
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('import.update', $importNotification) }}" class="space-y-6" id="importForm">
            @csrf
            @method('PUT')

            <!-- Header Tab Content -->
            <div class="tab-content" id="header-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Header</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nomor Aju</label>
                            <input type="text" name="header[nomor_aju]"
                                value="{{ old('header.nomor_aju', $importNotification->header['nomor_aju'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nomor Dokumen</label>
                            <input type="text" name="header[nomor_dokumen]"
                                value="{{ old('header.nomor_dokumen', $importNotification->header['nomor_dokumen'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tanggal</label>
                            <input type="date" name="header[tanggal]"
                                value="{{ old('header.tanggal', $importNotification->header['tanggal'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Kantor Pabean</label>
                            <select name="header[kantor_pabean]" class="w-full border rounded px-3 py-2" required>
                                <option value="">Pilih Kantor Pabean</option>
                                @foreach (config('import.kantor_pabean') as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('header.kantor_pabean', $importNotification->header['kantor_pabean'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="button" class="next-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-next="entitas">Selanjutnya</button>
                    </div>
                </div>
            </div>

            <!-- Entitas Tab Content -->
            <div class="tab-content hidden" id="entitas-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Entitas</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Importir</label>
                            <input type="text" name="entitas[importir_nama]"
                                value="{{ old('entitas.importir_nama', $importNotification->entitas['importir_nama'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">NPWP Importir</label>
                            <input type="text" name="entitas[importir_npwp]"
                                value="{{ old('entitas.importir_npwp', $importNotification->entitas['importir_npwp'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Alamat Importir</label>
                            <textarea name="entitas[importir_alamat]" class="w-full border rounded px-3 py-2" rows="3" required>{{ old('entitas.importir_alamat', $importNotification->entitas['importir_alamat'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" class="prev-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-prev="header">Sebelumnya</button>
                        <button type="button" class="next-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-next="dokumen">Selanjutnya</button>
                    </div>
                </div>
            </div>

            <!-- Dokumen Tab Content -->
            <div class="tab-content hidden" id="dokumen-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Dokumen</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Jenis Dokumen</label>
                            <select name="dokumen[jenis_dokumen]" class="w-full border rounded px-3 py-2" required>
                                <option value="">Pilih Jenis Dokumen</option>
                                @foreach (config('import.jenis_dokumen') as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('dokumen.jenis_dokumen', $importNotification->dokumen['jenis_dokumen'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nomor Dokumen</label>
                            <input type="text" name="dokumen[nomor_dokumen]"
                                value="{{ old('dokumen.nomor_dokumen', $importNotification->dokumen['nomor_dokumen'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" class="prev-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-prev="entitas">Sebelumnya</button>
                        <button type="button" class="next-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-next="pengangkut">Selanjutnya</button>
                    </div>
                </div>
            </div>

            <!-- Pengangkut Tab Content -->
            <div class="tab-content hidden" id="pengangkut-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Pengangkut</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Pengangkut</label>
                            <input type="text" name="pengangkut[nama]"
                                value="{{ old('pengangkut.nama', $importNotification->pengangkut['nama'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nomor Pengangkut</label>
                            <input type="text" name="pengangkut[nomor]"
                                value="{{ old('pengangkut.nomor', $importNotification->pengangkut['nomor'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" class="prev-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-prev="dokumen">Sebelumnya</button>
                        <button type="button" class="next-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-next="kemasan">Selanjutnya</button>
                    </div>
                </div>
            </div>

            <!-- Kemasan Tab Content -->
            <div class="tab-content hidden" id="kemasan-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Kemasan</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Jenis Kemasan</label>
                            <input type="text" name="kemasan[jenis]"
                                value="{{ old('kemasan.jenis', $importNotification->kemasan['jenis'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Jumlah</label>
                            <input type="number" name="kemasan[jumlah]"
                                value="{{ old('kemasan.jumlah', $importNotification->kemasan['jumlah'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" class="prev-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-prev="pengangkut">Sebelumnya</button>
                        <button type="button" class="next-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-next="transaksi">Selanjutnya</button>
                    </div>
                </div>
            </div>

            <!-- Transaksi Tab Content -->
            <div class="tab-content hidden" id="transaksi-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Transaksi</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Jenis Transaksi</label>
                            <input type="text" name="transaksi[jenis]"
                                value="{{ old('transaksi.jenis', $importNotification->transaksi['jenis'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nilai</label>
                            <input type="number" step="0.01" name="transaksi[nilai]"
                                value="{{ old('transaksi.nilai', $importNotification->transaksi['nilai'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" class="prev-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-prev="kemasan">Sebelumnya</button>
                        <button type="button" class="next-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-next="barang">Selanjutnya</button>
                    </div>
                </div>
            </div>

            <!-- Barang Tab Content -->
            <div class="tab-content hidden" id="barang-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Barang</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Barang</label>
                            <input type="text" name="barang[nama]"
                                value="{{ old('barang.nama', $importNotification->barang['nama'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">HS Code</label>
                            <input type="text" name="barang[hs_code]"
                                value="{{ old('barang.hs_code', $importNotification->barang['hs_code'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Jumlah</label>
                            <input type="number" name="barang[jumlah]"
                                value="{{ old('barang.jumlah', $importNotification->barang['jumlah'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Satuan</label>
                            <input type="text" name="barang[satuan]"
                                value="{{ old('barang.satuan', $importNotification->barang['satuan'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" class="prev-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-prev="transaksi">Sebelumnya</button>
                        <button type="button" class="next-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-next="pungutan">Selanjutnya</button>
                    </div>
                </div>
            </div>

            <!-- Pungutan Tab Content -->
            <div class="tab-content hidden" id="pungutan-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Pungutan</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">BM (Bea Masuk)</label>
                            <input type="number" step="0.01" name="pungutan[bm]"
                                value="{{ old('pungutan.bm', $importNotification->pungutan['bm'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">PPN (PPN Impor)</label>
                            <input type="number" step="0.01" name="pungutan[ppn]"
                                value="{{ old('pungutan.ppn', $importNotification->pungutan['ppn'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">PPnBM</label>
                            <input type="number" step="0.01" name="pungutan[ppnbm]"
                                value="{{ old('pungutan.ppnbm', $importNotification->pungutan['ppnbm'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">PPH</label>
                            <input type="number" step="0.01" name="pungutan[pph]"
                                value="{{ old('pungutan.pph', $importNotification->pungutan['pph'] ?? '') }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" class="prev-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-prev="barang">Sebelumnya</button>
                        <button type="button" class="next-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-next="pernyataan">Selanjutnya</button>
                    </div>
                </div>
            </div>

            <!-- Pernyataan Tab Content -->
            <div class="tab-content hidden" id="pernyataan-tab">
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Pernyataan</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Pernyataan</label>
                            <textarea name="pernyataan[teks]" class="w-full border rounded px-3 py-2" rows="4" required>{{ old('pernyataan.teks', $importNotification->pernyataan['teks'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" class="w-full border rounded px-3 py-2" required>
                                <option value="draft"
                                    {{ old('status', $importNotification->status) == 'draft' ? 'selected' : '' }}>Draft
                                </option>
                                <option value="submitted"
                                    {{ old('status', $importNotification->status) == 'submitted' ? 'selected' : '' }}>
                                    Submitted</option>
                                <option value="approved"
                                    {{ old('status', $importNotification->status) == 'approved' ? 'selected' : '' }}>
                                    Approved</option>
                                <option value="rejected"
                                    {{ old('status', $importNotification->status) == 'rejected' ? 'selected' : '' }}>
                                    Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <button type="button" class="prev-tab px-4 py-2 bg-gray-500 text-white rounded"
                            data-prev="pungutan">Sebelumnya</button>
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            // Tab button click handler
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-black', 'text-black');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });

                    // Add active class to clicked button
                    this.classList.add('active', 'border-black', 'text-black');
                    this.classList.remove('border-transparent', 'text-gray-500');

                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });

                    // Show target tab content
                    document.getElementById(targetTab + '-tab').classList.remove('hidden');
                });
            });

            // Next/Previous button handlers
            document.querySelectorAll('.next-tab').forEach(button => {
                button.addEventListener('click', function() {
                    const nextTab = this.getAttribute('data-next');
                    document.querySelector(`[data-tab="${nextTab}"]`).click();
                });
            });

            document.querySelectorAll('.prev-tab').forEach(button => {
                button.addEventListener('click', function() {
                    const prevTab = this.getAttribute('data-prev');
                    document.querySelector(`[data-tab="${prevTab}"]`).click();
                });
            });
        });
    </script>
@endsection
