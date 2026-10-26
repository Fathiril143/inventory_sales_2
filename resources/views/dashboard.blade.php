<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Inventory Sales</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 1050px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin-bottom: 4px;">Dashboard</h1>
            <p style="margin: 0; color: #64748b;">
                Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Anda login sebagai role: 
                <span style="display: inline-block; padding: 2px 8px; background: #e0f2fe; color: #0284c7; border-radius: 4px; font-weight: 700; text-transform: uppercase; font-size: .8rem;">
                    {{ auth()->user()->role }}
                </span>
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('storefront.index') }}" target="_blank" style="padding: 8px 14px; background: #005baa; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: .88rem;">
                🛒 Buka Katalog Toko Customer
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" style="padding: 8px 14px; background: #fff; color: #b91c1c; border: 1px solid #fecaca; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: .88rem;">
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notifikasi Verifikasi Pembayaran -->
    @if ($waitingVerificationCount > 0)
        <div style="padding: 1rem 1.2rem; background: #fffbeb; border: 2px solid #fde68a; border-radius: 12px; margin-bottom: 1.8rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.4rem;">🔔</span>
                <div>
                    <strong style="color: #92400e;">Ada {{ $waitingVerificationCount }} pembayaran manual transfer yang menunggu verifikasi Staff!</strong>
                    <div style="color: #b45309; font-size: .88rem;">Pelanggan telah mengunggah bukti transfer yang perlu ditinjau.</div>
                </div>
            </div>
            <a href="{{ route('sales.index', ['payment_status' => 'waiting_verification']) }}" style="padding: 6px 14px; background: #d97706; color: #fff; border-radius: 8px; font-weight: 700; font-size: .85rem; text-decoration: none;">
                Tinjau Bukti Bayar →
            </a>
        </div>
    @endif

    <!-- Kartu Statistik Finansial & Operasional -->
    <h2 style="font-size: 1.2rem; color: #1e293b; margin-bottom: .8rem;">Ringkasan Keuangan &amp; Penjualan</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 2rem;">
        <div style="padding: 1.2rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;">
            <div style="font-size: .78rem; font-weight: 700; color: #166534; text-transform: uppercase;">Total Pendapatan Lunas</div>
            <div style="font-size: 1.6rem; font-weight: 900; color: #15803d; margin-top: 4px;">
                Rp {{ number_format((float) $totalRevenuePaid, 0, ',', '.') }}
            </div>
            <div style="font-size: .8rem; color: #166534; margin-top: 4px;">Dana berhasil masuk kas</div>
        </div>

        <div style="padding: 1.2rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px;">
            <div style="font-size: .78rem; font-weight: 700; color: #92400e; text-transform: uppercase;">Menunggu Pembayaran</div>
            <div style="font-size: 1.6rem; font-weight: 900; color: #b45309; margin-top: 4px;">
                Rp {{ number_format((float) $totalPendingAmount, 0, ',', '.') }}
            </div>
            <div style="font-size: .8rem; color: #92400e; margin-top: 4px;">Status Pending &amp; Verifikasi</div>
        </div>

        <div style="padding: 1.2rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px;">
            <div style="font-size: .78rem; font-weight: 700; color: #1e40af; text-transform: uppercase;">Penjualan Hari Ini</div>
            <div style="font-size: 1.6rem; font-weight: 900; color: #1d4ed8; margin-top: 4px;">
                Rp {{ number_format((float) $salesToday, 0, ',', '.') }}
            </div>
            <div style="font-size: .8rem; color: #1e40af; margin-top: 4px;">Bulan ini: Rp {{ number_format((float) $salesThisMonth, 0, ',', '.') }}</div>
        </div>

        <div style="padding: 1.2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
            <div style="font-size: .78rem; font-weight: 700; color: #475569; text-transform: uppercase;">Total Katalog Produk</div>
            <div style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin-top: 4px;">
                {{ $totalProducts }} Produk
            </div>
            <div style="font-size: .8rem; color: #64748b; margin-top: 4px;">{{ $totalTransactions }} total transaksi</div>
        </div>
    </div>

    <!-- Rincian Transaksi per Metode Pembayaran -->
    <h2 style="font-size: 1.2rem; color: #1e293b; margin-bottom: .8rem;">Rincian Transaksi per Metode Pembayaran (Lunas)</h2>
    @if ($paymentMethodStats->isEmpty())
        <p style="color: #64748b; font-size: .9rem;">Belum ada data pembayaran lunas untuk dirinci.</p>
    @else
        <table style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 2rem;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1;">Metode Pembayaran</th>
                    <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1; text-align: center;">Jumlah Transaksi</th>
                    <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1; text-align: right;">Total Nominal Terkumpul</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paymentMethodStats as $stat)
                    <tr>
                        <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;">
                            @php
                                $methodLabels = [
                                    'qris' => '📱 QRIS Instan',
                                    'va' => '🏦 Virtual Account Bank',
                                    'ewallet' => '💳 E-Wallet (GoPay, DANA, OVO)',
                                    'manual_transfer' => '📝 Transfer Bank Manual',
                                    'cash' => '💵 Kasir Tunai (POS)',
                                ];
                            @endphp
                            <strong>{{ $methodLabels[$stat->payment_method] ?? ucfirst($stat->payment_method) }}</strong>
                        </td>
                        <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; text-align: center; font-weight: 600;">
                            {{ $stat->count }}x
                        </td>
                        <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 700; color: #15803d;">
                            Rp {{ number_format((float) $stat->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Produk Stok Rendah -->
    <h2 style="font-size: 1.2rem; color: #1e293b; margin-bottom: .8rem;">Peringatan Stok Rendah</h2>
    @if ($lowStockProducts->isEmpty())
        <p style="color: #166534; font-size: .9rem;">✓ Seluruh stok produk dalam batas aman.</p>
    @else
        <table style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 2rem;">
            <thead><tr style="background: #f8fafc;"><th style="padding: .6rem; border-bottom: 1px solid #cbd5e1;">Produk</th><th style="padding: .6rem; border-bottom: 1px solid #cbd5e1;">Kategori</th><th style="padding: .6rem; border-bottom: 1px solid #cbd5e1; text-align: center;">Sisa Stok</th><th style="padding: .6rem; border-bottom: 1px solid #cbd5e1; text-align: center;">Batas Minimum</th></tr></thead>
            <tbody>
                @foreach ($lowStockProducts as $product)
                    <tr>
                        <td style="padding: .6rem; border-bottom: 1px solid #e2e8f0;"><strong>{{ $product->name }}</strong></td>
                        <td style="padding: .6rem; border-bottom: 1px solid #e2e8f0;">{{ $product->category->name }}</td>
                        <td style="padding: .6rem; border-bottom: 1px solid #e2e8f0; text-align: center; color: #dc2626; font-weight: 700;">{{ $product->stock }}</td>
                        <td style="padding: .6rem; border-bottom: 1px solid #e2e8f0; text-align: center;">{{ $product->minimum_stock }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Menu Navigasi Back-Office -->
    <h2 style="font-size: 1.2rem; color: #1e293b; margin-bottom: .8rem;">Navigasi Sistem</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 2rem;">
        <a href="{{ route('sales.index') }}" style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #1e293b; font-weight: 600;">
            📦 Kelola Penjualan &amp; Pesanan →
        </a>
        <a href="{{ route('purchases.index') }}" style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #1e293b; font-weight: 600;">
            📥 Kelola Pembelian Stok →
        </a>
        <a href="{{ route('products.index') }}" style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #1e293b; font-weight: 600;">
            🏷️ Kelola Produk &amp; Harga →
        </a>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('payment-channels.index') }}" style="padding: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; text-decoration: none; color: #166534; font-weight: 600;">
                💳 Kelola Channel Pembayaran →
            </a>
            <a href="{{ route('payment-reports.index') }}" style="padding: 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; text-decoration: none; color: #1e40af; font-weight: 600;">
                📊 Laporan Keuangan Transaksi →
            </a>
            <a href="{{ route('categories.index') }}" style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #1e293b; font-weight: 600;">
                📂 Kelola Kategori →
            </a>
            <a href="{{ route('suppliers.index') }}" style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #1e293b; font-weight: 600;">
                🏢 Kelola Pemasok →
            </a>
            <a href="{{ route('users.index') }}" style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #1e293b; font-weight: 600;">
                👥 Kelola Pengguna (Users) →
            </a>
        @endif
    </div>
</body>
</html>
