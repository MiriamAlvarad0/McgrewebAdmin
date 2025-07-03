<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);


        $user = User::where('email', '=', $request->email)->first();


        if(isset($user)){
            if(Hash::check($request->password, $user->password)){
                /* Get associated tokens by the user */
                $UserHasTokens = User::where('email', '=', $request->email)->with('tokens')->first();


                /* Remove 'user_auth_token' tokens from the current user */
                if($UserHasTokens->tokens){
                    foreach($UserHasTokens->tokens as $token){
                        if($token->name === 'user_auth_token'){
                            $UserHasTokens->tokens()->where('id', '=', $token->id)->delete();
                        }
                    }
                }
                /* Get permission associated with the user role */
                $abilities = $this->rolePermissions($request->email);
                
                /* Create the new access token 'user_auth_token */
                $token = $user->createToken("user_auth_token", $abilities)->plainTextToken;
                
                /* Get the uset with the new token access */
                $user = User::where('email', '=', $request->email)->first();
                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'user' => $user,
                    'access_token' => $token
                );
            }else{
                throw ValidationException::withMessages([
                    'password' => ['La contraseña es incorrecta']
                ]);
            }
        }else{
            throw ValidationException::withMessages([
                'email' => ['El correo electrónico no existe']
            ]);
        }
        return response()->json($data, $data['code']);
    }

    public function register(Request $request) {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);
        
        $roleName = $request->role ? $request->role : 'guest';
        $role = Role::where('name', '=', $roleName)->first();
        if($role){
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password)
            ])->assignRole($roleName);


        }else{
            throw ValidationException::withMessages([
                'role' => ['El role que intenta asignar no existe']
            ]);
        }
        /* Get permission associated with the user role */
        $abilities = $this->rolePermissions($request->email);
                
        /* Create the new access token 'user_auth_token */
        $token = $user->createToken("user_auth_token", $abilities)->plainTextToken;


        $data = array(
            'success'  => '',
            'code'    => 201,
            'message' =>  'User added',
            'user' => $user,
            'access_token' => $token
        );


        return response()->json($data, $data['code']);
    }

    private function rolePermissions($email){
        $user = User::where('email', '=' , $email)->first();
        $role = Role::where('name', '=', $user->roles[0]->name)->first();
        if(isset($role)){
            foreach($role->permissions as $permisssion){
                $result[] = $permisssion->name;
            }
        }else{
            $result[] = 'guest';
        }
        return $result;
    }

    public function abilities(Request $request){
        $data = auth()->user()->tokens[0]->abilities;
        return response()->json($data, 200);
    }

public function userProfile(){
    $data = array(
        'succes'=>'',
        'status'=>'200',
        'message'=>'User Data',
        'user' => auth()->user()
    );
    return response()->json($data, $data['status']);
}

public function logout(){
    $UserhasTokens = auth()->user()->with('tokens')->first();
    if($UserhasTokens->tokens){
        foreach($UserhasTokens->tokens as $token){
            if($token -> name === "user_auth_token"){
                $UserhasTokens->tokens()->where('id',$token->id)->delete();
            }
        }
    }

    $data = array(
        'succes'=>'',
        'status'=>'200',
        'message'=>'Logout',
        'user' => auth()->user()
    );
    return response()->json($data, $data['status']);

}


}

