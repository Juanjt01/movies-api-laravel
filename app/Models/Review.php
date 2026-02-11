<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'movie_id',
        'user_id',
        'rating',
        'comment'
    ];

    // Relación con Movie
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}