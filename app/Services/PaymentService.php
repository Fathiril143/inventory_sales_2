<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Create a payment record for a given sale.
     */
    public function createPaymentForSale(Sale $sale, string $channelCode, array $extra = []): Payment
    {
        $channel = PaymentChannel::where('code', $channelCode)->where('is_active', true)->firstOrFail();

        $refNumber = 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        $adminFee = (float) $channel->admin_fee;
        $uniqueCode = ($channel->category === 'manual_transfer') ? random_int(101, 899) : 0;
        $totalAmount = (float) $sale->total + $adminFee + $uniqueCode;

        $vaNumber = null;
        if ($channel->category === 'va') {
            $prefix = $channel->account_number ?: '8277';
            // 10 digits based on date and random number
            $vaNumber = $prefix . now()->format('ymd') . str_pad((string) random_int(100, 999), 3, '0', STR_PAD_LEFT);
        }

        $qrisPayload = null;
        if ($channel->category === 'qris' || $channel->code === 'qris') {
            $cleanAmount = (int) round($totalAmount);
            $qrisPayload = "00020101021226670016COM.GO-JEK.WWW011893600914300000000002150000000000000000303UMI51440014ID.LINKAJA.WWW011893600914300000000002150000000000000000303UMI520454995303360540{$cleanAmount}5802ID5923INVENTORY SALES UAS6007JAKARTA61051234062070703A016304" . strtoupper(Str::random(4));
        }

        $payment = Payment::create([
            'sale_id' => $sale->id,
            'user_id' => $sale->user_id,
            'payment_channel_id' => $channel->id,
            'reference_number' => $refNumber,
            'payment_method' => $channel->category,
            'payment_channel' => $channel->name,
            'virtual_account_number' => $vaNumber,
            'qris_payload' => $qrisPayload,
            'amount' => $sale->total,
            'admin_fee' => $adminFee,
            'unique_code' => $uniqueCode,
            'total_amount' => $totalAmount,
            'status' => ($channel->category === 'cash' && ($extra['is_cashier'] ?? false)) ? 'paid' : 'pending',
            'paid_at' => ($channel->category === 'cash' && ($extra['is_cashier'] ?? false)) ? now() : null,
            'expired_at' => now()->addHours(24),
            'verified_by' => ($channel->category === 'cash' && ($extra['is_cashier'] ?? false)) ? auth()->id() : null,
            'verified_at' => ($channel->category === 'cash' && ($extra['is_cashier'] ?? false)) ? now() : null,
        ]);

        $sale->update([
            'payment_status' => $payment->status,
            'payment_method' => $channel->category,
            'payment_channel' => $channel->name,
        ]);

        return $payment;
    }

    /**
     * Simulate successful payment (instant callback) for UAS Demo.
     */
    public function simulatePaid(Payment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($lockedPayment->status === 'paid') {
                return true;
            }

            if ($lockedPayment->status === 'expired') {
                throw ValidationException::withMessages([
                    'payment' => 'Pembayaran ini sudah kedaluwarsa dan tidak dapat dilunasi.',
                ]);
            }

            $lockedPayment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'notes' => 'Pembayaran berhasil dikonfirmasi via Simulasi Callback UAS.',
            ]);

            $sale = Sale::whereKey($lockedPayment->sale_id)->lockForUpdate()->firstOrFail();
            $sale->update([
                'payment_status' => 'paid',
                'status' => 'completed',
            ]);

            return true;
        });
    }

    /**
     * Simulate expired payment for UAS Demo (returns stock to inventory).
     */
    public function simulateExpired(Payment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($lockedPayment->status === 'paid') {
                throw ValidationException::withMessages([
                    'payment' => 'Pembayaran yang sudah lunas tidak dapat diubah menjadi kedaluwarsa.',
                ]);
            }

            if ($lockedPayment->status === 'expired') {
                return true;
            }

            $lockedPayment->update([
                'status' => 'expired',
                'notes' => 'Pembayaran kedaluwarsa (Simulasi Timeout UAS). Stok dikembalikan.',
            ]);

            $sale = Sale::whereKey($lockedPayment->sale_id)->lockForUpdate()->firstOrFail();
            
            // Return stock if sale wasn't already cancelled
            if ($sale->status !== 'cancelled') {
                $items = $sale->items()->lockForUpdate()->get();
                foreach ($items as $item) {
                    $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            $sale->update([
                'payment_status' => 'expired',
                'status' => 'cancelled',
            ]);

            return true;
        });
    }

    /**
     * Upload proof of payment for manual bank transfer.
     */
    public function uploadProof(Payment $payment, UploadedFile $file): Payment
    {
        return DB::transaction(function () use ($payment, $file) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($lockedPayment->status === 'paid') {
                throw ValidationException::withMessages([
                    'proof' => 'Pembayaran sudah lunas.',
                ]);
            }

            // Delete old proof if exists
            if ($lockedPayment->proof_of_payment && Storage::disk('public')->exists($lockedPayment->proof_of_payment)) {
                Storage::disk('public')->delete($lockedPayment->proof_of_payment);
            }

            $filename = 'proof-' . $lockedPayment->reference_number . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('proofs', $filename, 'public');

            $lockedPayment->update([
                'proof_of_payment' => $path,
                'status' => 'waiting_verification',
                'notes' => 'Bukti pembayaran diunggah oleh pelanggan pada ' . now()->format('d/m/Y H:i') . '. Menunggu verifikasi Staff.',
            ]);

            $sale = Sale::whereKey($lockedPayment->sale_id)->lockForUpdate()->firstOrFail();
            $sale->update([
                'payment_status' => 'waiting_verification',
            ]);

            return $lockedPayment;
        });
    }

    /**
     * Staff/Admin verifies manual payment proof (Approve or Reject).
     */
    public function verifyProof(Payment $payment, User $verifier, bool $approve, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($payment, $verifier, $approve, $reason) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $sale = Sale::whereKey($lockedPayment->sale_id)->lockForUpdate()->firstOrFail();

            if ($approve) {
                $lockedPayment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'verified_by' => $verifier->id,
                    'verified_at' => now(),
                    'notes' => 'Bukti pembayaran disetujui oleh ' . $verifier->name . ($reason ? ' - Catatan: ' . $reason : ''),
                ]);

                $sale->update([
                    'payment_status' => 'paid',
                    'status' => 'completed',
                ]);
            } else {
                $lockedPayment->update([
                    'status' => 'failed',
                    'verified_by' => $verifier->id,
                    'verified_at' => now(),
                    'notes' => 'Bukti transfer ditolak oleh ' . $verifier->name . '. Alasan: ' . ($reason ?: 'Bukti pembayaran tidak valid / dana tidak masuk.'),
                ]);

                // Restore stock
                if ($sale->status !== 'cancelled') {
                    $items = $sale->items()->lockForUpdate()->get();
                    foreach ($items as $item) {
                        $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                }

                $sale->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
            }

            return true;
        });
    }

    /**
     * Fulfillment status progression by warehouse staff.
     */
    public function updateFulfillment(Sale $sale, User $user, string $targetStatus): bool
    {
        if (! $sale->isPaid()) {
            throw ValidationException::withMessages([
                'fulfillment' => 'Barang tidak dapat diproses karena pembayaran belum lunas.',
            ]);
        }

        $validStatuses = ['processing', 'shipped', 'delivered'];
        if (! in_array($targetStatus, $validStatuses, true)) {
            throw ValidationException::withMessages([
                'fulfillment' => 'Status pengiriman tidak valid.',
            ]);
        }

        $sale->update([
            'fulfillment_status' => $targetStatus,
            'fulfilled_at' => now(),
            'fulfilled_by' => $user->id,
        ]);

        return true;
    }
}
