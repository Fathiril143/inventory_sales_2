<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Transaksi Pembayaran | UAS Inventory Sales</title>
    <style>
        @media print {
            body {
                background: #fff !important;
                padding: 10px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
            table {
                font-size: 11px !important;
            }
            .report-official-header {
                display: block !important;
            }
            .report-signatures {
                display: flex !important;
            }
        }
        .report-official-header {
            display: none;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .report-signatures {
            display: none;
            justify-content: space-between;
            margin-top: 50px;
            padding: 0 40px;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; max-width: 1100px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <div class="no-print">
        <p><a href="{{ route('dashboard') }}">← Kembali ke Dashboard</a></p>
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 1rem;">
            <div>
                <h1 style="margin-bottom: 4px;">Laporan Transaksi Pembayaran</h1>
                <p style="color: #64748b; margin: 0;">Rekapitulasi keuangan seluruh transaksi masuk melalui Payment Gateway &amp; Kasir.</p>
            </div>
            <button type="button" onclick="window.print()" style="padding: 10px 20px; background: #176b78; color: #fff; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                🖨️ Cetak / Unduh Laporan (PDF)
            </button>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('payment-reports.index') }}" style="margin: 1.2rem 0; padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; flex-wrap: wrap; gap: .8rem; align-items: end;">
            <label>Dari tanggal<br><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></label>
            <label>Sampai tanggal<br><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></label>
            <label>Status Bayar<br>
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="paid" @selected(($filters['status'] ?? '') === 'paid')>🟢 Lunas (Paid)</option>
                    <option value="waiting_verification" @selected(($filters['status'] ?? '') === 'waiting_verification')>🟡 Menunggu Verifikasi</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>🔵 Belum Dibayar (Pending)</option>
                    <option value="expired" @selected(($filters['status'] ?? '') === 'expired')>🔴 Kedaluwarsa</option>
                    <option value="failed" @selected(($filters['status'] ?? '') === 'failed')>✕ Gagal / Ditolak</option>
                </select>
            </label>
            <label>Metode / Channel<br>
                <select name="channel">
                    <option value="">Semua Channel</option>
                    @foreach ($channels as $ch)
                        <option value="{{ $ch->name }}" @selected(($filters['channel'] ?? '') === $ch->name)>{{ $ch->name }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" style="padding: .65rem 1.2rem; background: #176b78; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                Terapkan
            </button>
            <a href="{{ route('payment-reports.index') }}" style="padding: .65rem 1rem; color: #64748b; text-decoration: none;">Reset</a>
        </form>
    </div>

    <!-- Header Resmi Khusus Cetak -->
    <div class="report-official-header">
        <h2 style="margin: 0; text-transform: uppercase;">LAPORAN TRANSAKSI KEUANGAN &amp; PENJUALAN</h2>
        <div style="font-size: 13px; color: #334155;">Sistem Inventory &amp; Sales Management — Project UAS</div>
        <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Periode: {{ ($filters['date_from'] ?? null) ?: 'Awal' }} s/d {{ ($filters['date_to'] ?? null) ?: 'Hari Ini' }} | Tanggal Cetak: {{ now()->format('d/m/Y H:i') }} WIB</div>
    </div>

    <!-- Ringkasan Statistik -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 1.5rem;">
        <div style="padding: 1.2rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;">
            <div style="font-size: .8rem; color: #166534; font-weight: 700; text-transform: uppercase;">Total Penerimaan Lunas</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #15803d; margin-top: 4px;">
                Rp {{ number_format((float) $totalPaid, 0, ',', '.') }}
            </div>
        </div>
        <div style="padding: 1.2rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;">
            <div style="font-size: .8rem; color: #92400e; font-weight: 700; text-transform: uppercase;">Total Menunggu Bayar</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #b45309; margin-top: 4px;">
                Rp {{ number_format((float) $totalPending, 0, ',', '.') }}
            </div>
        </div>
        <div style="padding: 1.2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
            <div style="font-size: .8rem; color: #475569; font-weight: 700; text-transform: uppercase;">Total Transaksi Terfilter</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-top: 4px;">
                {{ $totalTransactionsCount }} Transaksi
            </div>
        </div>
    </div>

    <!-- Tabel Data Laporan -->
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1;">Tanggal</th>
                <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1;">No. Invoice</th>
                <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1;">Pelanggan</th>
                <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1;">Metode / Channel</th>
                <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1; text-align: right;">Total Tagihan</th>
                <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1; text-align: center;">Status</th>
                <th style="padding: .7rem; border-bottom: 2px solid #cbd5e1; text-align: right;" class="no-print">Struk</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $p)
                <tr>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; font-size: .85rem;">
                        {{ $p->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; font-family: monospace; font-size: .85rem;">
                        <strong>{{ $p->reference_number }}</strong>
                        <div style="font-size: .75rem; color: #64748b;">Order: {{ $p->sale->sale_number }}</div>
                    </td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;">
                        {{ $p->sale->customer_name ?: ($p->user?->name ?? 'Pelanggan Umum') }}
                    </td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; font-size: .88rem;">
                        {{ $p->payment_channel }}
                    </td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 700;">
                        Rp {{ number_format((float) $p->total_amount, 0, ',', '.') }}
                    </td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; text-align: center;">
                        <span style="font-weight: 700; font-size: .8rem; color: {{ $p->status_color }};">
                            {{ strtoupper($p->status_label) }}
                        </span>
                    </td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; text-align: right;" class="no-print">
                        <a href="{{ route('payment.invoice', $p->reference_number) }}" target="_blank" style="padding: 4px 8px; background: #f1f5f9; border-radius: 6px; font-size: .78rem; color: #176b78; text-decoration: none;">
                            Invoice →
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="padding: 1.5rem; text-align: center; color: #64748b;">Tidak ada transaksi pembayaran pada kriteria filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="no-print" style="margin-top: 1.2rem;">
        {{ $payments->links() }}
    </div>

    <!-- Tanda Tangan Pengesahan Khusus Cetak Laporan UAS -->
    <div class="report-signatures">
        <div style="text-align: center;">
            <div>Mengetahui,</div>
            <div style="margin-top: 60px; font-weight: 700; border-top: 1px solid #000; padding-top: 4px; width: 180px;">
                Dosen Penguji / Pembimbing
            </div>
        </div>
        <div style="text-align: center;">
            <div>Jakarta, {{ now()->format('d F Y') }}</div>
            <div>Dibuat oleh (Mahasiswa),</div>
            <div style="margin-top: 60px; font-weight: 700; border-top: 1px solid #000; padding-top: 4px; width: 180px;">
                {{ auth()->user()->name }}
            </div>
        </div>
    </div>
</body>
</html>
