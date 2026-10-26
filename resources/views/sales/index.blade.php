<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pesanan &amp; Penjualan | Inventory Sales</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 1100px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <p><a href="{{ route('dashboard') }}">← Kembali ke Dashboard</a></p>
    <h1>Daftar Pesanan &amp; Penjualan</h1>

    @if (session('success'))
        <p style="padding: .8rem; background: #dcfce7; color: #166534; border-radius: 8px;">{{ session('success') }}</p>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
        <a href="{{ route('sales.create') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #176b78; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 700;">
            + Input Penjualan / Kasir POS
        </a>
        <a href="{{ route('storefront.index') }}" target="_blank" style="color: #176b78; font-weight: 600;">
            Buka Katalog Toko Customer →
        </a>
    </div>

    <!-- Filter Multi-Kriteria -->
    <form method="GET" action="{{ route('sales.index') }}" style="margin: 1.2rem 0; padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; flex-wrap: wrap; gap: .8rem; align-items: end;">
        <label>Dari tanggal<br><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></label>
        <label>Sampai tanggal<br><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></label>
        
        <label>Status Bayar<br>
            <select name="payment_status">
                <option value="">Semua Status</option>
                <option value="paid" @selected(($filters['payment_status'] ?? '') === 'paid')>🟢 Lunas (Paid)</option>
                <option value="waiting_verification" @selected(($filters['payment_status'] ?? '') === 'waiting_verification')>🟡 Menunggu Verifikasi</option>
                <option value="pending" @selected(($filters['payment_status'] ?? '') === 'pending')>🔵 Belum Dibayar (Pending)</option>
                <option value="expired" @selected(($filters['payment_status'] ?? '') === 'expired')>🔴 Kedaluwarsa (Expired)</option>
                <option value="failed" @selected(($filters['payment_status'] ?? '') === 'failed')>❌ Gagal / Ditolak</option>
            </select>
        </label>

        <label>Metode Pembayaran<br>
            <select name="payment_method">
                <option value="">Semua Metode</option>
                <option value="qris" @selected(($filters['payment_method'] ?? '') === 'qris')>QRIS</option>
                <option value="va" @selected(($filters['payment_method'] ?? '') === 'va')>Virtual Account</option>
                <option value="ewallet" @selected(($filters['payment_method'] ?? '') === 'ewallet')>E-Wallet</option>
                <option value="manual_transfer" @selected(($filters['payment_method'] ?? '') === 'manual_transfer')>Transfer Manual</option>
                <option value="cash" @selected(($filters['payment_method'] ?? '') === 'cash')>Kasir Tunai</option>
            </select>
        </label>

        <button type="submit" style="padding: .65rem 1.2rem; background: #176b78; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
            Filter
        </button>
        <a href="{{ route('sales.index') }}" style="padding: .65rem 1rem; color: #64748b; text-decoration: none;">Reset</a>
    </form>
    @error('date_to') <p style="color: #b91c1c;">{{ $message }}</p> @enderror

    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">No. Pesanan</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Tanggal</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Pelanggan / Kasir</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Metode Bayar</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Total</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Status Bayar</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Status Pengiriman</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td style="padding: .75rem; border-bottom: 1px solid #e2e8f0;">
                        <a href="{{ route('sales.show', $sale) }}" style="font-weight: 700; color: #176b78;">
                            {{ $sale->sale_number }}
                        </a>
                        @if ($sale->payment)
                            <div style="font-size: .75rem; color: #64748b;">Ref: {{ $sale->payment->reference_number }}</div>
                        @endif
                    </td>
                    <td style="padding: .75rem; border-bottom: 1px solid #e2e8f0; font-size: .88rem;">
                        {{ $sale->sold_at->format('d/m/Y H:i') }}
                    </td>
                    <td style="padding: .75rem; border-bottom: 1px solid #e2e8f0;">
                        <strong>{{ $sale->customer_name ?: ($sale->user?->name ?? 'Pelanggan Umum') }}</strong>
                        @if ($sale->customer_phone)
                            <div style="font-size: .78rem; color: #64748b;">{{ $sale->customer_phone }}</div>
                        @endif
                    </td>
                    <td style="padding: .75rem; border-bottom: 1px solid #e2e8f0;">
                        <span style="display: inline-block; padding: 3px 8px; background: #f1f5f9; border-radius: 6px; font-size: .82rem; font-weight: 600;">
                            {{ $sale->payment_channel ?: ($sale->payment?->payment_channel ?? 'Belum ditentukan') }}
                        </span>
                    </td>
                    <td style="padding: .75rem; border-bottom: 1px solid #e2e8f0; font-weight: 700;">
                        Rp {{ number_format((float) $sale->total, 0, ',', '.') }}
                    </td>
                    <td style="padding: .75rem; border-bottom: 1px solid #e2e8f0;">
                        @php
                            $pStatus = $sale->payment_status ?: ($sale->payment?->status ?? 'pending');
                        @endphp
                        @if ($pStatus === 'paid')
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #dcfce7; color: #15803d; border-radius: 20px; font-size: .78rem; font-weight: 700;">
                                ✓ LUNAS
                            </span>
                        @elseif ($pStatus === 'waiting_verification')
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fef3c7; color: #b45309; border-radius: 20px; font-size: .78rem; font-weight: 700;">
                                ⏳ Butuh Verifikasi
                            </span>
                        @elseif ($pStatus === 'expired')
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fee2e2; color: #b91c1c; border-radius: 20px; font-size: .78rem; font-weight: 700;">
                                ✕ Kedaluwarsa
                            </span>
                        @elseif ($pStatus === 'failed')
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fee2e2; color: #991b1b; border-radius: 20px; font-size: .78rem; font-weight: 700;">
                                ✕ Ditolak
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #e0f2fe; color: #0369a1; border-radius: 20px; font-size: .78rem; font-weight: 700;">
                                ⏱️ Belum Dibayar
                            </span>
                        @endif
                    </td>
                    <td style="padding: .75rem; border-bottom: 1px solid #e2e8f0; font-size: .85rem;">
                        @php
                            $fStatus = $sale->fulfillment_status ?: 'unfulfilled';
                        @endphp
                        @if ($fStatus === 'delivered')
                            <span style="color: #16a34a; font-weight: 600;">✓ Selesai / Terkirim</span>
                        @elseif ($fStatus === 'shipped')
                            <span style="color: #2563eb; font-weight: 600;">🚚 Dalam Kiriman</span>
                        @elseif ($fStatus === 'processing')
                            <span style="color: #d97706; font-weight: 600;">📦 Sedang Dikemas</span>
                        @else
                            <span style="color: #64748b;">Belum Diproses</span>
                        @endif
                    </td>
                    <td style="padding: .75rem; border-bottom: 1px solid #e2e8f0;">
                        <a href="{{ route('sales.show', $sale) }}" style="padding: 4px 10px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; font-size: .82rem; text-decoration: none; color: #334155;">
                            Detail →
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="padding: 1.5rem; text-align: center; color: #64748b;">Belum ada data transaksi penjualan sesuai filter.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1.2rem;">{{ $sales->links() }}</div>
</body>
</html>
