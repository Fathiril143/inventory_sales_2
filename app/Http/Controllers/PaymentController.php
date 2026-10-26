<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display modern payment gateway checkout screen.
     */
    public function show(string $reference): View
    {
        $payment = Payment::with(['sale.items.product', 'channel'])
            ->where('reference_number', $reference)
            ->firstOrFail();

        return view('payment.checkout', compact('payment'));
    }

    /**
     * Handle manual transfer proof upload.
     */
    public function uploadProof(Request $request, string $reference): RedirectResponse
    {
        $payment = Payment::where('reference_number', $reference)->firstOrFail();

        $request->validate([
            'proof_file' => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ], [
            'proof_file.required' => 'Silakan pilih file bukti transfer Anda.',
            'proof_file.mimes' => 'Format bukti pembayaran harus berupa file JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran file bukti pembayaran maksimal 5 MB.',
        ]);

        $this->paymentService->uploadProof($payment, $request->file('proof_file'));

        return redirect()->route('payment.show', $reference)
            ->with('success', 'Bukti pembayaran berhasil diunggah! Staff kami akan segera memverifikasi pesanan Anda.');
    }

    /**
     * UAS Demo: Instant Simulate Paid.
     */
    public function simulatePaid(Request $request, string $reference): JsonResponse|RedirectResponse
    {
        $payment = Payment::where('reference_number', $reference)->firstOrFail();

        $this->paymentService->simulatePaid($payment);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => 'paid',
                'message' => 'Simulasi Callback Pembayaran Berhasil! Status berubah menjadi LUNAS (Paid).',
                'invoice_url' => route('payment.invoice', $reference),
            ]);
        }

        return redirect()->route('payment.show', $reference)
            ->with('simulated_success', 'Simulasi Pembayaran Berhasil! Tagihan telah lunas dan stok inventory terkonfirmasi.');
    }

    /**
     * UAS Demo: Simulate Expired.
     */
    public function simulateExpired(Request $request, string $reference): JsonResponse|RedirectResponse
    {
        $payment = Payment::where('reference_number', $reference)->firstOrFail();

        $this->paymentService->simulateExpired($payment);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => 'expired',
                'message' => 'Simulasi Waktu Habis! Status transaksi kedaluwarsa dan stok dikembalikan ke gudang.',
            ]);
        }

        return redirect()->route('payment.show', $reference)
            ->with('simulated_expired', 'Simulasi Selesai: Pembayaran kedaluwarsa dan stok produk telah dikembalikan ke inventaris toko.');
    }

    /**
     * Live status check polling.
     */
    public function checkStatus(string $reference): JsonResponse
    {
        $payment = Payment::where('reference_number', $reference)->firstOrFail();

        return response()->json([
            'reference_number' => $payment->reference_number,
            'status' => $payment->status,
            'is_paid' => $payment->isPaid(),
            'status_label' => $payment->status_label,
            'status_color' => $payment->status_color,
            'paid_at' => $payment->paid_at?->format('d/m/Y H:i:s'),
            'invoice_url' => route('payment.invoice', $reference),
        ]);
    }

    /**
     * Display printable/downloadable official invoice.
     */
    public function invoice(string $reference): View
    {
        $payment = Payment::with(['sale.items.product', 'sale.user', 'channel'])
            ->where('reference_number', $reference)
            ->firstOrFail();

        return view('payment.invoice', compact('payment'));
    }
}
