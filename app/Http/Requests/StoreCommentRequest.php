<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|min:1|max:10000',
            'parent_id' => 'nullable|exists:comments,id',
            'attachments.*' => 'nullable|file|max:10240', // Máximo 10MB por archivo
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'El contenido del comentario es requerido',
            'content.min' => 'El comentario debe tener al menos 1 carácter',
            'content.max' => 'El comentario no puede tener más de 10000 caracteres',
            'parent_id.exists' => 'El comentario padre no existe',
            'attachments.*.file' => 'Los archivos adjuntos deben ser archivos válidos',
            'attachments.*.max' => 'Los archivos adjuntos no pueden ser mayores a 10MB',
        ];
    }
}
