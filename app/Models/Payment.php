<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'user_id',
        'payment_channel_id',
        'reference_number',
        'payment_method',
        'payment_channel',
        'virtual_account_number',
        'qris_payload',
        'amount',
        'admin_fee',
        'unique_code',
        'total_amount',
        'proof_of_payment',
        'status',
        'notes',
        'paid_at',
        'expired_at',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'admin_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'unique_code' => 'integer',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    // Alias for order relationship
    public function order(): BelongsTo
    {
        return $this->sale();
    }

    public function getOrderIdAttribute(): int
    {
        return (int) $this->sale_id;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class, 'payment_channel_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isWaitingVerification(): bool
    {
        return $this->status === 'waiting_verification';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->expired_at && now()->greaterThan($this->expired_at) && ! $this->isPaid());
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Lunas',
            'waiting_verification' => 'Menunggu Verifikasi',
            'pending' => 'Menunggu Pembayaran',
            'expired' => 'Kedaluwarsa',
            'failed' => 'Gagal / Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => '#16a34a', // green
            'waiting_verification' => '#d97706', // amber
            'pending' => '#2563eb', // blue
            'expired' => '#dc2626', // red
            'failed' => '#b91c1c', // dark red
            default => '#64748b',
        };
    }
}
