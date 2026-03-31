<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Component extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model_number',
        'sku',
        'location_id',
        'quantity',
        'allocated',
        'min_quantity',
        'notes',
    ];

    protected $appends = ['available_quantity'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ComponentTransaction::class)->latest('transacted_at');
    }

    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->allocated);
    }
}
