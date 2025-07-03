<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use App\Models\Categoria;
use Illuminate\Http\Request;

class SubcategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $subcategorias = Subcategoria::with(['categoria', 'maquinarias'])
                            ->orderBy('nombre')
                            ->get();
            
            return response()->json([
                'success' => true,
                'data' => $subcategorias
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las subcategorías: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener subcategorías por categoría
     */
    public function porCategoria($categoriaId)
    {
        try {
            $subcategorias = Subcategoria::withCount('maquinarias')
                            ->where('id_categoria', $categoriaId)
                            ->orderBy('nombre')
                            ->get();
            
            return response()->json([
                'success' => true,
                'data' => $subcategorias
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las subcategorías por categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_categoria' => 'required|exists:categorias,id',
                'nombre' => 'required|string|max:100|unique:subcategorias,nombre,NULL,id,id_categoria,'.$request->id_categoria
            ]);

            $subcategoria = Subcategoria::create($validated);

            return response()->json([
                'success' => true,
                'data' => $subcategoria,
                'message' => 'Subcategoría creada correctamente'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Error de validación'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la subcategoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $subcategoria = Subcategoria::with(['categoria', 'maquinarias'])
                            ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $subcategoria
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subcategoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la subcategoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $subcategoria = Subcategoria::findOrFail($id);
            
            $validated = $request->validate([
                'id_categoria' => 'sometimes|exists:categorias,id',
                'nombre' => 'required|string|max:100|unique:subcategorias,nombre,'.$id.',id,id_categoria,'.($request->id_categoria ?? $subcategoria->id_categoria)
            ]);

            // Verificar si hay máquinas asociadas antes de cambiar de categoría
            if (isset($validated['id_categoria']) && $subcategoria->maquinarias()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cambiar la categoría porque hay máquinas asociadas a esta subcategoría'
                ], 400);
            }

            $subcategoria->update($validated);

            return response()->json([
                'success' => true,
                'data' => $subcategoria,
                'message' => 'Subcategoría actualizada correctamente'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Error de validación'
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subcategoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la subcategoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $subcategoria = Subcategoria::findOrFail($id);
            
            // Verificar si tiene máquinas asociadas
            if ($subcategoria->maquinarias()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la subcategoría porque tiene máquinas asociadas'
                ], 400);
            }
            
            $subcategoria->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Subcategoría eliminada correctamente'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subcategoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la subcategoría: ' . $e->getMessage()
            ], 500);
        }
    }
}