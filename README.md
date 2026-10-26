# Inventory & Sales Management System

Aplikasi web Laravel untuk mengelola data produk, pemasok, pembelian stok, dan penjualan. Proyek ini dibuat untuk UTS Web Programming 2.

## Kebutuhan

- PHP 8.2 atau lebih baru
- Composer
- MySQL 8 atau MariaDB
- Node.js dan npm

## Instalasi

1. Clone repository, lalu masuk ke folder proyek.
2. Pasang dependensi PHP:

   ```powershell
   composer install
   ```

3. Salin konfigurasi contoh menjadi konfigurasi lokal:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Buat database MySQL bernama `inventory_sales`. Sesuaikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `.env` dengan pengaturan MySQL lokal.
5. Buat kunci aplikasi dan bangun tabel serta akun demo:

   ```powershell
   php artisan key:generate
   php artisan migrate --seed
   ```

6. Pasang dan bangun aset frontend:

   ```powershell
   npm install
   npm run build
   ```

7. Jalankan aplikasi:

   ```powershell
   php artisan serve
   ```

8. Buka `http://127.0.0.1:8000` di browser.

## Akun demo

| Role | Email | Kata sandi |
| --- | --- | --- |
| Admin / Owner | `admin@inventory.test` | `admin12345` |
| Staff | `staff@inventory.test` | `staff12345` |

Akun tersebut hanya untuk demonstrasi lokal. Seeder membuat atau memperbarui akun demo saat `php artisan db:seed` dijalankan.

## Fitur

- Login dan logout dengan autentikasi berbasis sesi.
- Role Admin dan Staff yang dibatasi di sisi server.
- CRUD kategori, produk, pemasok, dan pengguna untuk Admin.
- Pencarian produk berdasarkan SKU atau nama, filter kategori, dan pagination.
- Transaksi pembelian dengan beberapa item; stok bertambah ketika transaksi berhasil.
- Transaksi penjualan dengan beberapa item; stok diperiksa dan dikurangi ketika transaksi berhasil.
- Total dan subtotal dihitung di server; harga jual diambil dari data produk.
- Pembatalan transaksi oleh Admin dengan penyesuaian stok yang konsisten.
- Dashboard berisi ringkasan produk, transaksi, penjualan, dan stok rendah.
- Filter tanggal dan status pada daftar transaksi.
- Detail pembelian dan penjualan.
- Katalog produk publik yang dapat dibuka pelanggan tanpa login.
- Pesanan pelanggan tanpa akun dengan validasi stok dan pencatatan kontak.

## Katalog pelanggan

Pelanggan dapat membuka halaman awal untuk mencari produk yang stoknya tersedia, memilih beberapa produk, lalu membuat pesanan tanpa akun. Nama dan nomor kontak dicatat pada transaksi, stok diperiksa dan dikurangi secara atomik, dan nomor pesanan ditampilkan setelah berhasil. Pembayaran online tidak termasuk.

## Aturan data dan transaksi

- SKU produk harus unik.
- Kuantitas transaksi harus lebih dari nol dan harga tidak boleh negatif.
- Penjualan ditolak jika stok tidak cukup.
- Header transaksi, item, dan perubahan stok disimpan dalam satu database transaction.
- Pembatalan penjualan mengembalikan stok.
- Pembatalan pembelian mengurangi stok yang masuk; pembatalan ditolak jika stok tersebut sudah terpakai dan hasilnya akan membuat stok negatif.
- Kategori, pemasok, produk, atau pengguna yang sudah dipakai dalam transaksi dilindungi dari penghapusan yang merusak riwayat.

## Struktur data

ERD dan penjelasan relasi ada di [`docs/erd.md`](docs/erd.md). Tabel utama: `users`, `categories`, `products`, `suppliers`, `purchases`, `purchase_items`, `sales`, dan `sale_items`.

## Keamanan dan konfigurasi

- Jangan commit `.env`, kata sandi asli, atau kunci API.
- Commit `.env.example` sebagai contoh konfigurasi tanpa rahasia.
- Laravel melakukan hashing kata sandi melalui model User.
- Formulir memakai CSRF protection dan validasi server-side.
- Periksa role pada middleware sebelum mengakses halaman Admin.

## Batasan

Proyek ini berfokus pada persediaan dan penjualan dasar. Payment gateway, multi-warehouse, akuntansi penuh, aplikasi mobile terpisah, forecasting AI, dan integrasi ERP tidak termasuk ruang lingkup.
