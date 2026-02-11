<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'title',
        'description',
        'poster',
        'is_published',
        'user_id'
    ];
    
    protected $casts = [
        'is_published' => 'boolean',
    ];

    // Relación con reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}