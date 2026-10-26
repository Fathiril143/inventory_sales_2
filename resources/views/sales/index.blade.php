<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penjualan | Inventory Sales</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 1100px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <p><a href="{{ route('dashboard') }}">← Kembali ke Dashboard</a></p>
    <h1>Penjualan</h1>

    @if (session('success'))
        <p style="padding: .8rem; background: #dcfce7; color: #166534;">{{ session('success') }}</p>
    @endif

    <p><a href="{{ route('sales.create') }}">+ Catat Penjualan</a></p>

    <form method="GET" action="{{ route('sales.index') }}" style="margin: 1rem 0; display: flex; flex-wrap: wrap; gap: .6rem; align-items: end;">
        <label>Dari tanggal<br><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></label>
        <label>Sampai tanggal<br><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></label>
        <label>Status<br><select name="status"><option value="">Semua</option><option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Selesai</option><option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Dibatalkan</option></select></label>
        <button type="submit">Terapkan Filter</button>
        <a href="{{ route('sales.index') }}">Bersihkan</a>
    </form>
    @error('date_to') <p style="color: #b91c1c;">{{ $message }}</p> @enderror

    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1;">Nomor</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1;">Tanggal</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1;">Kasir / Pelanggan</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1;">Jenis Produk</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1;">Total</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;"><a href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a></td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;">{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;">{{ $sale->user?->name ?? $sale->customer_name ?? 'Pelanggan tanpa akun' }}</td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;">{{ $sale->items_count }}</td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;">Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;">{{ ucfirst($sale->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="padding: 1rem;">Belum ada transaksi penjualan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">{{ $sales->links() }}</div>
</body>
</html>
