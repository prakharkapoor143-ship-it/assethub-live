<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AccessoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'accessory_id',
        'type',
        'quantity',
        'counterparty',
        'notes',
        'transacted_at',
    ];

    protected $casts = [
        'transacted_at' => 'datetime',
    ];

    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class);
    }
}
