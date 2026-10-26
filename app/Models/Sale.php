<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'sale_number',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'payment_channel',
        'fulfillment_status',
        'fulfilled_at',
        'fulfilled_by',
        'sold_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'sold_at' => 'datetime',
            'fulfilled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function fulfiller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function canBeFulfilled(): bool
    {
        return $this->isPaid() && $this->status !== 'cancelled' && $this->fulfillment_status !== 'delivered';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'waiting_verification' => 'Menunggu Verifikasi',
            'pending' => 'Belum Dibayar',
            'expired' => 'Kedaluwarsa',
            'failed' => 'Gagal',
            default => ucfirst($this->payment_status ?? 'pending'),
        };
    }

    public function getFulfillmentStatusLabelAttribute(): string
    {
        return match ($this->fulfillment_status) {
            'unfulfilled' => 'Belum Diproses',
            'processing' => 'Sedang Dikemas / Diproses',
            'shipped' => 'Dalam Pengiriman',
            'delivered' => 'Terkirim / Selesai',
            default => ucfirst($this->fulfillment_status ?? 'unfulfilled'),
        };
    }
}
