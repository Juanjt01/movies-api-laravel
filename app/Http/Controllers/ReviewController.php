<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Movie;

class ReviewController extends Controller
{
    // Listar reviews de una película
    public function index($movieId)
    {
        $reviews = Review::where('movie_id', $movieId)
            ->with('user') // Incluir datos del usuario
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $reviews
        ], 200);
    }

    // Crear una review
    public function store(Request $request, $movieId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review = Review::create([
            'movie_id' => $movieId,
            'user_id' => $request->user()->id, // Usuario autenticado
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null
        ]);

        // Cargar la relación del usuario
        $review->load('user');

        return response()->json([
            'message' => 'Review creada correctamente',
            'data' => $review
        ], 201);
    }
}