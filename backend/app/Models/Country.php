<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone_code',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
