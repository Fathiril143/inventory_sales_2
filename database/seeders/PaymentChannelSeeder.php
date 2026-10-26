<?php

namespace Database\Seeders;

use App\Models\PaymentChannel;
use Illuminate\Database\Seeder;

class PaymentChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            // QRIS
            [
                'code' => 'qris',
                'name' => 'QRIS Instan Otomatis',
                'category' => 'qris',
                'account_number' => 'NMID: ID1020038920192',
                'account_name' => 'INVENTORY SALES UAS MERCHANT',
                'admin_fee' => 0,
                'icon' => 'qris',
                'color' => '#ea1f27',
                'is_active' => true,
                'sort_order' => 1,
                'instructions' => [
                    'qris' => [
                        'Buka aplikasi mobile banking (BCA Mobile, Livin by Mandiri, BRImo, BNI Mobile) atau e-wallet (GoPay, OVO, DANA, ShopeePay).',
                        'Pilih menu Scan / Bayar QRIS pada aplikasi.',
                        'Arahkan kamera ke kode QR yang tampil di layar.',
                        'Pastikan nama merchant "INVENTORY SALES UAS" dan nominal pembayaran sesuai.',
                        'Konfirmasi dan masukkan PIN transaksi Anda. Pembayaran akan terverifikasi otomatis dalam beberapa detik.',
                    ],
                ],
            ],

            // Virtual Accounts
            [
                'code' => 'bca_va',
                'name' => 'BCA Virtual Account',
                'category' => 'va',
                'account_number' => '8277',
                'account_name' => 'INVENTORY SALES BCA VA',
                'admin_fee' => 0,
                'icon' => 'bca',
                'color' => '#005baa',
                'is_active' => true,
                'sort_order' => 2,
                'instructions' => [
                    'mbanking' => [
                        'Buka aplikasi BCA Mobile / myBCA dan pilih m-Transfer.',
                        'Pilih menu BCA Virtual Account.',
                        'Masukkan Nomor Virtual Account yang tertera di layar pembayaran.',
                        'Pastikan nama dan nominal pembayaran telah sesuai, lalu masukkan PIN m-BCA.',
                        'Simpan bukti transaksi. Sistem akan memverifikasi pembayaran Anda secara instan.',
                    ],
                    'atm' => [
                        'Masukkan Kartu ATM BCA dan PIN Anda di mesin ATM.',
                        'Pilih menu Transaksi Lainnya > Transfer > Ke Rek BCA Virtual Account.',
                        'Masukkan Nomor Virtual Account lalu tekan Benar.',
                        'Periksa detail pembayaran di layar lalu selesaikan transaksi.',
                    ],
                    'ibanking' => [
                        'Login ke KlikBCA Individual.',
                        'Pilih menu Transfer Dana > Transfer ke BCA Virtual Account.',
                        'Masukkan Nomor Virtual Account dan ikuti instruksi KeyBCA untuk konfirmasi.',
                    ],
                ],
            ],
            [
                'code' => 'mandiri_va',
                'name' => 'Mandiri Virtual Account',
                'category' => 'va',
                'account_number' => '8890',
                'account_name' => 'INVENTORY SALES MANDIRI VA',
                'admin_fee' => 0,
                'icon' => 'mandiri',
                'color' => '#003d79',
                'is_active' => true,
                'sort_order' => 3,
                'instructions' => [
                    'mbanking' => [
                        'Buka aplikasi Livin\' by Mandiri dan lakukan Login.',
                        'Pilih menu Bayar > Buat Pembayaran Baru > Multipayment.',
                        'Pilih penyedia jasa atau masukkan kode perusahaan 8890 (Inventory Sales).',
                        'Masukkan Nomor Mandiri Virtual Account Anda.',
                        'Periksa jumlah tagihan dan konfirmasi dengan PIN Livin\'.',
                    ],
                    'atm' => [
                        'Masukkan kartu ATM Mandiri dan PIN.',
                        'Pilih menu Bayar/Beli > Multipayment.',
                        'Masukkan kode perusahaan dan nomor Virtual Account.',
                        'Konfirmasi transaksi dan simpan struk pembayaran.',
                    ],
                ],
            ],
            [
                'code' => 'bni_va',
                'name' => 'BNI Virtual Account',
                'category' => 'va',
                'account_number' => '9881',
                'account_name' => 'INVENTORY SALES BNI VA',
                'admin_fee' => 0,
                'icon' => 'bni',
                'color' => '#f15a24',
                'is_active' => true,
                'sort_order' => 4,
                'instructions' => [
                    'mbanking' => [
                        'Login ke BNI Mobile Banking.',
                        'Pilih menu Pembayaran > Virtual Account Billing.',
                        'Masukkan Nomor BNI Virtual Account lalu tekan Lanjut.',
                        'Periksa tagihan dan masukkan Password Transaksi Anda.',
                    ],
                    'atm' => [
                        'Masukkan kartu ATM BNI dan PIN.',
                        'Pilih Menu Lainnya > Transfer > Rekening Tabungan > Ke Rekening BNI.',
                        'Masukkan 16 digit Nomor BNI Virtual Account lalu konfirmasi.',
                    ],
                ],
            ],
            [
                'code' => 'bri_va',
                'name' => 'BRI Virtual Account (BRIVA)',
                'category' => 'va',
                'account_number' => '1280',
                'account_name' => 'INVENTORY SALES BRIVA',
                'admin_fee' => 0,
                'icon' => 'bri',
                'color' => '#00529c',
                'is_active' => true,
                'sort_order' => 5,
                'instructions' => [
                    'mbanking' => [
                        'Buka aplikasi BRImo dan pilih menu BRIVA.',
                        'Pilih Pembayaran Baru dan masukkan Nomor Virtual Account BRIVA.',
                        'Periksa data transaksi dan konfirmasi dengan PIN BRImo.',
                    ],
                    'atm' => [
                        'Masukkan kartu ATM BRI dan PIN.',
                        'Pilih menu Transaksi Lain > Pembayaran > Lainnya > BRIVA.',
                        'Masukkan Nomor BRIVA lalu konfirmasi pembayaran.',
                    ],
                ],
            ],
            [
                'code' => 'bsi_va',
                'name' => 'BSI Virtual Account',
                'category' => 'va',
                'account_number' => '7110',
                'account_name' => 'INVENTORY SALES BSI VA',
                'admin_fee' => 0,
                'icon' => 'bsi',
                'color' => '#00a39d',
                'is_active' => true,
                'sort_order' => 6,
                'instructions' => [
                    'mbanking' => [
                        'Login ke aplikasi BSI Mobile.',
                        'Pilih menu Bayar > Institusi / Virtual Account.',
                        'Masukkan Nomor BSI Virtual Account dan selesaikan transaksi.',
                    ],
                ],
            ],

            // E-Wallets
            [
                'code' => 'gopay',
                'name' => 'GoPay',
                'category' => 'ewallet',
                'account_number' => '0812-9988-7711',
                'account_name' => 'INVENTORY SALES GOPAY',
                'admin_fee' => 0,
                'icon' => 'gopay',
                'color' => '#00aed6',
                'is_active' => true,
                'sort_order' => 7,
                'instructions' => [
                    'app' => [
                        'Buka aplikasi GoPay atau Gojek di HP Anda.',
                        'Pilih menu Bayar / Scan kode QR atau gunakan fitur Transfer.',
                        'Pastikan saldo GoPay Anda mencukupi total pesanan.',
                        'Konfirmasi pembayaran dan masukkan PIN GoPay.',
                    ],
                ],
            ],
            [
                'code' => 'dana',
                'name' => 'DANA',
                'category' => 'ewallet',
                'account_number' => '0812-9988-7722',
                'account_name' => 'INVENTORY SALES DANA',
                'admin_fee' => 0,
                'icon' => 'dana',
                'color' => '#118eea',
                'is_active' => true,
                'sort_order' => 8,
                'instructions' => [
                    'app' => [
                        'Buka aplikasi DANA di smartphone Anda.',
                        'Pilih menu Kirim atau Scan QR.',
                        'Pastikan nominal transfer sesuai dengan total tagihan.',
                        'Masukkan PIN DANA Anda untuk memproses transaksi.',
                    ],
                ],
            ],
            [
                'code' => 'ovo',
                'name' => 'OVO',
                'category' => 'ewallet',
                'account_number' => '0812-9988-7733',
                'account_name' => 'INVENTORY SALES OVO',
                'admin_fee' => 0,
                'icon' => 'ovo',
                'color' => '#4c3494',
                'is_active' => true,
                'sort_order' => 9,
                'instructions' => [
                    'app' => [
                        'Buka aplikasi OVO.',
                        'Pilih menu Transfer atau Scan QRIS.',
                        'Periksa nama penerima dan nominal transaksi.',
                        'Selesaikan pembayaran dengan memasukkan Security Code OVO.',
                    ],
                ],
            ],

            // Transfer Bank Manual
            [
                'code' => 'manual_bca',
                'name' => 'Transfer Bank BCA (Manual)',
                'category' => 'manual_transfer',
                'account_number' => '5410-8899-22',
                'account_name' => 'Fathiril (Rekening Kas Toko)',
                'admin_fee' => 0,
                'icon' => 'bank',
                'color' => '#005baa',
                'is_active' => true,
                'sort_order' => 10,
                'instructions' => [
                    'transfer' => [
                        'Transfer tepat sesuai total nominal yang tercantum (termasuk kode unik jika ada).',
                        'Rekening Tujuan: BCA 5410-8899-22 a.n. Fathiril (Rekening Kas Toko).',
                        'Setelah melakukan transfer, simpan struk atau tangkapan layar (screenshot) bukti transfer.',
                        'Unggah foto bukti transfer pada form yang disediakan di halaman pembayaran.',
                        'Staff toko akan memverifikasi bukti Anda maksimal 1x24 jam kerja.',
                    ],
                ],
            ],
            [
                'code' => 'manual_mandiri',
                'name' => 'Transfer Bank Mandiri (Manual)',
                'category' => 'manual_transfer',
                'account_number' => '137-00-1928374-1',
                'account_name' => 'Fathiril (Rekening Kas Toko)',
                'admin_fee' => 0,
                'icon' => 'bank',
                'color' => '#003d79',
                'is_active' => true,
                'sort_order' => 11,
                'instructions' => [
                    'transfer' => [
                        'Transfer tepat sesuai total tagihan ke Rekening Mandiri 137-00-1928374-1 a.n. Fathiril.',
                        'Simpan bukti transfer m-banking atau struk ATM.',
                        'Unggah file bukti bayar melalui halaman ini agar diverifikasi oleh Staff kasir/gudang.',
                    ],
                ],
            ],

            // Tunai / Kasir POS
            [
                'code' => 'cash',
                'name' => 'Tunai / Kasir POS (Langsung di Toko)',
                'category' => 'cash',
                'account_number' => 'KASIR-MEJA-1',
                'account_name' => 'KAS TOKO UTAMA',
                'admin_fee' => 0,
                'icon' => 'cash',
                'color' => '#16a34a',
                'is_active' => true,
                'sort_order' => 12,
                'instructions' => [
                    'cash' => [
                        'Lakukan pembayaran langsung secara tunai kepada kasir toko.',
                        'Kasir akan menghitung uang diterima dan mencetak struk transaksi resmi lunas.',
                    ],
                ],
            ],
        ];

        foreach ($channels as $channel) {
            PaymentChannel::updateOrCreate(
                ['code' => $channel['code']],
                $channel
            );
        }
    }
}
