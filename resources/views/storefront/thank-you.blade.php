<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Berhasil | Inventory &amp; Sales</title>
</head>
<body class="storefront-page">
    <header class="storefront-header">
        <div class="storefront-brand-wrapper">
            <a class="storefront-brand" href="{{ route('storefront.index') }}">Inventory <span>&amp;</span> Sales</a>
            @if (auth()->check())
                @if (auth()->user()->role === 'customer')
                    <span class="badge-role-guest">🟢 Akun: Customer</span>
                @else
                    <span class="badge-role-guest">🟢 Pratinjau Toko ({{ ucfirst(auth()->user()->role) }})</span>
                @endif
            @else
                <span class="badge-role-guest">🟢 Akun: Customer (Tanpa Login)</span>
            @endif
        </div>
        <nav class="storefront-nav">
            <a class="nav-login-link" href="{{ route('storefront.index') }}">← Katalog Produk</a>
            @if (auth()->check())
                @if (in_array(auth()->user()->role, ['admin', 'staff']))
                    <a class="nav-login-link" href="{{ route('dashboard') }}">Dashboard →</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display:inline; margin:0;">
                    @csrf
                    <button type="submit" class="nav-logout-btn">Keluar</button>
                </form>
            @endif
        </nav>
    </header>
    <main class="storefront-container">
        <section class="order-success-card">
            <div class="success-mark" aria-hidden="true">✓</div><span class="eyebrow">PESANAN TERCATAT</span>
            <h1>Terima kasih!</h1>
            <p>Pesananmu berhasil disimpan. Catat nomor pesanan ini untuk ditunjukkan kepada petugas toko.</p>
            <div class="order-number">{{ $orderNumber }}</div>
            <p class="payment-note">Pembayaran dilakukan langsung dengan toko. Halaman ini tidak memproses pembayaran online.</p>
            <div class="success-actions"><button type="button" onclick="window.print()">Cetak bukti</button><a href="{{ route('storefront.index') }}">Kembali ke katalog</a></div>
        </section>
    </main>
    <footer class="storefront-footer">Inventory &amp; Sales · Pesanan pelanggan</footer>
</body>
</html>