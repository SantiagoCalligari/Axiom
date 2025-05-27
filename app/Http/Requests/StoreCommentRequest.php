<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|min:1|max:10000',
            'parent_id' => [
                'nullable',
                'exists:comments,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $parentComment = \App\Models\Comment::find($value);
                        if ($parentComment && $parentComment->comment_type !== $this->input('comment_type')) {
                            $fail('No se puede responder a un comentario de ' . 
                                ($parentComment->comment_type === 'exam' ? 'examen' : 'resolución') . 
                                ' con un comentario de ' . 
                                ($this->input('comment_type') === 'exam' ? 'examen' : 'resolución'));
                        }
                    }
                }
            ],
            'attachments.*' => 'nullable|file|max:10240', // Máximo 10MB por archivo
            'comment_type' => 'required|in:exam,resolution',
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
            'comment_type.required' => 'El tipo de comentario es requerido',
            'comment_type.in' => 'El tipo de comentario debe ser exam o resolution',
        ];
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('Debes estar autenticado para crear un comentario.');
    }
}
