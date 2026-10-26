<?php

namespace App\Http\Controllers;

use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Sale;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): View
    {
        $filters = array_merge([
            'date_from' => null,
            'date_to' => null,
            'status' => null,
            'payment_status' => null,
            'payment_method' => null,
        ], $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:completed,cancelled,pending'],
            'payment_status' => ['nullable', 'in:paid,waiting_verification,pending,expired,failed'],
            'payment_method' => ['nullable', 'string'],
        ]));

        if (! empty($filters['date_from']) && ! empty($filters['date_to']) && $filters['date_from'] > $filters['date_to']) {
            return back()->withErrors(['date_to' => 'Tanggal akhir harus sama atau setelah tanggal awal.'])->withInput();
        }

        $query = Sale::with(['user', 'payment.channel', 'fulfiller'])->withCount('items');

        if (! empty($filters['date_from'])) $query->whereDate('sold_at', '>=', $filters['date_from']);
        if (! empty($filters['date_to'])) $query->whereDate('sold_at', '<=', $filters['date_to']);
        if (! empty($filters['status'])) $query->where('status', $filters['status']);
        if (! empty($filters['payment_status'])) $query->where('payment_status', $filters['payment_status']);
        if (! empty($filters['payment_method'])) $query->where('payment_method', $filters['payment_method']);

        $sales = $query->latest('sold_at')->paginate(12)->withQueryString();
        $paymentChannels = PaymentChannel::orderBy('sort_order')->get();

        return view('sales.index', compact('sales', 'filters', 'paymentChannels'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get(['id', 'sku', 'name', 'stock', 'selling_price']);
        $paymentChannels = PaymentChannel::active()->orderBy('sort_order')->get();

        return view('sales.create', compact('products', 'paymentChannels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'payment_channel' => ['required', 'string', 'exists:payment_channels,code'],
            'cash_tendered' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $items = collect($validated['items'])->sortBy('product_id');
        $channelCode = $validated['payment_channel'];

        $result = DB::transaction(function () use ($items, $validated, $channelCode, $request) {
            $isCash = ($channelCode === 'cash');

            $sale = Sale::create([
                'user_id' => $request->user()->id,
                'customer_name' => $validated['customer_name'] ?: 'Pelanggan Langsung (Kasir)',
                'customer_phone' => $validated['customer_phone'],
                'sale_number' => 'PJ-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'total' => 0,
                'status' => $isCash ? 'completed' : 'pending',
                'payment_status' => $isCash ? 'paid' : 'pending',
                'fulfillment_status' => $isCash ? 'delivered' : 'unfulfilled',
                'fulfilled_at' => $isCash ? now() : null,
                'fulfilled_by' => $isCash ? $request->user()->id : null,
                'sold_at' => now(),
            ]);

            $totalCents = 0;

            foreach ($items as $item) {
                $product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                $quantity = (int) $item['quantity'];

                if ($quantity < 1 || $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup. Stok tersedia: {$product->stock}.",
                    ]);
                }

                $unitPriceCents = (int) round((float) $product->selling_price * 100);
                $subtotalCents = $unitPriceCents * $quantity;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => number_format($unitPriceCents / 100, 2, '.', ''),
                    'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
                ]);

                $product->decrement('stock', $quantity);
                $totalCents += $subtotalCents;
            }

            $totalFormatted = number_format($totalCents / 100, 2, '.', '');
            $sale->update(['total' => $totalFormatted]);

            $payment = $this->paymentService->createPaymentForSale($sale, $channelCode, [
                'is_cashier' => true,
            ]);

            return ['sale' => $sale, 'payment' => $payment];
        });

        $sale = $result['sale'];
        $payment = $result['payment'];

        if ($channelCode === 'cash') {
            return redirect()
                ->route('sales.show', $sale)
                ->with('success', "Penjualan {$sale->sale_number} berhasil dicatat dan lunas via Tunai (Kasir).");
        }

        // If non-cash selected in POS, redirect to payment gateway screen
        return redirect()->route('payment.show', $payment->reference_number)
            ->with('info', "Tagihan pembayaran {$payment->reference_number} telah dibuat. Silakan scan atau selesaikan pembayaran.");
    }

    public function show(Sale $sale): View
    {
        $sale->load(['user', 'items.product', 'payment.channel', 'payment.verifier', 'fulfiller']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Staff/Admin verifies payment proof (Approve/Reject).
     */
    public function verifyPayment(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $payment = $sale->payment;
        if (! $payment) {
            return back()->withErrors(['error' => 'Data pembayaran tidak ditemukan.']);
        }

        $approve = ($validated['action'] === 'approve');
        $this->paymentService->verifyProof($payment, $request->user(), $approve, $validated['notes'] ?? null);

        $msg = $approve
            ? 'Pembayaran berhasil diverifikasi! Status berubah menjadi LUNAS dan barang siap diproses.'
            : 'Pembayaran ditolak. Transaksi dibatalkan dan stok dikembalikan ke gudang.';

        return redirect()->route('sales.show', $sale)->with('success', $msg);
    }

    /**
     * Warehouse Staff progresses fulfillment (processing, shipped, delivered).
     */
    public function updateFulfillment(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'fulfillment_status' => ['required', 'in:processing,shipped,delivered'],
        ]);

        $this->paymentService->updateFulfillment($sale, $request->user(), $validated['fulfillment_status']);

        $statusLabels = [
            'processing' => 'Sedang Diproses / Dikemas di Gudang',
            'shipped' => 'Dalam Pengiriman / Diserahkan ke Kurir',
            'delivered' => 'Terkirim / Selesai Diterima Pelanggan',
        ];

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Status pengiriman berhasil diperbarui: ' . $statusLabels[$validated['fulfillment_status']]);
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        DB::transaction(function () use ($sale) {
            $lockedSale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

            if ($lockedSale->status === 'cancelled') {
                throw ValidationException::withMessages(['sale' => 'Transaksi ini sudah dibatalkan sebelumnya.']);
            }

            $items = $lockedSale->items()->orderBy('product_id')->get();

            foreach ($items as $item) {
                $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                $product->increment('stock', $item->quantity);
            }

            $lockedSale->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);

            if ($lockedSale->payment) {
                $lockedSale->payment->update(['status' => 'failed']);
            }
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan dibatalkan dan stok dikembalikan ke inventaris.');
    }
}
