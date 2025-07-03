<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

use App\Http\Controllers\MaquinariaController;



/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::prefix('v1')->group(function() {
  // Auth Routes
  Route::post('auth/login', [AuthController::class, 'login']);
  Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
  Route::post('auth/register', [AuthController::class, 'register']);
  Route::get('auth/abilities', [AuthController::class, 'abilities'])->middleware('auth:sanctum');
  Route::get('auth/user-profile', [AuthController::class, 'userProfile'])->middleware('auth:sanctum');

  // Users Routes
  Route::get('users', [UserController::class, 'index'])->middleware(['auth:sanctum', 'permission:users:view']);
  Route::post('users', [UserController::class, 'store'])->middleware(['auth:sanctum', 'permission:users:create']);
  Route::put('users/{id}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'permission:users:edit']);
  Route::delete('users/{id}', [UserController::class, 'destroy'])->middleware(['auth:sanctum', 'permission:users:delete']);

  // Roles Routes
  Route::get('roles', [RoleController::class, 'index'])->middleware(['auth:sanctum', 'permission:roles:view']);
  Route::post('roles', [RoleController::class, 'store'])->middleware(['auth:sanctum', 'permission:roles:create']);
  Route::put('roles/{id}', [RoleController::class, 'update'])->middleware(['auth:sanctum', 'permission:roles:edit']);
  Route::delete('roles/{id}', [RoleController::class, 'destroy'])->middleware(['auth:sanctum', 'permission:roles:delete']);

  // Permissions Routes
  Route::get('permissions', [PermissionController::class, 'index'])->middleware(['auth:sanctum', 'permission:permissions:view']);
  Route::post('permissions', [PermissionController::class, 'store'])->middleware(['auth:sanctum', 'permission:permissions:create']);
  Route::put('permissions/{id}', [PermissionController::class, 'update'])->middleware(['auth:sanctum', 'permission:permissions:edit']);
  Route::delete('permissions/{id}', [PermissionController::class, 'destroy'])->middleware(['auth:sanctum', 'permission:permissions:delete']);

  // Maquinaria Routes
    Route::get('maquinaria', [MaquinariaController::class, 'index'])->middleware(['auth:sanctum', 'permission:maquinaria:view']);
    Route::post('maquinaria', [MaquinariaController::class, 'store'])->middleware(['auth:sanctum', 'permission:maquinaria:create']);
/*     Route::get('maquinaria/{id}', [MaquinariaController::class, 'show'])->middleware(['auth:sanctum', 'permission:maquinaria:view']);
 */    Route::put('maquinaria/{id}', [MaquinariaController::class, 'update'])->middleware(['auth:sanctum', 'permission:maquinaria:edit']);
    Route::delete('maquinaria/{id}', [MaquinariaController::class, 'destroy'])->middleware(['auth:sanctum', 'permission:maquinaria:delete']); 


/*  GET, POST, PUT, DELETE, */
/* Route::apiResource('maquinaria', MaquinariaController::class); */ 


});


