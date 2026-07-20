<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'sale_number',
        'order_id',
        'source',
        'customer_name',
        'total',
        'payment_method',
        'amount_paid',
        'change',
        'status',
        'created_by',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'online'  => 'Online',
            'walk-in' => 'Walk-in',
            default   => ucfirst($this->source),
        };
    }
}
