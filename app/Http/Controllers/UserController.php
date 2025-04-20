<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        $user = $request->user;
        $user->update($request->validated());
        return new UserResource($user);
    }

    public function show(): UserResource
    {
        return new UserResource(Auth::user());
    }
}
