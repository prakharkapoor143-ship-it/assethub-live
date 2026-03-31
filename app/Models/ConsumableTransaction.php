<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ConsumableTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['consumable_id', 'type', 'quantity', 'counterparty', 'notes', 'transacted_at'];

    protected $casts = ['transacted_at' => 'datetime'];

    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }
}
