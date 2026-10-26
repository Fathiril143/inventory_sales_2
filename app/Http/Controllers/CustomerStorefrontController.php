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

class CustomerStorefrontController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->when($filters['q'] ?? null, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('storefront.index', compact('products', 'filters'));
    }

    public function create(): View
    {
        $products = Product::where('stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'sku', 'name', 'stock', 'selling_price']);

        $paymentChannels = PaymentChannel::active()
            ->where('category', '!=', 'cash')
            ->orderBy('sort_order')
            ->get();

        $groupedChannels = $paymentChannels->groupBy('category');

        return view('storefront.create', compact('products', 'paymentChannels', 'groupedChannels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+() .-]+$/'],
            'payment_channel' => ['required', 'string', 'exists:payment_channels,code'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ], [
            'customer_phone.regex' => 'Nomor kontak hanya boleh berisi angka, spasi, dan tanda + - ( ).',
            'payment_channel.required' => 'Pilih salah satu metode pembayaran yang diinginkan.',
            'payment_channel.exists' => 'Metode pembayaran yang dipilih tidak tersedia.',
        ]);

        $items = collect($validated['items'])->sortBy('product_id');

        $payment = DB::transaction(function () use ($items, $validated) {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'sale_number' => 'PJ-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'total' => 0,
                'status' => 'pending',
                'payment_status' => 'pending',
                'fulfillment_status' => 'unfulfilled',
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

                // Hold stock for this transaction
                $product->decrement('stock', $quantity);
                $totalCents += $subtotalCents;
            }

            $sale->update(['total' => number_format($totalCents / 100, 2, '.', '')]);

            // Create Payment record via PaymentService
            $payment = $this->paymentService->createPaymentForSale($sale, $validated['payment_channel']);

            return $payment;
        });

        // Redirect directly to the modern payment gateway checkout
        return redirect()->route('payment.show', $payment->reference_number)
            ->with('info', 'Pesanan Anda berhasil dibuat! Silakan selesaikan pembayaran sesuai instruksi di bawah ini.');
    }

    public function thankYou(Request $request): View|RedirectResponse
    {
        $orderNumber = $request->session()->get('orderNumber');

        if (! $orderNumber) {
            return redirect()->route('storefront.index');
        }

        return view('storefront.thank-you', compact('orderNumber'));
    }
}