<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Produk | Inventory &amp; Sales</title>
</head>
<body class="storefront-page">
    <header class="storefront-header">
        <a class="storefront-brand" href="{{ route('storefront.index') }}">Inventory <span>&amp;</span> Sales</a>
        <nav><a href="{{ route('login') }}">Masuk Admin / Staff</a></nav>
    </header>
    <main class="storefront-container">
        <section class="storefront-hero">
            <span class="eyebrow">KATALOG PRODUK</span>
            <h1>Belanja kebutuhanmu<br> dengan mudah.</h1>
            <p>Lihat produk yang tersedia dan buat pesanan tanpa perlu membuat akun.</p>
            <form class="storefront-search" method="GET" action="{{ route('storefront.index') }}">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau kode produk" aria-label="Cari produk">
                <button type="submit">Cari produk</button>
                @if (!empty($filters['q']))
                    <a class="text-link" href="{{ route('storefront.index') }}">Hapus pencarian</a>
                @endif
            </form>
        </section>
        <section class="storefront-section">
            <div class="section-heading">
                <div><span class="eyebrow">PILIHAN TOKO</span><h2>Produk tersedia</h2></div>
                <a class="storefront-order-link" href="{{ route('storefront.create') }}">Buat pesanan <span>→</span></a>
            </div>
            @if ($products->isEmpty())
                <div class="storefront-empty"><h3>Produk belum ditemukan</h3><p>Coba kata pencarian lain, atau lihat kembali nanti.</p></div>
            @else
                <div class="storefront-grid">
                    @foreach ($products as $product)
                        <article class="storefront-product">
                            <div class="product-art" aria-hidden="true"><span>{{ strtoupper(substr($product->name, 0, 1)) }}</span></div>
                            <div class="product-card-content">
                                <div class="product-meta"><span>{{ $product->category->name }}</span><span>Stok {{ $product->stock }}</span></div>
                                <h3>{{ $product->name }}</h3>
                                <p class="product-sku">Kode {{ $product->sku }}</p>
                                @if ($product->description)<p class="product-description">{{ $product->description }}</p>@endif
                                <div class="product-card-footer">
                                    <strong>Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}</strong>
                                    <a href="{{ route('storefront.create') }}" aria-label="Pesan {{ $product->name }}">Pesan</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="storefront-pagination">{{ $products->links() }}</div>
            @endif
        </section>
        <section class="storefront-note">
            <div><span class="eyebrow">TANPA AKUN</span><h2>Pesan langsung dari katalog</h2><p>Isi nama, nomor kontak, produk, dan jumlah. Sistem akan memeriksa ketersediaan stok sebelum menyimpan pesanan.</p></div>
            <a href="{{ route('storefront.create') }}">Mulai pesan <span>→</span></a>
        </section>
    </main>
    <footer class="storefront-footer">Inventory &amp; Sales · Katalog pelanggan</footer>
</body>
</html>