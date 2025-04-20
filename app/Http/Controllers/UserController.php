<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    public function store(StoreUserRequest $request): UserResource
    {
        $user = User::query()->create($request->validated());
        $user->assignRole(Role::USER);
        return new UserResource($user);
    }
}
