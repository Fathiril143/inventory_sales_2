<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Penjualan / Kasir POS | Inventory Sales</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 950px; margin: 3rem auto; padding: 0 1rem; color: #172033;">
    <p><a href="{{ route('sales.index') }}">← Kembali ke Daftar Penjualan</a></p>
    <h1>Input Penjualan / Kasir POS</h1>

    @if ($errors->any())
        <div style="padding: .8rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1rem;">
            <strong>Periksa kembali data berikut:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if ($products->isEmpty())
        <p>Tambahkan produk terlebih dahulu sebelum mencatat penjualan.</p>
    @else
        @php($formItems = old('items', [['product_id' => '', 'quantity' => 1]]))
        <form method="POST" action="{{ route('sales.store') }}" id="pos-sale-form">
            @csrf

            <!-- Data Pelanggan -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 1.5rem; padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                <label>Nama Pelanggan (Opsional)<br>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Pelanggan Langsung (Kasir)" style="width: 100%; padding: .65rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </label>
                <label>Nomor HP / WhatsApp (Opsional)<br>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="08xxxxxxxxxx" style="width: 100%; padding: .65rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </label>
            </div>

            <!-- Item Produk -->
            <h3>Daftar Produk yang Dijual</h3>
            <div id="sale-items">
                @foreach ($formItems as $index => $item)
                    <div class="sale-row" style="display: flex; flex-wrap: wrap; gap: .8rem; align-items: end; margin: .8rem 0; padding: .8rem; background: #f1f5f9; border-radius: 8px;">
                        <label style="flex: 2; min-width: 250px;">Produk<br>
                            <select name="items[{{ $index }}][product_id]" class="product-select" required style="padding: .6rem; width: 100%;">
                                <option value="">Pilih produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}" @selected(($item['product_id'] ?? '') == $product->id)>
                                        {{ $product->sku }} — {{ $product->name }} (stok {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label style="width: 100px;">Jumlah<br>
                            <input name="items[{{ $index }}][quantity]" type="number" min="1" max="9999" value="{{ $item['quantity'] ?? 1 }}" required class="product-qty" style="padding: .6rem; width: 100%;">
                        </label>
                        <div class="price-display" style="padding: .6rem; font-weight: 600; color: #176b78; min-width: 180px;">
                            Pilih produk
                        </div>
                        <button type="button" class="remove-row" style="background: #fff; color: #dc2626; border: 1px solid #fecaca; padding: .6rem .9rem; border-radius: 6px; cursor: pointer;">
                            Hapus
                        </button>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-row" style="padding: .6rem 1rem; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; font-weight: 600;">
                + Tambah Baris Produk
            </button>

            <!-- Metode Pembayaran Kasir -->
            <div style="margin-top: 1.8rem; padding: 1.2rem; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                <h3 style="margin-top: 0; color: #1e293b;">Metode Pembayaran Kasir</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
                    <label>Metode Pembayaran<br>
                        <select name="payment_channel" id="pos-payment-channel" required style="width: 100%; padding: .7rem; border-radius: 8px; font-weight: 600;" onchange="toggleCashCalculator(this.value)">
                            @foreach ($paymentChannels as $ch)
                                <option value="{{ $ch->code }}" @selected(old('payment_channel', 'cash') === $ch->code)>
                                    {{ $ch->name }} ({{ $ch->category_label }})
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <!-- Kalkulator Uang Tunai / Kembalian -->
                    <div id="cash-calculator-box">
                        <label>Uang Tunai Diterima (Rp)<br>
                            <input type="number" id="cash-tendered-input" name="cash_tendered" placeholder="0" min="0" style="width: 100%; padding: .7rem; border-radius: 8px;" oninput="calculateChange()">
                        </label>
                    </div>
                </div>

                <!-- Ringkasan Total POS -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.2rem; padding-top: 1rem; border-top: 2px dashed #cbd5e1; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <div style="font-size: .85rem; color: #64748b;">TOTAL TRANSAKSI</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #176b78;" id="pos-total-display">Rp 0</div>
                    </div>
                    <div id="pos-change-display-wrapper" style="text-align: right;">
                        <div style="font-size: .85rem; color: #64748b;">KEMBALIAN</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #16a34a;" id="pos-change-display">Rp 0</div>
                    </div>
                </div>
            </div>

            <p style="color: #64748b; font-size: .88rem; margin-top: 1rem;">
                * Untuk pembayaran <strong>Tunai</strong>, transaksi langsung berstatus Lunas. Untuk non-tunai (QRIS / VA), sistem akan membuka layar payment gateway.
            </p>

            <button type="submit" style="margin-top: .5rem; padding: .85rem 1.8rem; background: #176b78; color: #fff; font-size: 1rem; font-weight: 700; border: none; border-radius: 9px; cursor: pointer;">
                ✓ Proses &amp; Simpan Penjualan
            </button>
        </form>

        <template id="sale-row-template">
            <div class="sale-row" style="display: flex; flex-wrap: wrap; gap: .8rem; align-items: end; margin: .8rem 0; padding: .8rem; background: #f1f5f9; border-radius: 8px;">
                <label style="flex: 2; min-width: 250px;">Produk<br>
                    <select name="items[__INDEX__][product_id]" class="product-select" required style="padding: .6rem; width: 100%;">
                        <option value="">Pilih produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}">{{ $product->sku }} — {{ $product->name }} (stok {{ $product->stock }})</option>
                        @endforeach
                    </select>
                </label>
                <label style="width: 100px;">Jumlah<br>
                    <input name="items[__INDEX__][quantity]" type="number" min="1" max="9999" value="1" required class="product-qty" style="padding: .6rem; width: 100%;">
                </label>
                <div class="price-display" style="padding: .6rem; font-weight: 600; color: #176b78; min-width: 180px;">
                    Pilih produk
                </div>
                <button type="button" class="remove-row" style="background: #fff; color: #dc2626; border: 1px solid #fecaca; padding: .6rem .9rem; border-radius: 6px; cursor: pointer;">
                    Hapus
                </button>
            </div>
        </template>

        <script>
            let nextIndex = {{ count($formItems) }};
            const saleItems = document.getElementById('sale-items');

            function updateRowPrice(row) {
                const select = row.querySelector('.product-select');
                const qtyInput = row.querySelector('.product-qty');
                const opt = select.selectedOptions[0];
                const display = row.querySelector('.price-display');
                const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;

                if (!opt || !opt.dataset.price) {
                    display.textContent = 'Pilih produk';
                    display.dataset.subtotal = "0";
                    calculateGrandTotal();
                    return;
                }

                const unitPrice = parseFloat(opt.dataset.price);
                const subtotal = unitPrice * qty;
                display.dataset.subtotal = String(subtotal);
                display.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);

                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.sale-row').forEach(row => {
                    const disp = row.querySelector('.price-display');
                    if (disp && disp.dataset.subtotal) {
                        grandTotal += parseFloat(disp.dataset.subtotal) || 0;
                    }
                });

                const totalDisplay = document.getElementById('pos-total-display');
                if (totalDisplay) {
                    totalDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
                    totalDisplay.dataset.total = String(grandTotal);
                }

                calculateChange();
            }

            function calculateChange() {
                const totalDisplay = document.getElementById('pos-total-display');
                const total = parseFloat(totalDisplay?.dataset.total || 0);
                const cashInput = document.getElementById('cash-tendered-input');
                const cash = parseFloat(cashInput?.value || 0);
                const changeDisplay = document.getElementById('pos-change-display');

                const change = cash - total;
                if (changeDisplay) {
                    if (change >= 0) {
                        changeDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
                        changeDisplay.style.color = '#16a34a';
                    } else {
                        changeDisplay.textContent = '-Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(change)) + ' (Kurang)';
                        changeDisplay.style.color = '#dc2626';
                    }
                }
            }

            function toggleCashCalculator(channelCode) {
                const box = document.getElementById('cash-calculator-box');
                const wrapper = document.getElementById('pos-change-display-wrapper');
                if (channelCode === 'cash') {
                    if (box) box.style.display = 'block';
                    if (wrapper) wrapper.style.display = 'block';
                } else {
                    if (box) box.style.display = 'none';
                    if (wrapper) wrapper.style.display = 'none';
                }
            }

            document.getElementById('add-row').addEventListener('click', () => {
                const tpl = document.getElementById('sale-row-template').innerHTML.replaceAll('__INDEX__', String(nextIndex++));
                saleItems.insertAdjacentHTML('beforeend', tpl);
            });

            saleItems.addEventListener('change', (e) => {
                if (e.target.matches('.product-select') || e.target.matches('.product-qty')) {
                    updateRowPrice(e.target.closest('.sale-row'));
                }
            });

            saleItems.addEventListener('input', (e) => {
                if (e.target.matches('.product-qty')) {
                    updateRowPrice(e.target.closest('.sale-row'));
                }
            });

            saleItems.addEventListener('click', (e) => {
                if (e.target.matches('.remove-row') && saleItems.querySelectorAll('.sale-row').length > 1) {
                    e.target.closest('.sale-row').remove();
                    calculateGrandTotal();
                }
            });

            saleItems.querySelectorAll('.sale-row').forEach(updateRowPrice);
        </script>
    @endif
</body>
</html>
