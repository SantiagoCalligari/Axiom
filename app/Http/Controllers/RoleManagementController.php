<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\University;
use App\Models\Career;
use App\Models\Subject;
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
            'university_id' => 'required_if:role,university_admin|exists:universities,id',
            'career_id' => 'required_if:role,career_admin|exists:careers,id',
            'subject_id' => 'required_if:role,subject_admin|exists:subjects,id',
        ]);

        $role = $request->input('role');
        $currentUser = Auth::user();
        $currentHighest = $this->getHighestRole($currentUser);
        $targetLevel = $this->hierarchy[$role] ?? -1;

        // Solo puede asignar roles inferiores
        if ($currentHighest > $targetLevel) {
            $user->syncRoles([$role]);

            // Asignar entidades específicas según el rol y suscribir automáticamente
            switch ($role) {
                case 'university_admin':
                    $university = University::findOrFail($request->input('university_id'));
                    $user->adminUniversities()->syncWithoutDetaching([$university->id]);
                    // Suscribir automáticamente a la universidad
                    $user->subscribedUniversities()->syncWithoutDetaching([$university->id]);
                    break;

                case 'career_admin':
                    $career = Career::findOrFail($request->input('career_id'));
                    $user->adminCareers()->syncWithoutDetaching([$career->id]);
                    // Suscribir automáticamente a la carrera y su universidad
                    $user->subscribedCareers()->syncWithoutDetaching([$career->id]);
                    $user->subscribedUniversities()->syncWithoutDetaching([$career->university_id]);
                    // Asignar automáticamente como admin de todas las materias de la carrera
                    $user->adminSubjects()->syncWithoutDetaching($career->subjects->pluck('id'));
                    // Suscribir automáticamente a todas las materias de la carrera
                    $user->subscribedSubjects()->syncWithoutDetaching($career->subjects->pluck('id'));
                    break;

                case 'subject_admin':
                    $subject = Subject::findOrFail($request->input('subject_id'));
                    $user->adminSubjects()->syncWithoutDetaching([$subject->id]);
                    // Suscribir automáticamente a la materia, su carrera y universidad
                    $user->subscribedSubjects()->syncWithoutDetaching([$subject->id]);
                    $user->subscribedCareers()->syncWithoutDetaching([$subject->career_id]);
                    $user->subscribedUniversities()->syncWithoutDetaching([$subject->career->university_id]);
                    break;
            }

            return response()->json(['message' => 'Rol asignado correctamente']);
        }
        return response()->json(['message' => 'No tienes permiso para asignar este rol'], 403);
    }

    // Remover un rol a un usuario
    public function removeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
            'university_id' => 'required_if:role,university_admin|exists:universities,id',
            'career_id' => 'required_if:role,career_admin|exists:careers,id',
            'subject_id' => 'required_if:role,subject_admin|exists:subjects,id',
        ]);

        $role = $request->input('role');
        $currentUser = Auth::user();
        $currentHighest = $this->getHighestRole($currentUser);
        $targetLevel = $this->hierarchy[$role] ?? -1;

        // Solo puede remover roles inferiores
        if ($currentHighest > $targetLevel) {
            // Remover entidades específicas según el rol
            switch ($role) {
                case 'university_admin':
                    $university = University::findOrFail($request->input('university_id'));
                    $user->adminUniversities()->detach($university->id);
                    break;

                case 'career_admin':
                    $career = Career::findOrFail($request->input('career_id'));
                    $user->adminCareers()->detach($career->id);
                    // Remover automáticamente como admin de todas las materias de la carrera
                    $user->adminSubjects()->detach($career->subjects->pluck('id'));
                    break;

                case 'subject_admin':
                    $subject = Subject::findOrFail($request->input('subject_id'));
                    $user->adminSubjects()->detach($subject->id);
                    break;
            }

            $user->removeRole($role);
            return response()->json(['message' => 'Rol removido correctamente']);
        }
        return response()->json(['message' => 'No tienes permiso para remover este rol'], 403);
    }

    // Devuelve los roles que el usuario autenticado puede asignar
    public function assignableRoles()
    {
        $currentUser = Auth::user();
        $currentHighest = $this->getHighestRole($currentUser);
        $roles = collect(array_keys($this->hierarchy))
            ->filter(fn($role) => $this->hierarchy[$role] < $currentHighest)
            ->values();
        return response()->json(['roles' => $roles]);
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