<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreCareerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        if ($user->hasRole(Role::UNIVERSITY_ADMIN)) {
             // Load the relationship if it's not already loaded
             if (!$user->relationLoaded('adminUniversities')) {
                 $user->load('adminUniversities');
             }
             $university = $this->route('university');
            // Assuming the university ID is available in the route parameters
            return $user->adminUniversities->contains('id', $university->id);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'required'],
            'description' => ['string'],
        ];
    }
}
