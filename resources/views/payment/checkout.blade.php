<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Gateway | {{ $payment->reference_number }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="payment-gateway-page">
    <header class="pg-header">
        <div class="pg-header-inner">
            <div class="pg-brand">
                <a href="{{ route('storefront.index') }}" class="pg-brand-link">
                    <span class="pg-brand-dot"></span>
                    <span class="pg-brand-title">Inventory <span>&amp;</span> Sales</span>
                </a>
                <span class="pg-badge-demo">🎓 UAS Demo Mode Active</span>
            </div>
            <div class="pg-order-tag">
                <span class="pg-ref-label">No. Tagihan:</span>
                <span class="pg-ref-value">{{ $payment->reference_number }}</span>
            </div>
        </div>
    </header>

    <main class="pg-container">
        <!-- Banner Notifikasi -->
        @if (session('success'))
            <div class="pg-alert pg-alert-success" role="alert">
                <span class="pg-alert-icon">✓</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('simulated_success'))
            <div class="pg-alert pg-alert-success" role="alert">
                <span class="pg-alert-icon">⚡</span>
                <div>{{ session('simulated_success') }}</div>
            </div>
        @endif
        @if (session('simulated_expired'))
            <div class="pg-alert pg-alert-danger" role="alert">
                <span class="pg-alert-icon">⏳</span>
                <div>{{ session('simulated_expired') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="pg-alert pg-alert-danger" role="alert">
                <span class="pg-alert-icon">✕</span>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="pg-layout">
            <!-- Kolom Kiri: Midtrans/Xendit Payment Card -->
            <div class="pg-main-card">
                <!-- Header Card: Metode & Timer -->
                <div class="pg-card-header">
                    <div class="pg-method-info">
                        <span class="pg-method-eyebrow">{{ $payment->channel?->category_label ?? strtoupper($payment->payment_method) }}</span>
                        <h1 class="pg-method-name">{{ $payment->payment_channel }}</h1>
                    </div>
                    <div class="pg-status-badge status-{{ $payment->status }}" id="payment-status-badge">
                        {{ $payment->status_label }}
                    </div>
                </div>

                <!-- Total & Countdown Bar -->
                <div class="pg-total-bar">
                    <div class="pg-total-info">
                        <span class="pg-total-label">Total Pembayaran</span>
                        <div class="pg-total-amount" id="total-amount-display">
                            Rp {{ number_format((float) $payment->total_amount, 0, ',', '.') }}
                            <button type="button" class="btn-copy-sm" onclick="copyText('{{ (int) $payment->total_amount }}', 'Jumlah')" title="Salin jumlah">
                                📋 Salin
                            </button>
                        </div>
                    </div>
                    @if (!$payment->isPaid() && !$payment->isExpired())
                        <div class="pg-timer-box">
                            <span class="pg-timer-label">Selesaikan dalam:</span>
                            <div class="pg-timer-digits" id="countdown-timer">23:59:59</div>
                        </div>
                    @elseif($payment->isPaid())
                        <div class="pg-paid-stamp-box">
                            <span class="pg-paid-stamp">LUNAS</span>
                            <span class="pg-paid-date">{{ $payment->paid_at?->format('d M Y, H:i') }} WIB</span>
                        </div>
                    @else
                        <div class="pg-expired-stamp-box">
                            <span class="pg-expired-stamp">EXPIRED</span>
                        </div>
                    @endif
                </div>

                <!-- Section Konten Pembayaran Sesuai Metode -->
                <div class="pg-card-body">
                    {{-- 1. METODE QRIS --}}
                    @if ($payment->payment_method === 'qris')
                        <div class="pg-qris-container">
                            <div class="qris-header-badge">
                                <span class="qris-text">QRIS</span>
                                <span class="qris-sub">QR Code Standar Pembayaran Nasional</span>
                            </div>
                            <div class="qris-qr-wrapper">
                                <!-- Offline Dynamic QR SVG Generator -->
                                <div id="qris-qr-code" class="qris-qr-box">
                                    <svg viewBox="0 0 200 200" width="200" height="200" class="qris-svg">
                                        <!-- Realistic Offline QR Pattern -->
                                        <rect width="200" height="200" fill="#ffffff" rx="10" />
                                        <!-- Position Detection Pattern: Top-Left -->
                                        <rect x="15" y="15" width="45" height="45" fill="#000" rx="6"/>
                                        <rect x="22" y="22" width="31" height="31" fill="#fff" rx="3"/>
                                        <rect x="29" y="29" width="17" height="17" fill="#000" rx="2"/>

                                        <!-- Position Detection Pattern: Top-Right -->
                                        <rect x="140" y="15" width="45" height="45" fill="#000" rx="6"/>
                                        <rect x="147" y="22" width="31" height="31" fill="#fff" rx="3"/>
                                        <rect x="154" y="29" width="17" height="17" fill="#000" rx="2"/>

                                        <!-- Position Detection Pattern: Bottom-Left -->
                                        <rect x="15" y="140" width="45" height="45" fill="#000" rx="6"/>
                                        <rect x="22" y="147" width="31" height="31" fill="#fff" rx="3"/>
                                        <rect x="29" y="154" width="17" height="17" fill="#000" rx="2"/>

                                        <!-- Data Grid Matrix (Stylized dynamic mockup) -->
                                        <g fill="#111827">
                                            <rect x="70" y="18" width="8" height="8" /><rect x="85" y="18" width="8" height="8" /><rect x="100" y="18" width="8" height="8" /><rect x="115" y="18" width="8" height="8" />
                                            <rect x="75" y="32" width="8" height="8" /><rect x="95" y="32" width="8" height="8" /><rect x="110" y="32" width="8" height="8" /><rect x="125" y="32" width="8" height="8" />
                                            <rect x="70" y="46" width="8" height="8" /><rect x="90" y="46" width="8" height="8" /><rect x="105" y="46" width="8" height="8" /><rect x="120" y="46" width="8" height="8" />
                                            
                                            <!-- Middle & Timing Pattern -->
                                            <rect x="20" y="70" width="8" height="8" /><rect x="35" y="70" width="8" height="8" /><rect x="50" y="70" width="8" height="8" /><rect x="65" y="70" width="8" height="8" /><rect x="80" y="70" width="8" height="8" /><rect x="95" y="70" width="8" height="8" /><rect x="110" y="70" width="8" height="8" /><rect x="125" y="70" width="8" height="8" /><rect x="140" y="70" width="8" height="8" /><rect x="155" y="70" width="8" height="8" /><rect x="170" y="70" width="8" height="8" />
                                            <rect x="20" y="85" width="8" height="8" /><rect x="40" y="85" width="8" height="8" /><rect x="70" y="85" width="8" height="8" /><rect x="120" y="85" width="8" height="8" /><rect x="145" y="85" width="8" height="8" /><rect x="170" y="85" width="8" height="8" />
                                            <rect x="30" y="100" width="8" height="8" /><rect x="55" y="100" width="8" height="8" /><rect x="80" y="100" width="8" height="8" /><rect x="110" y="100" width="8" height="8" /><rect x="135" y="100" width="8" height="8" /><rect x="160" y="100" width="8" height="8" />
                                            <rect x="20" y="115" width="8" height="8" /><rect x="45" y="115" width="8" height="8" /><rect x="75" y="115" width="8" height="8" /><rect x="100" y="115" width="8" height="8" /><rect x="125" y="115" width="8" height="8" /><rect x="155" y="115" width="8" height="8" />

                                            <!-- Bottom Half Matrix -->
                                            <rect x="70" y="140" width="8" height="8" /><rect x="90" y="140" width="8" height="8" /><rect x="115" y="140" width="8" height="8" /><rect x="140" y="140" width="8" height="8" /><rect x="165" y="140" width="8" height="8" />
                                            <rect x="75" y="155" width="8" height="8" /><rect x="100" y="155" width="8" height="8" /><rect x="125" y="155" width="8" height="8" /><rect x="150" y="155" width="8" height="8" /><rect x="170" y="155" width="8" height="8" />
                                            <rect x="70" y="170" width="8" height="8" /><rect x="95" y="170" width="8" height="8" /><rect x="110" y="170" width="8" height="8" /><rect x="135" y="170" width="8" height="8" /><rect x="160" y="170" width="8" height="8" />
                                        </g>
                                        <!-- Center Logo Mock -->
                                        <rect x="82" y="82" width="36" height="36" fill="#fff" rx="6" stroke="#ea1f27" stroke-width="2"/>
                                        <text x="100" y="105" font-family="Arial, sans-serif" font-size="11" font-weight="900" fill="#ea1f27" text-anchor="middle">QRIS</text>
                                    </svg>
                                </div>
                                <div class="qris-meta">
                                    <div class="qris-merchant-name">INVENTORY &amp; SALES UAS</div>
                                    <div class="qris-nmid">NMID: ID1020038920192</div>
                                    <span class="qris-pill">✓ Dinamis &amp; Verifikasi Otomatis</span>
                                </div>
                            </div>
                            <div class="qris-steps">
                                <h3>Cara Pembayaran QRIS:</h3>
                                <ol>
                                    <li>Buka aplikasi <strong>BCA Mobile, Livin\' Mandiri, BRImo, BNI, GoPay, OVO, DANA</strong> atau e-wallet lainnya.</li>
                                    <li>Pilih menu <strong>Scan / Bayar</strong>.</li>
                                    <li>Arahkan kamera ke QR Code di atas.</li>
                                    <li>Periksa nama merchant dan nominal bayar: <strong>Rp {{ number_format((float) $payment->total_amount, 0, ',', '.') }}</strong>.</li>
                                    <li>Konfirmasi pembayaran dan masukkan PIN Anda.</li>
                                </ol>
                            </div>
                        </div>

                    {{-- 2. METODE VIRTUAL ACCOUNT --}}
                    @elseif ($payment->payment_method === 'va')
                        <div class="pg-va-container">
                            <div class="pg-va-card">
                                <div class="pg-va-label">Nomor Virtual Account {{ $payment->payment_channel }}</div>
                                <div class="pg-va-number-box">
                                    <span class="pg-va-number" id="va-number-text">{{ $payment->virtual_account_number }}</span>
                                    <button type="button" class="btn-copy-va" onclick="copyText('{{ $payment->virtual_account_number }}', 'Nomor Virtual Account')">
                                        📋 Salin No. VA
                                    </button>
                                </div>
                                <div class="pg-va-holder">Atas Nama: <strong>INVENTORY SALES UAS - {{ $payment->sale->customer_name ?: 'PELANGGAN' }}</strong></div>
                            </div>

                            <!-- Tabs Petunjuk Pembayaran VA -->
                            <div class="pg-instructions-tabs">
                                <div class="pg-tab-buttons">
                                    <button type="button" class="pg-tab-btn active" onclick="switchTab(this, 'tab-mbanking')">📱 M-Banking</button>
                                    <button type="button" class="pg-tab-btn" onclick="switchTab(this, 'tab-atm')">🏧 ATM</button>
                                    <button type="button" class="pg-tab-btn" onclick="switchTab(this, 'tab-ibanking')">💻 Internet Banking</button>
                                </div>
                                <div class="pg-tab-content active" id="tab-mbanking">
                                    <ol>
                                        <li>Buka aplikasi Mobile Banking bank Anda dan login.</li>
                                        <li>Pilih menu <strong>Transfer</strong> &gt; <strong>Virtual Account</strong>.</li>
                                        <li>Masukkan nomor VA: <strong>{{ $payment->virtual_account_number }}</strong>.</li>
                                        <li>Periksa nama dan nominal tagihan (harus sama persis).</li>
                                        <li>Masukkan PIN dan simpan resi bukti pembayaran.</li>
                                    </ol>
                                </div>
                                <div class="pg-tab-content" id="tab-atm">
                                    <ol>
                                        <li>Masukkan kartu ATM dan PIN Anda di mesin ATM.</li>
                                        <li>Pilih <strong>Transaksi Lainnya</strong> &gt; <strong>Transfer / Pembayaran</strong> &gt; <strong>Virtual Account</strong>.</li>
                                        <li>Ketikkan nomor VA: <strong>{{ $payment->virtual_account_number }}</strong> lalu tekan Benar.</li>
                                        <li>Konfirmasi data pembayaran yang tampil di layar mesin ATM.</li>
                                        <li>Ambil dan simpan struk pembayaran Anda.</li>
                                    </ol>
                                </div>
                                <div class="pg-tab-content" id="tab-ibanking">
                                    <ol>
                                        <li>Login ke portal Internet Banking bank Anda.</li>
                                        <li>Akses menu <strong>Pembayaran Tagihan</strong> &gt; <strong>Virtual Account</strong>.</li>
                                        <li>Input nomor Virtual Account dan ikuti otorisasi token/Key.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                    {{-- 3. METODE E-WALLET --}}
                    @elseif ($payment->payment_method === 'ewallet')
                        <div class="pg-ewallet-container">
                            <div class="pg-ewallet-hero">
                                <div class="pg-ewallet-icon-bubble">{{ strtoupper(substr($payment->payment_channel, 0, 1)) }}</div>
                                <h2>Bayar dengan {{ $payment->payment_channel }}</h2>
                                <p>Selesaikan pembayaran langsung melalui aplikasi e-wallet smartphone Anda.</p>
                                <div class="pg-ewallet-phone-box">
                                    <span>Nomor Akun / ID Merchant:</span>
                                    <strong>{{ $payment->channel?->account_number ?: '0812-9988-7711' }}</strong>
                                </div>
                            </div>
                            <div class="pg-ewallet-steps">
                                <h3>Petunjuk Pembayaran:</h3>
                                <ol>
                                    <li>Buka aplikasi <strong>{{ $payment->payment_channel }}</strong> di ponsel Anda.</li>
                                    <li>Lakukan pembayaran atau transfer ke nomor merchant di atas sebesar <strong>Rp {{ number_format((float) $payment->total_amount, 0, ',', '.') }}</strong>.</li>
                                    <li>Masukkan PIN transaksi Anda untuk mengonfirmasi.</li>
                                </ol>
                            </div>
                        </div>

                    {{-- 4. METODE TRANSFER MANUAL --}}
                    @elseif ($payment->payment_method === 'manual_transfer')
                        <div class="pg-manual-container">
                            <div class="pg-manual-bank-card">
                                <h3>Rekening Tujuan Transfer:</h3>
                                <div class="pg-bank-row">
                                    <span>Bank:</span>
                                    <strong>{{ $payment->payment_channel }}</strong>
                                </div>
                                <div class="pg-bank-row">
                                    <span>Nomor Rekening:</span>
                                    <div class="copyable-inline">
                                        <strong id="manual-account-no">{{ $payment->channel?->account_number }}</strong>
                                        <button type="button" class="btn-copy-sm" onclick="copyText('{{ $payment->channel?->account_number }}', 'Nomor Rekening')">📋 Salin</button>
                                    </div>
                                </div>
                                <div class="pg-bank-row">
                                    <span>Atas Nama:</span>
                                    <strong>{{ $payment->channel?->account_name }}</strong>
                                </div>
                                @if ($payment->unique_code > 0)
                                    <div class="pg-bank-row unique-code-row">
                                        <span>Kode Unik Verifikasi:</span>
                                        <span class="unique-code-tag">+{{ $payment->unique_code }}</span>
                                    </div>
                                @endif
                                <div class="pg-bank-row total-highlight">
                                    <span>Jumlah Transfer Tepat:</span>
                                    <strong>Rp {{ number_format((float) $payment->total_amount, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            <!-- Form Upload Bukti Transfer -->
                            <div class="pg-upload-box">
                                <h3>Unggah Bukti Transfer</h3>
                                @if ($payment->proof_of_payment)
                                    <div class="proof-preview-card">
                                        <span class="proof-status-tag">✓ Bukti Telah Diunggah</span>
                                        <p>File bukti sedang ditinjau oleh Staff. Anda dapat mengunggah ulang jika terdapat kesalahan.</p>
                                        <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" target="_blank" class="btn-view-proof">
                                            🔍 Lihat Bukti yang Terunggah
                                        </a>
                                    </div>
                                @endif

                                @if (!$payment->isPaid())
                                    <form method="POST" action="{{ route('payment.upload-proof', $payment->reference_number) }}" enctype="multipart/form-data" class="upload-proof-form">
                                        @csrf
                                        <label for="proof_file" class="file-dropzone">
                                            <span class="dropzone-icon">📎</span>
                                            <span class="dropzone-text">Pilih file foto bukti struk / transfer (JPG, PNG, PDF maks 5MB)</span>
                                            <input type="file" id="proof_file" name="proof_file" accept="image/jpeg,image/png,image/jpg,application/pdf" required onchange="showFilename(this)">
                                        </label>
                                        <div id="file-chosen-text" class="file-chosen-info"></div>
                                        <button type="submit" class="btn-upload-submit">Kirim Bukti Pembayaran</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    {{-- 5. METODE KASIR TUNAI --}}
                    @elseif ($payment->payment_method === 'cash')
                        <div class="pg-cash-container">
                            <div class="pg-cash-hero">
                                <span class="cash-icon">💵</span>
                                <h2>Pembayaran Kasir / POS Tunai</h2>
                                <p>Silakan lakukan pembayaran langsung ke kasir toko.</p>
                                <div class="cash-amount">Rp {{ number_format((float) $payment->total_amount, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Card: Tombol Invoice jika Lunas -->
                <div class="pg-card-footer">
                    @if ($payment->isPaid())
                        <div class="pg-paid-actions">
                            <span class="pg-paid-congrats">🎉 Pembayaran telah diterima dan terverifikasi secara resmi!</span>
                            <a href="{{ route('payment.invoice', $payment->reference_number) }}" class="btn-invoice-primary" target="_blank">
                                🖨️ Cetak / Unduh Struk Invoice (LUNAS) →
                            </a>
                            <a href="{{ route('storefront.index') }}" class="btn-back-catalog" style="font-weight: 700; color: #176b78; margin-top: 4px;">
                                ← Kembali ke Halaman Utama Customer
                            </a>
                        </div>
                    @else
                        <div class="pg-pending-actions">
                            <button type="button" class="btn-refresh-status" onclick="checkStatusRealtime()">
                                🔄 Cek Status Terkini
                            </button>
                            <a href="{{ route('storefront.index') }}" class="btn-back-catalog">
                                ← Kembali ke Toko
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Pesanan & Panel Simulasi Demo UAS -->
            <div class="pg-sidebar">
                <!-- Rincian Pesanan Singkat -->
                <div class="pg-order-summary-card">
                    <h3 class="pg-summary-title">Rincian Belanja</h3>
                    <div class="pg-summary-customer">
                        <div><strong>Pelanggan:</strong> {{ $payment->sale->customer_name ?: 'Pelanggan Umum' }}</div>
                        <div><strong>Telepon:</strong> {{ $payment->sale->customer_phone ?: '-' }}</div>
                        <div><strong>No. Pesanan:</strong> {{ $payment->sale->sale_number }}</div>
                    </div>
                    <div class="pg-items-list">
                        @foreach ($payment->sale->items as $item)
                            <div class="pg-item-row">
                                <div class="pg-item-name">
                                    <strong>{{ $item->product->name }}</strong>
                                    <span>{{ $item->quantity }} x Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="pg-item-subtotal">
                                    Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pg-cost-breakdown">
                        <div class="pg-breakdown-row">
                            <span>Subtotal Produk</span>
                            <span>Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="pg-breakdown-row">
                            <span>Biaya Layanan Admin</span>
                            <span>Rp {{ number_format((float) $payment->admin_fee, 0, ',', '.') }}</span>
                        </div>
                        @if ($payment->unique_code > 0)
                            <div class="pg-breakdown-row">
                                <span>Kode Unik</span>
                                <span>+Rp {{ number_format((float) $payment->unique_code, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="pg-breakdown-row total-row">
                            <strong>Total Bayar</strong>
                            <strong class="total-price">Rp {{ number_format((float) $payment->total_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- PANEL SPESIAL DEMO UAS -->
                <div class="pg-demo-card">
                    <div class="pg-demo-header">
                        <span class="demo-sparkle">🎓</span>
                        <div>
                            <h4>Simulasi Pembayaran (Mode Demo UAS)</h4>
                            <p>Gunakan tombol di bawah untuk mensimulasikan alur payment gateway di depan dosen tanpa perlu transaksi uang nyata.</p>
                        </div>
                    </div>
                    <div class="pg-demo-body">
                        <!-- Tombol Simulasi 1: Bayar Berhasil -->
                        <form method="POST" action="{{ route('payment.simulate-paid', $payment->reference_number) }}" id="form-simulate-paid" onsubmit="handleSimulatePaid(event)">
                            @csrf
                            <button type="submit" class="btn-demo-simulate-paid" @disabled($payment->isPaid() || $payment->isExpired())>
                                ⚡ Simulasi Bayar Berhasil (Instant Callback)
                            </button>
                        </form>
                        <p class="demo-btn-hint">Status berubah jadi <strong>PAID</strong>, stok resmi terpotong di inventory, dan struk lunas diterbitkan.</p>

                        <!-- Tombol Simulasi 2: Timeout / Expired -->
                        <form method="POST" action="{{ route('payment.simulate-expired', $payment->reference_number) }}" id="form-simulate-expired" onsubmit="handleSimulateExpired(event)">
                            @csrf
                            <button type="submit" class="btn-demo-simulate-expired" @disabled($payment->isPaid() || $payment->isExpired())>
                                ⏳ Simulasi Waktu Habis (Expired)
                            </button>
                        </form>
                        <p class="demo-btn-hint">Status berubah jadi <strong>EXPIRED</strong> dan stok barang yang tertahan otomatis dikembalikan ke gudang.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Overlay Modal Animasi Sukses Pembayaran -->
    <div id="success-modal-overlay" class="modal-overlay hidden">
        <div class="modal-card">
            <div class="modal-success-icon">✓</div>
            <h2>Pembayaran Berhasil!</h2>
            <p>Transaksi telah terverifikasi secara realtime melalui Mock Payment Gateway.</p>
            <div class="modal-details">
                <div>No. Referensi: <strong id="modal-ref">{{ $payment->reference_number }}</strong></div>
                <div>Metode: <strong>{{ $payment->payment_channel }}</strong></div>
                <div>Total: <strong>Rp {{ number_format((float) $payment->total_amount, 0, ',', '.') }}</strong></div>
            </div>
            <div class="modal-actions">
                <a href="{{ route('payment.invoice', $payment->reference_number) }}" class="btn-modal-invoice" target="_blank">
                    🖨️ Cetak / Unduh Struk Lunas
                </a>
                <a href="{{ route('storefront.index') }}" class="btn-modal-home">
                    ← Kembali ke Halaman Utama Customer
                </a>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="pg-toast" class="pg-toast hidden"></div>

    <script>
        // Countdown Timer Logic (24 Jam atau sisa waktu expired_at)
        @if (!$payment->isPaid() && !$payment->isExpired() && $payment->expired_at)
            const expiredTime = new Date("{{ $payment->expired_at->toIso8601String() }}").getTime();
            const timerElem = document.getElementById('countdown-timer');

            function updateTimer() {
                const now = new Date().getTime();
                const distance = expiredTime - now;

                if (distance < 0) {
                    if (timerElem) timerElem.textContent = "00:00:00 (Kedaluwarsa)";
                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const pad = (n) => String(n).padStart(2, '0');
                if (timerElem) {
                    timerElem.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                }
            }
            setInterval(updateTimer, 1000);
            updateTimer();
        @endif

        // Copy Text Helper
        function copyText(text, label) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(`✓ ${label} berhasil disalin ke clipboard!`);
            }).catch(() => {
                const temp = document.createElement('textarea');
                temp.value = text;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
                showToast(`✓ ${label} berhasil disalin!`);
            });
        }

        // Toast Helper
        function showToast(msg) {
            const toast = document.getElementById('pg-toast');
            if (toast) {
                toast.textContent = msg;
                toast.classList.remove('hidden');
                toast.classList.add('visible');
                setTimeout(() => {
                    toast.classList.remove('visible');
                    toast.classList.add('hidden');
                }, 3000);
            }
        }

        // Tabs Switcher
        function switchTab(btn, tabId) {
            document.querySelectorAll('.pg-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.pg-tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            const target = document.getElementById(tabId);
            if (target) target.classList.add('active');
        }

        // File chosen filename display
        function showFilename(input) {
            const display = document.getElementById('file-chosen-text');
            if (input.files && input.files[0]) {
                display.textContent = 'File dipilih: ' + input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
            }
        }

        // Realtime Status Check via AJAX
        async function checkStatusRealtime() {
            try {
                showToast('Memeriksa status pembayaran...');
                const res = await fetch("{{ route('payment.check-status', $payment->reference_number) }}");
                const data = await res.json();

                if (data.is_paid) {
                    showSuccessModal(data);
                } else {
                    showToast('Status saat ini: ' + data.status_label);
                }
            } catch (e) {
                showToast('Gagal memeriksa status terkini.');
            }
        }

        // AJAX Simulasi Bayar Berhasil (UAS Demo)
        async function handleSimulatePaid(e) {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            btn.disabled = true;
            btn.textContent = 'Memproses Callback...';

            try {
                const res = await fetch("{{ route('payment.simulate-paid', $payment->reference_number) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showSuccessModal(data);
                }
            } catch (err) {
                // Fallback to normal form submit
                e.target.submit();
            }
        }

        // AJAX Simulasi Waktu Habis (UAS Demo)
        async function handleSimulateExpired(e) {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            btn.disabled = true;
            btn.textContent = 'Memproses Timeout...';

            try {
                const res = await fetch("{{ route('payment.simulate-expired', $payment->reference_number) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Transkasi Kedaluwarsa! Memuat ulang...');
                    setTimeout(() => window.location.reload(), 1200);
                }
            } catch (err) {
                e.target.submit();
            }
        }

        function showSuccessModal(data) {
            const overlay = document.getElementById('success-modal-overlay');
            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('visible');
            }
            const statusBadge = document.getElementById('payment-status-badge');
            if (statusBadge) {
                statusBadge.className = 'pg-status-badge status-paid';
                statusBadge.textContent = 'Lunas';
            }
        }
    </script>
</body>
</html>
