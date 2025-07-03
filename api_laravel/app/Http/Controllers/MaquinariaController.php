<?php

namespace App\Http\Controllers;

use App\Models\Maquinaria;
use Illuminate\Http\Request;

class MaquinariaController extends Controller
{
    /* public function index()
    {
        return Maquinaria::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'id_categoria' => 'required|integer',
            'id_subcategoria' => 'required|integer',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'ano' => 'nullable|integer',
            'precio' => 'nullable|numeric',
            'descripcion' => 'nullable|string',
            'disponible' => 'boolean',
        ]);

        $maquinaria = Maquinaria::create($data);

        return response()->json($maquinaria, 201);
    }

    public function show($id)
    {
        return Maquinaria::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $maquinaria = Maquinaria::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'id_categoria' => 'required|integer',
            'id_subcategoria' => 'required|integer',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'ano' => 'nullable|integer',
            'precio' => 'nullable|numeric',
            'descripcion' => 'nullable|string',
            'disponible' => 'boolean',
        ]);

        $maquinaria->update($data);

        return response()->json($maquinaria);
    }

    public function destroy($id)
    {
        Maquinaria::destroy($id);
        return response()->json(null, 204);
    } */



public function index(Request $request)
{
    $search = $request->input('search'); // texto a buscar
    $disponible = $request->input('disponible'); // array: [0, 1]
    $perPage = $request->input('limit', 10); // por defecto 10
    $page = $request->input('page', 1);

    $query = Maquinaria::query();

    // Filtro de búsqueda por nombre
    if ($search) {
        $query->where('nombre', 'like', "%$search%");
    }

    // Filtro por disponibilidad (si viene como array)
    if (is_array($disponible)) {
        $query->whereIn('disponible', $disponible);
    }

    // Obtener resultados paginados
    $maquinaria = $query->paginate($perPage, ['*'], 'page', $page);

    // Respuesta con formato que espera el frontend
    return response()->json([
        'success' => true,
        'maquinaria' => $maquinaria
    ]);
}


    public function store(Request $request)
    {
        $request->validate([
    'nombre' => 'required|string|max:100',
            'id_categoria' => 'required|integer',
            'id_subcategoria' => 'required|integer',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'ano' => 'nullable|integer',
            'precio' => 'nullable|numeric',
            'descripcion' => 'nullable|string',
            'disponible' => 'boolean',
]);


        $maquinaria = Maquinaria::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $maquinaria
        ], 201);
    }

    public function show($id)
    {
        $maquinaria = Maquinaria::find($id);

        if (!$maquinaria) {
            return response()->json(['success' => false, 'message' => 'Maquinaria no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $maquinaria
        ]);
    }

    public function update(Request $request, $id)
    {
        $maquinaria = Maquinaria::find($id);

        if (!$maquinaria) {
            return response()->json(['success' => false, 'message' => 'Maquinaria no encontrada'], 404);
        }

    $request->validate([
    'nombre' => 'required|string|max:100',
            'id_categoria' => 'required|integer',
            'id_subcategoria' => 'required|integer',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'ano' => 'nullable|integer',
            'precio' => 'nullable|numeric',
            'descripcion' => 'nullable|string',
            'disponible' => 'boolean',
]);


        $maquinaria->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $maquinaria
        ]);
    }

    public function destroy($id)
    {
        $maquinaria = Maquinaria::find($id);

        if (!$maquinaria) {
            return response()->json(['success' => false, 'message' => 'Maquinaria no encontrada'], 404);
        }

        $maquinaria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maquinaria eliminada correctamente'
        ]);
    }
}
