<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $limit = ($request->input('limit') && $request->input('limit') <= 1000) ? $request->input('limit') : 1000;  // Limite de records por pagina
        $sortBy = $request->input('column') ? $request->input('column') : 'id'; // Columna por la cual sera sorteada la tabla
        $orderBy = $request->input('dir') ? $request->input('dir') : 'asc';// Orden ascendente o descentente
        $searchValue = $request->input('search'); // Campos de busqueda
        $active = $request->input('active')  ? $request->input('active')  : [false, true];

        $users = User::orderBy($sortBy, $orderBy)
            ->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('email', 'like', '%' . $searchValue . '%');
            })
            ->whereIn('active', $active)
            ->select('id', 'name', 'email', 'active', 'external_id')
            ->paginate($limit);

        // Agregar los nombres de los roles a cada usuario
        $users->getCollection()->transform(function ($user) {
            $user->role = $user->roles->pluck('name')->first();
            unset($user->roles);
            return $user;
        });

        return response()->json(compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users',
            'role'  => 'required'
        ]);
        
        $roleName = $request->role ? $request->role : 'guest';
        $active = $request->active ? $request->active : 0;
        $role = Role::where('name', '=', $roleName)->first();
        if($role){
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make('Password1#'),
                'active' => $active
            ])->assignRole($roleName);

        }else{
            throw ValidationException ::withMessages([
                'role' => ['El role que intenta asignar no existe']
            ]);
        }

        $data = array(
            'success'  => '',
            'code'    => 201,
            'message' =>  'User added',
            'user' => $user
        );

        return response()->json($data, $data['code']);
    }

    public function show(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,'.$id,
            'role'  => 'required',
        ]);
        
        $user = User::find($id);
        
        if($user){
            $roleName = $request->role ? $request->role : 'guest';
            $active = $request->active ? $request->active : 0;
            $role = Role::where('name', '=', $roleName)->first();
            if($role){
                $user->update([
                    'email' => $request->email,
                    'active' => $active
                ]);

                $user->syncRoles($roleName);

            }else{
                throw ValidationException ::withMessages([
                    'role' => ['El role que intenta asignar no existe']
                ]);
            }
            $data = array(
                'success'  => '',
                'code'    => 200,
                'message' =>  'User Modified',
                'user' => $user
            );
        }else{
            $data = array(
                'success'  => 'error',
                'code'    => '404',
                'message' => 'Record not found'
            );
        }
        
        return response()->json($data, $data['code']);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        
        if($user){
            $user->delete();
            
            $data = array(
                'success'  => '',
                'code'    => 200,
                'message' =>  'User Deleted',
                'user' => $user
            );
        }else{
            $data = array(
                'success'  => 'error',
                'code'    => '404',
                'message' => 'Record not found'
            );
        }
        
        return response()->json($data, $data['code']);
    }
}