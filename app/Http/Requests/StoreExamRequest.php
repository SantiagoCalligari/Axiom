<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:10240'], // Max 10MB example
            'title' => ['nullable', 'string', 'max:255'],
            'professor_name' => ['nullable', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)], // Year cannot be too far in the future
            'is_resolved' => ['nullable', 'boolean'],
            'exam_type' => ['required', 'string', Rule::in(['midterm', 'final', 'retake'])],
            'exam_date' => ['nullable', 'date'],
        ];
    }
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Ensure exam_type is lowercase for consistency (optional)
        if ($this->has('exam_type') && is_string($this->exam_type)) {
            $this->merge([
                'exam_type' => strtolower($this->exam_type),
            ]);
        }
    }
}
