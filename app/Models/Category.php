<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'notes'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
