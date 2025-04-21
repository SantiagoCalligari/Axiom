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
        $user = request()->user();
        return $user->hasRole(Role::ADMIN) or $user->hasRole(Role::TEACHER);
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
