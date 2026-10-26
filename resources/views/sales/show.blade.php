<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Penjualan #{{ $sale->sale_number }} | Inventory Sales</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 1000px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <p><a href="{{ route('sales.index') }}">← Kembali ke Daftar Penjualan</a></p>
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin-bottom: 4px;">Detail Penjualan: {{ $sale->sale_number }}</h1>
            <div style="color: #64748b; font-size: .92rem;">Dibuat pada: {{ $sale->sold_at->format('d/m/Y H:i:s') }} WIB</div>
        </div>
        <div style="display: flex; gap: 8px;">
            @if ($sale->payment)
                <a href="{{ route('payment.show', $sale->payment->reference_number) }}" target="_blank" style="padding: 8px 16px; background: #0284c7; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: .88rem;">
                    🌐 Buka Payment Gateway →
                </a>
                @if ($sale->isPaid())
                    <a href="{{ route('payment.invoice', $sale->payment->reference_number) }}" target="_blank" style="padding: 8px 16px; background: #16a34a; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: .88rem;">
                        🖨️ Cetak Invoice Lunas
                    </a>
                @endif
            @endif
        </div>
    </div>

    @if (session('success'))
        <div style="padding: 1rem; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 1.2rem; font-weight: 600;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1.2rem;">
            @foreach ($errors->all() as $error)
                <div>✕ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 2rem;">
        <!-- Card 1: Data Pelanggan & Kasir -->
        <div style="padding: 1.2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
            <h3 style="margin-top: 0; color: #334155; font-size: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                👤 Data Pelanggan
            </h3>
            <p style="margin: 6px 0;"><strong>Nama:</strong> {{ $sale->customer_name ?: ($sale->user?->name ?? 'Pelanggan Umum') }}</p>
            <p style="margin: 6px 0;"><strong>Kontak HP / WA:</strong> {{ $sale->customer_phone ?: '-' }}</p>
            <p style="margin: 6px 0;"><strong>Kasir / Akun:</strong> {{ $sale->user?->name ?? 'Akses Toko Langsung' }}</p>
            <p style="margin: 6px 0;"><strong>Status Order:</strong> 
                <span style="font-weight: 700; text-transform: uppercase;">{{ $sale->status }}</span>
            </p>
        </div>

        <!-- Card 2: Informasi Pembayaran -->
        <div style="padding: 1.2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
            <h3 style="margin-top: 0; color: #334155; font-size: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                💳 Status Pembayaran
            </h3>
            @php
                $payment = $sale->payment;
                $pStatus = $sale->payment_status ?: ($payment?->status ?? 'pending');
            @endphp
            <p style="margin: 6px 0;"><strong>Metode:</strong> {{ $sale->payment_channel ?: ($payment?->payment_channel ?? 'Belum dipilih') }}</p>
            @if ($payment && $payment->virtual_account_number)
                <p style="margin: 6px 0;"><strong>No. VA:</strong> <code>{{ $payment->virtual_account_number }}</code></p>
            @endif
            <p style="margin: 6px 0;"><strong>Status Bayar:</strong>
                @if ($pStatus === 'paid')
                    <span style="color: #16a34a; font-weight: 800; background: #dcfce7; padding: 2px 8px; border-radius: 6px;">✓ LUNAS</span>
                @elseif ($pStatus === 'waiting_verification')
                    <span style="color: #d97706; font-weight: 800; background: #fef3c7; padding: 2px 8px; border-radius: 6px;">⏳ MENUNGGU VERIFIKASI</span>
                @elseif ($pStatus === 'expired')
                    <span style="color: #dc2626; font-weight: 800; background: #fee2e2; padding: 2px 8px; border-radius: 6px;">🔴 KEDALUWARSA</span>
                @elseif ($pStatus === 'failed')
                    <span style="color: #991b1b; font-weight: 800; background: #fee2e2; padding: 2px 8px; border-radius: 6px;">✕ DITOLAK / GAGAL</span>
                @else
                    <span style="color: #0284c7; font-weight: 800; background: #e0f2fe; padding: 2px 8px; border-radius: 6px;">⏱️ BELUM DIBAYAR</span>
                @endif
            </p>
            @if ($payment && $payment->paid_at)
                <p style="margin: 6px 0; font-size: .88rem; color: #166534;">
                    <strong>Waktu Bayar:</strong> {{ $payment->paid_at->format('d/m/Y H:i:s') }} WIB
                </p>
            @endif
            @if ($payment && $payment->verifier)
                <p style="margin: 6px 0; font-size: .88rem; color: #475569;">
                    <strong>Diverifikasi oleh:</strong> {{ $payment->verifier->name }}
                </p>
            @endif
        </div>

        <!-- Card 3: Status Pengiriman / Gudang -->
        <div style="padding: 1.2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
            <h3 style="margin-top: 0; color: #334155; font-size: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                📦 Status Pengiriman (Gudang)
            </h3>
            <p style="margin: 6px 0;"><strong>Status Saat Ini:</strong>
                <span style="font-weight: 700; color: #176b78;">{{ $sale->fulfillment_status_label }}</span>
            </p>
            @if ($sale->fulfilled_at)
                <p style="margin: 6px 0; font-size: .85rem; color: #64748b;">
                    <strong>Diperbarui:</strong> {{ $sale->fulfilled_at->format('d/m/Y H:i') }} WIB oleh {{ $sale->fulfiller?->name ?? 'Staff' }}
                </p>
            @endif

            @if (!$sale->isPaid())
                <div style="margin-top: 10px; padding: 8px 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; font-size: .82rem; color: #991b1b;">
                    ⚠️ Barang tidak dapat diproses atau dikirim sampai pembayaran <strong>LUNAS</strong>.
                </div>
            @endif
        </div>
    </div>

    <!-- VERIFIKASI BUKTI TRANSFER MANUAL (BILA ADA) -->
    @if ($payment && ($payment->isWaitingVerification() || $payment->proof_of_payment))
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: #fffbeb; border: 2px solid #fde68a; border-radius: 12px;">
            <h3 style="margin-top: 0; color: #92400e; display: flex; align-items: center; gap: 8px;">
                <span>📸</span> Bukti Pembayaran Transfer Manual Pelanggan
            </h3>
            
            <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-start; margin-top: 12px;">
                @if ($payment->proof_of_payment)
                    <div style="border: 2px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #fff; max-width: 280px;">
                        <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" target="_blank" title="Klik untuk memperbesar">
                            <img src="{{ asset('storage/' . $payment->proof_of_payment) }}" alt="Bukti Transfer" style="max-width: 100%; max-height: 240px; display: block; object-fit: contain;">
                        </a>
                        <div style="padding: 6px 10px; background: #f1f5f9; font-size: .8rem; text-align: center;">
                            <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" target="_blank">🔎 Buka File Gambar Asli</a>
                        </div>
                    </div>
                @else
                    <p style="color: #64748b;">Belum ada file bukti yang diunggah.</p>
                @endif

                <div style="flex: 1; min-width: 280px;">
                    <p style="margin-top: 0; color: #78350f; font-size: .92rem;">
                        Periksa keaslian bukti transfer di atas dengan mutasi rekening toko sebelum menyetujui transaksi.
                    </p>

                    @if ($payment->isWaitingVerification())
                        <form method="POST" action="{{ route('sales.verify-payment', $sale) }}" style="display: flex; flex-direction: column; gap: 10px;">
                            @csrf
                            <label style="margin: 0;">Catatan Verifikasi (Opsional):<br>
                                <input type="text" name="notes" placeholder="Contoh: Dana masuk rekening BCA jam 14:15" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </label>
                            
                            <div style="display: flex; gap: 10px; margin-top: 6px;">
                                <button type="submit" name="action" value="approve" style="padding: 10px 18px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="return confirm('Apakah Anda yakin dana pembayaran telah masuk ke rekening toko?')">
                                    ✓ Terima &amp; Setujui Pembayaran (Lunas)
                                </button>
                                <button type="submit" name="action" value="reject" style="padding: 10px 18px; background: #dc2626; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="return confirm('Tolak bukti pembayaran ini dan batalkan pesanan?')">
                                    ✕ Tolak Pembayaran
                                </button>
                            </div>
                        </form>
                    @else
                        <div style="padding: 10px 14px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; font-size: .9rem;">
                            <strong>Catatan Petugas:</strong> {{ $payment->notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- AKSI PENGIRIMAN & GUDANG (STAFF) -->
    @if ($sale->status !== 'cancelled')
        <div style="margin-bottom: 2rem; padding: 1.2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
            <h3 style="margin-top: 0; color: #1e293b;">Alur Proses Barang di Gudang</h3>
            <p style="color: #64748b; font-size: .88rem; margin-bottom: 1rem;">
                Perbarui status pesanan dari pengemasan hingga pengiriman ke tangan pelanggan.
            </p>

            <form method="POST" action="{{ route('sales.update-fulfillment', $sale) }}" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                @csrf
                <button type="submit" name="fulfillment_status" value="processing" 
                    @disabled(!$sale->isPaid() || $sale->fulfillment_status === 'processing')
                    style="padding: 8px 16px; background: #f59e0b; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    📦 1. Mulai Kemas Barang (Processing)
                </button>

                <button type="submit" name="fulfillment_status" value="shipped" 
                    @disabled(!$sale->isPaid() || $sale->fulfillment_status === 'shipped')
                    style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    🚚 2. Kirim Barang (Shipped)
                </button>

                <button type="submit" name="fulfillment_status" value="delivered" 
                    @disabled(!$sale->isPaid() || $sale->fulfillment_status === 'delivered')
                    style="padding: 8px 16px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    ✓ 3. Selesai Diterima (Delivered)
                </button>
            </form>
        </div>
    @endif

    <!-- Tabel Item Produk -->
    <h2>Item Produk yang Dipesan</h2>
    <table style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 1.5rem;">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1;">SKU</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1;">Produk</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1; text-align: center;">Jumlah</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1; text-align: right;">Harga per Unit</th>
                <th style="padding: .7rem; border-bottom: 1px solid #cbd5e1; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;"><code>{{ $item->product->sku }}</code></td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0;"><strong>{{ $item->product->name }}</strong></td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; text-align: center;">{{ $item->quantity }}</td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td style="padding: .7rem; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600;">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display: flex; justify-content: flex-end; margin-bottom: 2rem;">
        <div style="width: 300px; padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span>Subtotal:</span>
                <strong>Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</strong>
            </div>
            @if ($payment && $payment->admin_fee > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Biaya Layanan:</span>
                    <span>Rp {{ number_format((float) $payment->admin_fee, 0, ',', '.') }}</span>
                </div>
            @endif
            @if ($payment && $payment->unique_code > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Kode Unik:</span>
                    <span>+Rp {{ number_format((float) $payment->unique_code, 0, ',', '.') }}</span>
                </div>
            @endif
            <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; border-top: 2px solid #0f172a; padding-top: 8px; margin-top: 6px;">
                <span>Total Tagihan:</span>
                <span style="color: #176b78;">Rp {{ number_format((float) ($payment?->total_amount ?? $sale->total), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if (auth()->user()->role === 'admin' && $sale->status !== 'cancelled')
        <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('PERINGATAN: Batalkan transaksi penjualan ini dan kembalikan stok barang ke inventaris?')" style="margin-top: 2rem;">
            @csrf
            <button type="submit" style="padding: 8px 16px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px; cursor: pointer; font-weight: 600;">
                ✕ Batalkan Transaksi &amp; Kembalikan Stok
            </button>
        </form>
    @endif
</body>
</html>
