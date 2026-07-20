<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    protected $fillable = [
        'opname_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'opname_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class, 'opname_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
