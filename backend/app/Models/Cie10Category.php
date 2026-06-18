<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cie10Category extends Model
{
    use HasFactory;

    protected $table = 'cie10_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function cie10Codes(): HasMany
    {
        return $this->hasMany(Cie10Code::class, 'category_id');
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
