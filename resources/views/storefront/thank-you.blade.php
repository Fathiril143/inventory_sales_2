<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Berhasil | Inventory &amp; Sales</title>
</head>
<body class="storefront-page">
    <header class="storefront-header"><a class="storefront-brand" href="{{ route('storefront.index') }}">Inventory <span>&amp;</span> Sales</a></header>
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