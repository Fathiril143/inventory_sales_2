<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Pesanan | Inventory &amp; Sales</title>
</head>
<body class="storefront-page">
    <header class="storefront-header">
        <a class="storefront-brand" href="{{ route('storefront.index') }}">Inventory <span>&amp;</span> Sales</a>
        <nav><a href="{{ route('login') }}">Masuk Admin / Staff</a></nav>
    </header>
    <main class="storefront-container storefront-order-container">
        <a class="back-link" href="{{ route('storefront.index') }}">← Kembali ke katalog</a>
        <section class="storefront-order-intro">
            <span class="eyebrow">PESAN TANPA AKUN</span>
            <h1>Data pesanan</h1>
            <p>Isi kontak dan pilih produk. Harga dan stok akan diperiksa oleh sistem.</p>
        </section>
        @if ($errors->any())
            <div class="storefront-alert" role="alert"><strong>Pesanan belum dapat diproses.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @if ($products->isEmpty())
            <div class="storefront-empty"><h3>Belum ada produk tersedia</h3><p>Silakan kembali lagi nanti.</p></div>
        @else
            @php($formItems = old('items', [['product_id' => '', 'quantity' => 1]]))
            <form method="POST" action="{{ route('storefront.store') }}" class="customer-order-form">
                @csrf
                <section class="order-panel">
                    <h2>Kontak pelanggan</h2>
                    <div class="customer-fields">
                        <label>Nama<input name="customer_name" type="text" value="{{ old('customer_name') }}" maxlength="120" required autocomplete="name" placeholder="Nama kamu"></label>
                        <label>Nomor HP / WhatsApp<input name="customer_phone" type="tel" value="{{ old('customer_phone') }}" maxlength="30" required autocomplete="tel" placeholder="Contoh: 08xx atau +62"></label>
                    </div>
                </section>
                <section class="order-panel">
                    <div class="section-heading"><div><h2>Produk yang dipesan</h2><p>Pilih satu atau lebih produk. Produk yang sama cukup dimasukkan sekali.</p></div></div>
                    <div id="customer-order-items">
                        @foreach ($formItems as $index => $item)
                            <div class="customer-order-row">
                                <label>Produk<select name="items[{{ $index }}][product_id]" required class="customer-product-select"><option value="">Pilih produk</option>@foreach ($products as $product)<option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}" @selected(($item['product_id'] ?? '') == $product->id)>{{ $product->name }} · {{ $product->sku }} · stok {{ $product->stock }}</option>@endforeach</select></label>
                                <label>Jumlah<input name="items[{{ $index }}][quantity]" type="number" min="1" max="9999" value="{{ $item['quantity'] ?? 1 }}" required></label>
                                <span class="customer-price-display">Pilih produk untuk melihat harga</span>
                                <button type="button" class="remove-order-row">Hapus</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-customer-order-row" class="secondary-button">+ Tambah produk</button>
                </section>
                <div class="order-submit-row"><p>Pesanan mengurangi stok setelah berhasil disimpan. Pembayaran dilakukan langsung dengan toko.</p><button type="submit">Kirim pesanan</button></div>
            </form>
            <template id="customer-order-row-template">
                <div class="customer-order-row">
                    <label>Produk<select name="items[__INDEX__][product_id]" required class="customer-product-select"><option value="">Pilih produk</option>@foreach ($products as $product)<option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}">{{ $product->name }} · {{ $product->sku }} · stok {{ $product->stock }}</option>@endforeach</select></label>
                    <label>Jumlah<input name="items[__INDEX__][quantity]" type="number" min="1" max="9999" value="1" required></label>
                    <span class="customer-price-display">Pilih produk untuk melihat harga</span><button type="button" class="remove-order-row">Hapus</button>
                </div>
            </template>
            <script>
                let nextOrderItemIndex = {{ count($formItems) }};
                const orderItemList = document.getElementById('customer-order-items');
                function updateCustomerPrice(row) {
                    const select = row.querySelector('.customer-product-select');
                    const option = select.selectedOptions[0];
                    const display = row.querySelector('.customer-price-display');
                    if (!option || !option.dataset.price) {
                        display.textContent = 'Pilih produk untuk melihat harga';
                        return;
                    }
                    const price = new Intl.NumberFormat('id-ID').format(Number(option.dataset.price));
                    display.textContent = 'Harga: Rp ' + price + ' · stok ' + option.dataset.stock;
                }
                orderItemList.querySelectorAll('.customer-order-row').forEach(updateCustomerPrice);
                document.getElementById('add-customer-order-row').addEventListener('click', () => {
                    const rowHtml = document.getElementById('customer-order-row-template').innerHTML.replaceAll('__INDEX__', String(nextOrderItemIndex++));
                    orderItemList.insertAdjacentHTML('beforeend', rowHtml);
                });
                orderItemList.addEventListener('change', (event) => {
                    if (event.target.matches('.customer-product-select')) updateCustomerPrice(event.target.closest('.customer-order-row'));
                });
                orderItemList.addEventListener('click', (event) => {
                    if (event.target.matches('.remove-order-row') && orderItemList.querySelectorAll('.customer-order-row').length > 1) event.target.closest('.customer-order-row').remove();
                });
            </script>
        @endif
    </main>
    <footer class="storefront-footer">Inventory &amp; Sales · Pesanan pelanggan</footer>
</body>
</html>