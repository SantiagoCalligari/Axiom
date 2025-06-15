<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = request()->user()->id;
        return [
            'email' => ['email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['min:8', 'confirmed'],
            'name' => ['required', 'min:3'],
            'display_name' => ['required', 'min:3', 'max:255'],
        ];
    }
}
