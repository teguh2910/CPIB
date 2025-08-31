<p align="center"><strong>CPIB</strong> – Aplikasi internal untuk pengelolaan Import Notification, barang, dan ekspor laporan.</p>

## Tech Stack

- PHP 8.4.11
- Laravel 12.26.4
- SQLite (default)
- Vite + Tailwind CSS 4
- Maatwebsite/Excel 3.1 (ekspor/import Excel)

## Fitur Utama

- Autentikasi sederhana (form login manual).
- CRUD Import Notification beserta relasi: Dokumen, Kemasan, Peti Kemas, Barang, dan Pungutan.
- Upload Barang via CSV/Excel + template bawaan (`public/template.xlsx`).
- Pencarian referensi via AJAX: Party, Pelabuhan, TPS, Pelabuhan Tujuan, Negara, Kurs.
- Ekspor seluruh data atau per Import Notification ke Excel maupun JSON.

## Persyaratan

- PHP 8.2+ (disarankan 8.4.11)
- Composer
- Node.js 18+ dan npm

## Instalasi & Setup

1) Install dependency backend dan frontend.
2) Salin file env, generate APP_KEY.
3) Pastikan database SQLite tersedia, lalu jalankan migrasi.

Contoh pengaturan cepat (SQLite):

- Pastikan `DB_CONNECTION=sqlite` di `.env` dan file `database/database.sqlite` ada.
- Jalankan migrasi untuk membuat tabel yang diperlukan.

## Menjalankan Aplikasi

- Mode all-in-one (server + queue + logs + Vite) gunakan script Composer: `composer run dev`.
- Atau jalankan komponen terpisah: `php artisan serve`, `php artisan queue:listen`, dan `npm run dev`.

Jika perubahan frontend tidak muncul, jalankan ulang Vite (`npm run dev`) atau lakukan `npm run build`.

## Autentikasi

- Halaman login: `GET /login`
- Aplikasi menggunakan autentikasi sesi sederhana. Buat user terlebih dahulu (mis. via seeder atau tinker) agar dapat masuk.

## Rute Penting (ringkas)

- GET `/` -> redirect ke login.
- Import Notification (butuh login): resource `import.*`
- Barang: `barang.index|create|store|edit|update|destroy`, upload: `POST /barang/upload`, template: `GET /barang/template`.
- AJAX: `/ajax/party/search`, `/ajax/party/{id}`, `/ajax/pelabuhan/search`, `/ajax/tps/search`, `/ajax/pelabuhan-tujuan/search`, `/ajax/negara/search`, kurs: `/ajax/kurs`.
- Ekspor: `GET /export/all` (semua tabel -> multi-sheet), `GET /export/{id}` (per notification), `GET /export/{id}/json` (JSON terstruktur).

Nama rute tersedia di `routes/web.php` (gunakan `php artisan route:list` bila perlu).

## Data Referensi

Sebagian daftar referensi (kode kantor, negara, dsb.) didefinisikan di `config/import.php`. Ubah file tersebut bila perlu memperbarui opsi dropdown/pencarian.

## Pengembangan

- Formatter: Jalankan Pint untuk memformat kode PHP: `vendor/bin/pint --dirty`.
- Test: `php artisan test`.

## Catatan

- Template unggah barang tersedia di `public/template.xlsx`.
- Ekspor Excel menggunakan Maatwebsite/Excel; setiap tabel diekspor ke sheet terpisah.
- Sebagian rute AJAX dan rute uji (`/test/pelabuhan-tujuan`) disiapkan untuk pengujian awal.
