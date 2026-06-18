<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'key',
        'value',
        'setting_group',
        'type',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('setting_group', $group);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    public static function getValue(string $key, mixed $default = null, ?int $institutionId = null): mixed
    {
        $query = static::where('key', $key);

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        $setting = $query->first();

        return $setting?->value ?? $default;
    }

    public static function setValue(string $key, mixed $value, ?int $institutionId = null): static
    {
        return static::updateOrCreate(
            [
                'key' => $key,
                'institution_id' => $institutionId,
            ],
            ['value' => $value]
        );
    }
}
