<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Resmi #{{ $payment->reference_number }} | Inventory &amp; Sales</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body.invoice-page {
            background: #f8fafc;
            color: #0f172a;
            font-family: 'DM Sans', Inter, system-ui, -apple-system, sans-serif;
            padding: 2rem 1rem;
            margin: 0;
            display: block !important;
            max-width: 100% !important;
            width: 100% !important;
            min-height: 100vh;
        }
        .invoice-paper {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            position: relative;
            overflow: hidden;
        }
        .invoice-watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 5.5rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            pointer-events: none;
            user-select: none;
            z-index: 1;
            padding: 10px 40px;
            border: 6px dashed;
            border-radius: 20px;
        }
        .watermark-paid {
            color: rgba(22, 163, 74, 0.16);
            border-color: rgba(22, 163, 74, 0.22);
        }
        .watermark-pending {
            color: rgba(217, 119, 6, 0.14);
            border-color: rgba(217, 119, 6, 0.2);
        }
        .watermark-expired {
            color: rgba(220, 38, 38, 0.14);
            border-color: rgba(220, 38, 38, 0.2);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 24px;
            margin-bottom: 28px;
            position: relative;
            z-index: 2;
        }
        .company-logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #132b3d;
            letter-spacing: -0.03em;
        }
        .company-logo-text span {
            color: #24837b;
        }
        .company-sub {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 4px;
        }
        .invoice-meta-head {
            text-align: right;
        }
        .invoice-title-tag {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: -0.02em;
        }
        .invoice-num {
            font-size: 0.95rem;
            color: #176b78;
            font-weight: 700;
            margin-top: 4px;
        }
        .invoice-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 32px;
            position: relative;
            z-index: 2;
        }
        .detail-box-title {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .detail-box-content p {
            margin: 3px 0;
            font-size: 0.92rem;
            color: #334155;
        }
        .detail-box-content strong {
            color: #0f172a;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
            position: relative;
            z-index: 2;
        }
        .invoice-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 16px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        .invoice-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.92rem;
            color: #1e293b;
        }
        .invoice-table td.text-right, .invoice-table th.text-right {
            text-align: right;
        }
        .invoice-table td.text-center, .invoice-table th.text-center {
            text-align: center;
        }
        .invoice-summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 36px;
            position: relative;
            z-index: 2;
        }
        .summary-table {
            width: 320px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.92rem;
            color: #475569;
            border-bottom: 1px dashed #e2e8f0;
        }
        .summary-row.grand-total {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            padding: 12px 0;
            margin-top: 4px;
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }
        .invoice-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 24px;
            font-size: 0.84rem;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        .invoice-actions-bar {
            max-width: 820px;
            margin: 1.5rem auto 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            padding: 10px 22px;
            background: #176b78;
            color: #fff;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover {
            background: #0f4d59;
        }
        .btn-back-inv {
            color: #547083;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.92rem;
        }
        .btn-back-inv:hover {
            color: #176b78;
            text-decoration: underline;
        }

        @media print {
            body.invoice-page {
                background: #fff;
                padding: 0;
            }
            .invoice-paper {
                box-shadow: none;
                border: none;
                padding: 20px 0;
                max-width: 100%;
            }
            .invoice-actions-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body class="invoice-page">
    <div class="invoice-actions-bar">
        <a href="{{ route('storefront.index') }}" class="btn-back-inv">
            ← Kembali ke Halaman Utama Customer
        </a>
        <button type="button" class="btn-print" onclick="window.print()">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="invoice-paper">
        <!-- Watermark -->
        @if ($payment->isPaid())
            <div class="invoice-watermark watermark-paid">LUNAS / PAID</div>
        @elseif ($payment->isExpired())
            <div class="invoice-watermark watermark-expired">EXPIRED</div>
        @else
            <div class="invoice-watermark watermark-pending">PENDING</div>
        @endif

        <!-- Header -->
        <div class="invoice-header">
            <div>
                <div class="company-logo-text">INVENTORY <span>&amp;</span> SALES</div>
                <div class="company-sub">Sistem Manajemen Stok &amp; Penjualan Berbasis Web (Project UAS)</div>
                <div class="company-sub">Jl. Kampus Merdeka No. 10, Jakarta Selatan</div>
            </div>
            <div class="invoice-meta-head">
                <h1 class="invoice-title-tag">INVOICE RESMI</h1>
                <div class="invoice-num">{{ $payment->reference_number }}</div>
                <div class="company-sub">Tanggal Cetak: {{ now()->format('d/m/Y H:i') }} WIB</div>
            </div>
        </div>

        <!-- Meta Details -->
        <div class="invoice-details-grid">
            <div>
                <div class="detail-box-title">DITAGIHKAN KEPADA:</div>
                <div class="detail-box-content">
                    <p><strong>Nama:</strong> {{ $payment->sale->customer_name ?: 'Pelanggan Umum' }}</p>
                    <p><strong>No. Kontak:</strong> {{ $payment->sale->customer_phone ?: '-' }}</p>
                    <p><strong>No. Pesanan:</strong> {{ $payment->sale->sale_number }}</p>
                    <p><strong>Tanggal Transaksi:</strong> {{ $payment->sale->sold_at->format('d/m/Y H:i') }} WIB</p>
                </div>
            </div>
            <div>
                <div class="detail-box-title">INFORMASI PEMBAYARAN:</div>
                <div class="detail-box-content">
                    <p><strong>Metode Bayar:</strong> {{ $payment->payment_channel }}</p>
                    <p><strong>Kategori:</strong> {{ $payment->channel?->category_label ?? strtoupper($payment->payment_method) }}</p>
                    @if ($payment->virtual_account_number)
                        <p><strong>No. VA:</strong> {{ $payment->virtual_account_number }}</p>
                    @endif
                    <p><strong>Status:</strong> 
                        <span style="color: {{ $payment->status_color }}; font-weight: 800;">
                            {{ strtoupper($payment->status_label) }}
                        </span>
                    </p>
                    @if ($payment->paid_at)
                        <p><strong>Waktu Lunas:</strong> {{ $payment->paid_at->format('d/m/Y H:i:s') }} WIB</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Item Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th>Kode / SKU</th>
                    <th>Nama Produk</th>
                    <th class="text-center" style="width: 90px;">Jumlah</th>
                    <th class="text-right" style="width: 140px;">Harga Satuan</th>
                    <th class="text-right" style="width: 150px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payment->sale->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><code>{{ $item->product->sku }}</code></td>
                        <td><strong>{{ $item->product->name }}</strong></td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="invoice-summary-section">
            <div class="summary-table">
                <div class="summary-row">
                    <span>Subtotal Produk</span>
                    <span>Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Biaya Layanan Admin</span>
                    <span>Rp {{ number_format((float) $payment->admin_fee, 0, ',', '.') }}</span>
                </div>
                @if ($payment->unique_code > 0)
                    <div class="summary-row">
                        <span>Kode Unik Transaksi</span>
                        <span>+Rp {{ number_format((float) $payment->unique_code, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="summary-row grand-total">
                    <span>TOTAL TAGIHAN</span>
                    <span>Rp {{ number_format((float) $payment->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div>
                <strong>Catatan:</strong>
                <div>Struk ini adalah bukti transaksi digital resmi dari sistem Inventory &amp; Sales.</div>
                <div>Terima kasih atas kepercayaan Anda bertransaksi dengan kami.</div>
            </div>
            <div style="text-align: right;">
                <div>Petugas Verifikasi:</div>
                <div style="margin-top: 30px; font-weight: 700; border-top: 1px solid #cbd5e1; padding-top: 4px;">
                    {{ $payment->verifier?->name ?? 'Sistem Otomatis (Gateway)' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
