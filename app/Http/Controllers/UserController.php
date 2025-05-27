<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\University;
use App\Models\Career;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): UserResourceCollection
    {
        $users = User::query()->with('roles');

        // Filtrar por nombre
        if ($request->has('search') && !empty($request->query('search'))) {
            $searchTerm = $request->query('search');
            $users->where(function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Filtrar por rol
        if ($request->has('role') && !empty($request->query('role'))) {
            $role = $request->query('role');
            $users->whereHas('roles', function($query) use ($role) {
                $query->where('name', $role);
            });
        }

        // Paginación
        $limit = $request->query('limit', 10);
        $users->limit($limit);

        return new UserResourceCollection($users->get());
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $user = User::query()->create($request->validated());
        $user->assignRole(Role::USER);
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request): UserResource
    {
        $user = $request->user();
        $user->update($request->validated());
        return new UserResource($user);
    }

    public function show(): UserResource
    {
        $user = Auth::user();
        $user->load('roles');

        // Cargar relaciones según el rol más relevante
        if ($user->hasRole('university_admin')) {
            $user->load('adminUniversities');
        } elseif ($user->hasRole('career_admin')) {
            $user->load('adminCareers');
        } elseif ($user->hasRole('subject_admin')) {
            $user->load('adminSubjects');
        }

        return new UserResource($user);
    }

    public function assignTeacherRole(User $user)
    {
        if (!$this->authorize('modify user roles')) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
        }
        $user->assignRole('teacher');
        return new UserResource($user);
    }

    public function assignRole(Request $request, User $user)
    {
        if (!$this->authorize('modify user roles')) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción'], 403);
        }

        $role = $request->input('role');
        $entityId = null;

        // Determinar el ID de la entidad según el rol
        switch ($role) {
            case 'university_admin':
                $entityId = $request->input('university_id');
                if (!$entityId) {
                    return response()->json(['message' => 'Se requiere el ID de la universidad'], 400);
                }
                $user->assignRole('university_admin');
                $user->adminUniversities()->attach($entityId);
                break;

            case 'career_admin':
                $entityId = $request->input('career_id');
                if (!$entityId) {
                    return response()->json(['message' => 'Se requiere el ID de la carrera'], 400);
                }
                $user->assignRole('career_admin');
                $user->adminCareers()->attach($entityId);
                break;

            case 'subject_admin':
                $entityId = $request->input('subject_id');
                if (!$entityId) {
                    return response()->json(['message' => 'Se requiere el ID de la materia'], 400);
                }
                $user->assignRole('subject_admin');
                $user->adminSubjects()->attach($entityId);
                break;

            default:
                return response()->json(['message' => 'Rol no válido'], 400);
        }

        return new UserResource($user);
    }

    public function subscribeToSubject(University $university, Career $career, Subject $subject)
    {
        $user = Auth::user();
        if ($user->subjects()->where('subject_id', $subject->id)->exists()) {
            return response()->json(['message' => 'Ya estás suscrito a esta materia'], 400);
        }
        $user->subjects()->attach($subject->id);
        $user->load(['roles', 'subjects.career.university']);
        return new UserResource($user);
    }

    public function unsubscribeFromSubject(University $university, Career $career, Subject $subject)
    {
        $user = Auth::user();
        $user->subjects()->detach($subject->id);
        return new UserResource($user);
    }
}
