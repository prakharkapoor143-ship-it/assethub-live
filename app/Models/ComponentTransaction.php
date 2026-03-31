<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ComponentTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['component_id', 'type', 'quantity', 'counterparty', 'notes', 'transacted_at'];

    protected $casts = ['transacted_at' => 'datetime'];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
