<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    /// UserRoleController.php
    public function index()
    {
        return User::with('roles')->get(); // Devuelve usuarios CON sus roles
    }

    // Listar todos los roles disponibles
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    // Asignar roles a un usuario
    // Asignar roles a un usuario
    public function assignRoles(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);
    
        $user = User::findOrFail($request->user_id);
    
        // Usamos sync() para reemplazar los roles del usuario
        $user->roles()->sync($request->roles);
    
        return response()->json([
            'message' => 'Roles actualizados correctamente',
            'user' => $user->load('roles'),
        ]);
    }
    
}
