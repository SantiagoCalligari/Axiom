<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\University;
use App\Models\Career;

class UserController extends Controller
{
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
        $user->load(['roles', 'subjects.career.university']);
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
