<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
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

        // Assuming 'subject' route parameter is bound to a Subject model instance
        $subject = $this->route('subject');

        // university_admin can modify subjects if they administer the university the subject's career belongs to
        if ($user->hasRole(Role::UNIVERSITY_ADMIN)) {
             // Assuming Subject model has career relationship loaded, and Career has university relationship loaded
             if (!$user->relationLoaded('adminUniversities')) {
                 $user->load('adminUniversities');
             }
             // Assuming subject->career relationship is loaded or accessible and has university_id or university relationship
             if ($user->adminUniversities->contains('id', $subject->career->university_id)) {
                 return true;
             }
        }

        if ($user->hasRole(Role::CAREER_ADMIN)) {
            // Check if the user administers the career the subject belongs to
            // Assuming Subject model has career_id and User has admin_careers relationship
             if (!$user->relationLoaded('adminCareers')) {
                 $user->load('adminCareers');
             }
            if ($user->adminCareers->contains('id', $subject->career_id)) {
                return true;
            }
        }

        if ($user->hasRole(Role::SUBJECT_ADMIN)) {
            // Check if the user administers this specific subject
            // Assuming User has admin_subjects relationship
             if (!$user->relationLoaded('adminSubjects')) {
                 $user->load('adminSubjects');
             }
            if ($user->adminSubjects->contains('id', $subject->id)) {
                return true;
            }
        }

        if ($user->hasRole(Role::TEACHER)) {
             // Check if the user teaches this specific subject
             // Assuming User has teachesSubjects relationship
             if (!$user->relationLoaded('teachesSubjects')) {
                 $user->load('teachesSubjects');
             }
             if ($user->teachesSubjects->contains('id', $subject->id)) {
                 return true;
             }
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

            //
        ];
    }
}
