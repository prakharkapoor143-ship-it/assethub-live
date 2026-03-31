<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_name', 'email', 'phone', 'notes'];

    public function licenses(): HasMany
    {
        return $this->hasMany(LicenseRecord::class);
    }
}
