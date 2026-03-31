<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LicenseRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_key',
        'seats_total',
        'seats_used',
        'expires_at',
        'company_id',
        'supplier_id',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    protected $appends = ['seats_available'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getSeatsAvailableAttribute(): int
    {
        return max(0, $this->seats_total - $this->seats_used);
    }
}
