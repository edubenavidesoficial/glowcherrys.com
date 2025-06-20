<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
     use HasFactory;

    protected $fillable = [
        'name',
        'image_path',
        'price',
        'diamonds',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    protected $appends = ['image_url'];

    /**
     * URL completa de la imagen del regalo
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/gifts/' . $this->image_path);
    }

    /**
     * Formatear el precio para visualización
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Scope para regalos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
