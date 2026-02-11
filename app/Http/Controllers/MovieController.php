<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
class MovieController extends Controller
{
     public function index()
    {
        // Listar películas
        $movies = Movie::all();
        return response()->json([
            'data'=> $movies
        ],200);
    }

    public function store(Request $request)
    {
        // Crear película
        // Validación de datos
    $validated = $request->validate([
        'title' => 'required|string|max:100',
        'description' => 'nullable|string',
        'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $validated ['is_published']=false;

    //  Manejo del poster 
    if ($request->hasFile('poster')) {
        $path = $request->file('poster')->store('posters', 'public');
        $validated['poster'] = $path;
    }

    //  Crear la película
    $validated['user_id'] = $request->user()->id;
    $movie = Movie::create($validated);

    //  Respuesta JSON
    return response()->json([
        'message' => 'Película creada correctamente',
        'data' => $movie
    ], 201);
    }

    public function show($id)
    {
        // Mostrar una película
        $movie = Movie::find($id);

        if (!$movie) {
        return response()->json([
            'message' => 'Película no encontrada'
        ], 404);
        }

        return response()->json([
        'data' => $movie
        ], 200);
    }

    public function update(Request $request, Movie $movie)
    {
    // 1. Validar los campos
    $validated = $request->validate([
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'poster' => 'sometimes|file|image|max:2048',
        'is_published' => 'sometimes',
    ]);

    // 2. Convertir is_published a boolean
    if ($request->has('is_published')) {
        $validated['is_published'] = filter_var($request->input('is_published'), FILTER_VALIDATE_BOOLEAN);
    }

    // 3. Manejo del poster
    if ($request->hasFile('poster')) {
        $path = $request->file('poster')->store('posters', 'public');
        $validated['poster'] = $path;
    }

    // 4. Actualizar la película
    $movie->update($validated);

    // 5. Refrescar modelo para asegurar que JSON tenga los valores nuevos
    $movie->refresh();

    // 6. Devolver respuesta JSON
    return response()->json([
        'message' => 'Película actualizada correctamente',
        'data' => $movie
    ], 200);
    }

    public function destroy(Movie $movie)
    {
        // Eliminar película
        $movie->delete();

        return response()->json([
            'message' => 'Película eliminada correctamente (soft delete)',
        ], 200);
    }
    public function restore($id)
    {
    $movie = Movie::onlyTrashed()->find($id);

    if (!$movie) {
        return response()->json([
            'message' => 'Película no encontrada o no eliminada'
        ], 404);
    }

    $movie->restore(); // limpia deleted_at

    return response()->json([
        'message' => 'Película restaurada correctamente',
        'data' => $movie
    ], 200);
    }
}
