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

## Akun demo & Akses Peran

| Role | Kredensial / Akses | Keterangan |
| --- | --- | --- |
| Admin / Owner | `admin@inventory.test` \| `admin12345` | Kelola produk, kategori, pemasok, transaksi, dan akun pengguna. |
| Staff | `staff@inventory.test` \| `staff12345` | Kelola produk dan transaksi pembelian serta penjualan. |
| Customer / Pelanggan | **Tanpa Login** (Buka `/` atau klik tombol di halaman `/login`) | Melihat katalog produk dan memesan barang langsung tanpa perlu login. |
| Customer (Akun Demo) | `customer@inventory.test` \| `customer12345` | Akun demo opsional jika ingin sesi akun pelanggan. |

Akun tersebut hanya untuk demonstrasi lokal. Seeder membuat atau memperbarui akun demo saat `php artisan db:seed` dijalankan. Pengunjung toko (Customer) dapat langsung berbelanja tanpa perlu login melalui katalog utama (`/`) atau tombol **Masuk sebagai Customer (Tanpa Login)** pada halaman login.

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
- **Mock Payment Gateway (100% Localhost):** Antarmuka modern ala Midtrans Snap & Xendit untuk Virtual Account (BCA, Mandiri, BNI, BRI, BSI), QRIS Dinamis, E-Wallet (GoPay, DANA, OVO), dan Transfer Bank Manual.
- **Panel Simulasi Demo UAS:** Tombol callback instan pembayaran lunas (Instant Paid) & simulasi waktu habis (Expired) dengan pengembalian stok otomatis.
- **Struk / Invoice Resmi (Print-friendly / PDF):** Cetak invoice dengan cap watermark LUNAS/PAID.
- **Verifikasi Bukti Transfer Staff:** Upload struk/tangkapan layar transfer dan approval/reject manual oleh Staff kasir/gudang.
- **Alur Gudang & Fulfillment:** Status pemrosesan dan pengiriman barang hanya dapat dijalankan jika status pembayaran telah LUNAS.
- **Kasir / POS Langsung (Staff):** Pilihan pembayaran Tunai (dengan kalkulator kembalian otomatis) dan QRIS langsung di meja kasir.
- **Dashboard Keuangan & Laporan Transaksi (Admin):** Statistik omset lunas, pending, grafik breakdown metode bayar, dan laporan keuangan cetak berformat resmi dengan tanda tangan pengesahan UAS.

## Panduan Demo Presentasi UAS (Payment Gateway)

1. Buka katalog customer di `http://127.0.0.1:8000` atau klik **"Buat Pesanan"** (`/pesan`).
2. Masukkan data pesanan, pilih produk, lalu pilih salah satu metode pembayaran:
   - **QRIS Instan**: Membuka layar QR Code dinamis dengan logo QRIS dan batas waktu pembayaran.
   - **Virtual Account (BCA/Mandiri/BNI/BRI/BSI)**: Menampilkan nomor VA unik yang dapat disalin beserta petunjuk M-Banking, ATM, dan Internet Banking.
   - **Transfer Bank Manual**: Menampilkan rekening toko dan form upload bukti transfer (JPG/PNG/PDF).
3. **Di Halaman Payment Gateway (`/payment/{INV-XXXX}`):**
   - Tunjukkan Countdown Timer 24 jam yang berjalan realtime.
   - Klik tombol **⚡ Simulasi Bayar Berhasil (Instant Callback / Paid)** pada panel demo UAS:
     - Sistem langsung memicu callback internal tanpa API pihak ketiga.
     - Status berubah menjadi **LUNAS (Paid)** secara realtime.
     - Muncul modal animasi sukses dan tombol cetak invoice resmi.
   - Klik **🖨️ Cetak / Unduh Struk Lunas** untuk memperlihatkan invoice ber-watermark *"LUNAS / PAID"*.
4. **Demonstrasi Kasus Timeout / Expired:**
   - Buat pesanan baru, lalu klik **⏳ Simulasi Waktu Habis (Expired)**.
   - Status tagihan berubah menjadi Kedaluwarsa dan stok barang yang tertahan otomatis dikembalikan ke inventaris.
5. **Demonstrasi Verifikasi Manual Staff & Pengiriman Gudang:**
   - Pilih metode Transfer Manual & unggah foto bukti transfer.
   - Login sebagai Staff (`staff@inventory.test` | `staff12345`), buka menu **Kelola Penjualan** (`/sales`).
   - Buka detail pesanan, tinjau foto bukti pembayaran, lalu klik **✓ Terima & Setujui Pembayaran**.
   - Perhatikan tombol alur gudang: Staff sekarang dapat menekan tombol **Mulai Kemas Barang (Processing)** dan **Kirim Barang (Shipped)** karena pembayaran sudah lunas.
6. **Demonstrasi Dashboard Keuangan & Laporan Admin:**
   - Login sebagai Admin (`admin@inventory.test` | `admin12345`).
   - Buka **Dashboard** untuk melihat total pendapatan lunas, pending, dan rincian transaksi per metode (QRIS, VA, E-Wallet, Transfer Manual, Kasir Tunai).
   - Buka **Kelola Channel Pembayaran** (`/admin/payment-channels`) untuk mendemokan fitur on/off channel dan perubahan nomor rekening.
   - Buka **Laporan Keuangan Transaksi** (`/admin/payment-reports`) dan klik tombol **Cetak / Unduh Laporan (PDF)** untuk memperlihatkan lembar pengesahan UAS bertanda tangan dosen penguji.

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
