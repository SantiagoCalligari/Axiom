<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    // Mapa de jerarquía
    private $hierarchy = [
        'admin' => 4,
        'university_admin' => 3,
        'career_admin' => 2,
        'subject_admin' => 1,
        'teacher' => 0,
        'user' => 0,
    ];

    // Asignar un rol a un usuario
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);
        $role = $request->input('role');
        $currentUser = Auth::user();
        $currentHighest = $this->getHighestRole($currentUser);
        $targetLevel = $this->hierarchy[$role] ?? -1;

        // Solo puede asignar roles inferiores
        if ($currentHighest > $targetLevel) {
            $user->syncRoles([$role]);
            return response()->json(['message' => 'Rol asignado correctamente']);
        }
        return response()->json(['message' => 'No tienes permiso para asignar este rol'], 403);
    }

    // Remover un rol a un usuario
    public function removeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);
        $role = $request->input('role');
        $currentUser = Auth::user();
        $currentHighest = $this->getHighestRole($currentUser);
        $targetLevel = $this->hierarchy[$role] ?? -1;

        // Solo puede remover roles inferiores
        if ($currentHighest > $targetLevel) {
            $user->removeRole($role);
            return response()->json(['message' => 'Rol removido correctamente']);
        }
        return response()->json(['message' => 'No tienes permiso para remover este rol'], 403);
    }

    // Obtener el nivel más alto de un usuario
    private function getHighestRole($user)
    {
        $max = 0;
        foreach ($user->roles as $role) {
            $level = $this->hierarchy[$role->name] ?? 0;
            if ($level > $max) {
                $max = $level;
            }
        }
        return $max;
    }
} 