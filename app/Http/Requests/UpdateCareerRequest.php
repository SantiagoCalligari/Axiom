<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCareerRequest extends FormRequest
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

        $career = $this->route('career'); // Assuming 'career' is the route parameter name

        if ($user->hasRole(Role::UNIVERSITY_ADMIN)) {
            // Load the relationship if it's not already loaded
            if (!$user->relationLoaded('adminUniversities')) {
                $user->load('adminUniversities');
            }
            // Check if the user administers the university associated with this career
            return $user->adminUniversities->contains('id', $career->university_id); // Assuming career has university_id
        }

        if ($user->hasRole(Role::CAREER_ADMIN)) {
            // Load the relationship if it's not already loaded
            if (!$user->relationLoaded('adminCareers')) {
                $user->load('adminCareers');
            }
            // Check if the user administers this specific career
            return $user->adminCareers->contains('id', $career->id); // Assuming user has admin_careers relationship
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
            'name' => ['string'],
            'description' => ['string'],
        ];
    }
}
