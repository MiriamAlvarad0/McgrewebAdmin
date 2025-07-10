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

        $query = Maquinaria::query()
            ->whereDoesntHave('subasta') // maquinaria que NO tenga subasta
            ->whereDoesntHave('proveedores'); // maquinaria que NO tenga proveedor

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
    $validated = $request->validate([
        'nombre' => 'required|string|max:100',
        'id_categoria' => 'required|integer',
        'id_subcategoria' => 'required|integer',
        'marca' => 'nullable|string|max:100',
        'modelo' => 'nullable|string|max:100',
        'ano' => 'nullable|integer',
        'precio' => 'nullable|numeric',
        'descripcion' => 'nullable|string',
        'disponible' => 'boolean',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

    ]);

    if ($request->hasFile('imagen')) {
        // Guarda la imagen en storage/app/public/maquinaria
        $path = $request->file('imagen')->store('maquinaria', 'public');
        // Guarda la ruta relativa en el arreglo validado
        $validated['imagen'] = $path;
    }

    // Crea la maquinaria con los datos validados, incluida la ruta de la imagen
    $maquinaria = Maquinaria::create($validated);

    return response()->json(['success' => true, 'data' => $maquinaria], 201);
}


    public function show($id)
    {
        $maquinaria = Maquinaria::find($id);

        if (!$maquinaria) {
            return response()->json(['success' => false, 'message' => 'Maquinaria no encontrada'], 404);
        }

        // Si quieres, puedes modificar la respuesta para agregar la URL completa de la imagen:
    $maquinaria->imagen_url = $maquinaria->imagen ? url('storage/'.$maquinaria->imagen) : null;

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

    // Guardar validación en variable
    $validated = $request->validate([
        'nombre' => 'required|string|max:100',
        'id_categoria' => 'required|integer',
        'id_subcategoria' => 'required|integer',
        'marca' => 'nullable|string|max:100',
        'modelo' => 'nullable|string|max:100',
        'ano' => 'nullable|integer',
        'precio' => 'nullable|numeric',
        'descripcion' => 'nullable|string',
        'disponible' => 'boolean',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('imagen')) {
        // Primero elimina la imagen vieja si existe
        if ($maquinaria->imagen && \Storage::disk('public')->exists($maquinaria->imagen)) {
            \Storage::disk('public')->delete($maquinaria->imagen);
        }
        // Guarda la nueva imagen
        $path = $request->file('imagen')->store('maquinaria', 'public');
        $validated['imagen'] = $path;
    }

    $maquinaria->update($validated);

    return response()->json(['success' => true, 'data' => $maquinaria]);
}


    public function destroy($id)
    {
        $maquinaria = Maquinaria::find($id);

        if (!$maquinaria) {
            return response()->json(['success' => false, 'message' => 'Maquinaria no encontrada'], 404);
        }

        // Eliminar imagen del disco
        if ($maquinaria->imagen && \Storage::disk('public')->exists($maquinaria->imagen)) {
            \Storage::disk('public')->delete($maquinaria->imagen);
        }

        $maquinaria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maquinaria eliminada correctamente'
        ]);
    }

    



}
