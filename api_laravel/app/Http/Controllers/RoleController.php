<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{

    public function index(Request $request)
    {
        $limit = ($request->input('limit') && $request->input('limit') <= 1000) ? $request->input('limit') : 1000;  // Limite de records por pagina
        $sortBy = $request->input('column') ? $request->input('column') : 'id'; // Columna por la cual sera sorteada la tabla
        $orderBy = $request->input('dir') ? $request->input('dir') : 'asc';// Orden ascendente o descentente
        $searchValue = $request->input('search'); // Campos de busqueda

        $roles = Role::orderBy($sortBy, $orderBy)
        ->where(function($query) use ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%');
        })
        ->select('id', 'name')
        ->paginate($limit);

        // Agregar los nombres de los permisos a cada role
        $roles->getCollection()->transform(function ($role) {
            $role->permission = $role->permissions->pluck('id');
            $role->permissionName = $role->permissions->pluck('name');
            unset($role->permissions);
            return $role;
        });

        return response()->json(compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required'
        ]);
        
        $role = Role::create([
            'name' => $request->name
        ]);

        $role->givePermissionTo($request->permissions);

        $data = array(
            'success'  => '',
            'code'    => 201,
            'message' =>  'Role added',
            'user' => $role
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
        
        $role = Role::find($id);
        
        if(!$role){
            throw ValidationException::withMessages([
                'role' => ['Record not found']
            ]);
        }

        $role->update([
            'name' => $request->name
        ]);
        $role->syncPermissions($request->permissions);
        $data = array(
            'success'  => '',
            'code'    => 200,
            'message' =>  'Role Modified',
            'user' => $role
        );   
        return response()->json($data, $data['code']);
    }

    public function destroy(string $id)
    {
        $role = Role::find($id);
        
        if(!$role){
            throw ValidationException::withMessages([
                'role' => ['Record not found']
            ]);
        }
        $user = User::role($role->name)->get();

        if($user->count() > 0){
            throw ValidationException::withMessages([
                'relationship' => ['The role cannot be deleted because it has related users']
            ]);
        }

        $role->delete();
        
        $data = array(
            'success'  => '',
            'code'    => 200,
            'message' =>  'Role Deleted',
            'user' => $role
        );         
        return response()->json($data, $data['code']);
    }
}