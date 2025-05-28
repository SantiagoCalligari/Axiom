<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
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

        // Assuming 'career' route parameter is bound to a Career model instance
        $career = $this->route('career');

        // university_admin can create subjects if they administer the university the career belongs to
        if ($user->hasRole(Role::UNIVERSITY_ADMIN)) {
             // Assuming Career model has university relationship loaded
             if (!$user->relationLoaded('adminUniversities')) {
                 $user->load('adminUniversities');
             }
             if ($user->adminUniversities->contains('id', $career->university_id)) {
                 return true;
             }
        }

        if ($user->hasRole(Role::CAREER_ADMIN)) {
            // Check if the user administers this specific career
            // Assuming user has admin_careers relationship loading Career models
             if (!$user->relationLoaded('adminCareers')) {
                 $user->load('adminCareers');
             }
            return $user->adminCareers->contains('id', $career->id);
        }

        // Keep TEACHER role authorized if they were before, as the request focused on admins.
        // If teachers should also be limited by career, further logic would be needed here.
        if ($user->hasRole(Role::TEACHER)) {
             return true;
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
        // Get the career ID from the route parameters.
        $careerId = $this->route('career');

        // Get the subject ID from the route parameters.
        $subjectId = $this->route('subject');

        return [
            'name' => [
                // Use Rule::unique to add constraints.
                // Validate that the name is unique in the 'subjects' table
                Rule::unique('subjects')
                    // ONLY for subjects where the career_id matches the current careerId from the route.
                    ->where(function ($query) use ($careerId) {
                        return $query->where('career_id', $careerId);
                    })
                    // IMPORTANT: Ignore the subject with the ID we are currently updating.
                    // This prevents the validation from failing if the name hasn't changed,
                    // or if it's unique amongst others in the career but just happens to
                    // be the name of the subject itself.
                    ->ignore($subjectId)
            ],
            'description' => ['string', 'max:512', 'nullable'], // Nullable is good for optional fields in updates
        ];
    }
}
