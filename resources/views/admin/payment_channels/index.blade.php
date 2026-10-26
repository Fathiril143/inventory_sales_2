<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Channel Pembayaran | Admin</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 1050px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <p><a href="{{ route('dashboard') }}">← Kembali ke Dashboard</a></p>
    <h1>Manajemen Channel Pembayaran (Mock Gateway)</h1>
    <p style="color: #64748b;">
        Atur metode pembayaran yang aktif di toko, ubah nomor rekening tujuan transfer, serta tetapkan biaya admin.
    </p>

    @if (session('success'))
        <div style="padding: .8rem 1rem; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 1.2rem; font-weight: 600;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <table style="width: 100%; border-collapse: collapse; text-align: left; margin-top: 1rem;">
        <thead>
            <tr style="background: #f8fafc;">
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Metode &amp; Kode</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Kategori</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">No. Rekening / VA</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Atas Nama</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1;">Biaya Admin</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1; text-align: center;">Status</th>
                <th style="padding: .75rem; border-bottom: 2px solid #cbd5e1; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($channels as $channel)
                <tr>
                    <td style="padding: .85rem; border-bottom: 1px solid #e2e8f0;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: {{ $channel->color ?: '#176b78' }};"></span>
                            <div>
                                <strong>{{ $channel->name }}</strong>
                                <div style="font-size: .78rem; color: #64748b;"><code>{{ $channel->code }}</code></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: .85rem; border-bottom: 1px solid #e2e8f0;">
                        <span style="padding: 3px 8px; background: #f1f5f9; border-radius: 6px; font-size: .8rem; font-weight: 600;">
                            {{ $channel->category_label }}
                        </span>
                    </td>
                    <td style="padding: .85rem; border-bottom: 1px solid #e2e8f0; font-family: monospace;">
                        {{ $channel->account_number ?: '-' }}
                    </td>
                    <td style="padding: .85rem; border-bottom: 1px solid #e2e8f0;">
                        {{ $channel->account_name ?: '-' }}
                    </td>
                    <td style="padding: .85rem; border-bottom: 1px solid #e2e8f0;">
                        Rp {{ number_format((float) $channel->admin_fee, 0, ',', '.') }}
                    </td>
                    <td style="padding: .85rem; border-bottom: 1px solid #e2e8f0; text-align: center;">
                        <form method="POST" action="{{ route('payment-channels.toggle', $channel) }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="padding: 4px 10px; border-radius: 20px; font-size: .78rem; font-weight: 700; cursor: pointer; border: none; background: {{ $channel->is_active ? '#dcfce7' : '#fee2e2' }}; color: {{ $channel->is_active ? '#15803d' : '#b91c1c' }};">
                                {{ $channel->is_active ? '✓ Aktif' : '✕ Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td style="padding: .85rem; border-bottom: 1px solid #e2e8f0; text-align: right;">
                        <a href="{{ route('payment-channels.edit', $channel) }}" style="padding: 5px 12px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; font-size: .82rem; text-decoration: none; color: #176b78; font-weight: 600;">
                            Ubah
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
