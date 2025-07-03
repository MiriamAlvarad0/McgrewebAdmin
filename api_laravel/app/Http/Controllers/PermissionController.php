<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{

    public function index(Request $request)
    {
        $limit = ($request->input('limit') && $request->input('limit') <= 1000) ? $request->input('limit') : 1000;  // Limite de records por pagina
        $sortBy = $request->input('column') ? $request->input('column') : 'id'; // Columna por la cual sera sorteada la tabla
        $orderBy = $request->input('dir') ? $request->input('dir') : 'asc';// Orden ascendente o descentente
        $searchValue = $request->input('search'); // Campos de busqueda
    
        $roles = Permission::orderBy($sortBy, $orderBy)
        ->where(function($query) use ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%');
        })
        ->select('id', 'name')
        ->paginate($limit);

        return response()->json(compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required'
        ]);
        
        $permission = Permission::create([
            'name' => $request->name
        ]);

        $data = array(
            'success'  => '',
            'code'    => 201,
            'message' =>  'Permission added',
            'user' => $permission
        );

        return response()->json($data, $data['code']);
    }

    public function show(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required'
        ]);
        
        $permission = Permission::find($id);
        
        if(!$permission){
            throw ValidationException::withMessages([
                'role' => ['Record not found']
            ]);
        }

        $permission->update([
            'name' => $request->name
        ]);

        $data = array(
            'success'  => '',
            'code'    => 200,
            'message' =>  'Role Modified',
            'user' => $permission
        );   
        return response()->json($data, $data['code']);
    }

    public function destroy(string $id)
    {
        $permission = Permission::find($id);
        
        if(!$permission){
            throw ValidationException ::withMessages([
                'permission' => ['Record not found']
            ]);
        }

        $role = Role::permission($permission->name)->get();

        if($role->count() > 0){
            throw ValidationException ::withMessages([
                'relationship' => ['The permission cannot be deleted because it has related roles']
            ]);
        }
    
        $permission->delete();
        
        $data = array(
            'success'  => '',
            'code'    => 200,
            'message' =>  'Permission Deleted',
            'user' => $permission
        );         
        return response()->json($data, $data['code']);
    }
}