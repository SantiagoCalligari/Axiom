<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResolutionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole(['professor', 'admin']);
    }

    public function rules()
    {
        $rules = [
            'file' => 'required|file|mimes:pdf|max:10240',
        ];

        // Si es una actualización, el archivo es opcional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['file'] = 'nullable|file|mimes:pdf|max:10240';
        }

        return $rules;
    }
} 