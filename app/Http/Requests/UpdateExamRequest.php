<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user)
            return false;
        $exam = $this->route('exam');
        $isUploader = $exam->user_id === $user->id;

        $isAdmin = $user->hasRole(Role::ADMIN);

        return $isUploader || $isAdmin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => ['exists:subjects,id'], // Ensure subject exists
            'file' => ['file', 'mimes:pdf,jpeg,png,jpg', 'max:10240'], // Max 10MB example
            'title' => ['nullable', 'string', 'max:255'],
            'professor_name' => ['nullable', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)], // Year cannot be too far in the future
            'is_resolved' => ['nullable', 'boolean'],
            'exam_date' => ['nullable', 'date'],
        ];
    }
}
