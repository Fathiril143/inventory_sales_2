<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'account_number',
        'account_name',
        'admin_fee',
        'icon',
        'color',
        'instructions',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'admin_fee' => 'decimal:2',
            'instructions' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'qris' => 'QRIS Instan',
            'va' => 'Virtual Account',
            'ewallet' => 'E-Wallet',
            'manual_transfer' => 'Transfer Bank Manual',
            'cash' => 'Tunai / Kasir POS',
            default => ucfirst($this->category),
        };
    }
}
