<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'name_en',
    ];

    public function getNameAttribute($value)
    {
        $locale = app()->getLocale();

        if ($locale === 'en' && ! empty($this->attributes['name_en'])) {
            return $this->attributes['name_en'];
        }

        if ($locale === 'ar' && ! empty($this->attributes['name_ar'])) {
            return $this->attributes['name_ar'];
        }

        if (! empty($value)) {
            return $value;
        }

        return $this->attributes['name_ar']
            ?? $this->attributes['name_en']
            ?? $value;
    }

    public function getRawNameAttribute(): ?string
    {
        return $this->attributes['name'] ?? null;
    }
}
