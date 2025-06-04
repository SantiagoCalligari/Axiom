<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'university_admin', 'career_admin', 'subject_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'Debes proporcionar una razón para rechazar el examen.',
            'rejection_reason.min' => 'La razón del rechazo debe tener al menos 10 caracteres.',
            'rejection_reason.max' => 'La razón del rechazo no puede tener más de 500 caracteres.',
        ];
    }
}
