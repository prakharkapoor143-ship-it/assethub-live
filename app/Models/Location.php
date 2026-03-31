<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'notes'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function accessories(): HasMany
    {
        return $this->hasMany(Accessory::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function consumables(): HasMany
    {
        return $this->hasMany(Consumable::class);
    }
}
