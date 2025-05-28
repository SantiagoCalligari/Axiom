<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
        ];

        // Agregar la relación administrativa cargada si existe
        if ($this->relationLoaded('adminUniversities')) {
            $data['admin_universities'] = $this->adminUniversities->map(function ($university) {
                return [
                    'id' => $university->id,
                    'name' => $university->name,
                    'slug' => $university->slug,
                    'description' => $university->description
                ];
            });
        } elseif ($this->relationLoaded('adminCareers')) {
            $data['admin_careers'] = $this->adminCareers->map(function ($career) {
                return [
                    'id' => $career->id,
                    'name' => $career->name,
                    'slug' => $career->slug,
                    'description' => $career->description,
                    'university' => [
                        'id' => $career->university->id,
                        'name' => $career->university->name,
                        'slug' => $career->university->slug
                    ]
                ];
            });
        } elseif ($this->relationLoaded('adminSubjects')) {
            $data['admin_subjects'] = $this->adminSubjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'slug' => $subject->slug,
                    'description' => $subject->description,
                    'career' => [
                        'id' => $subject->career->id,
                        'name' => $subject->career->name,
                        'slug' => $subject->career->slug,
                        'university' => [
                            'id' => $subject->career->university->id,
                            'name' => $subject->career->university->name,
                            'slug' => $subject->career->university->slug
                        ]
                    ]
                ];
            });
        }

        // Agregar suscripciones si están cargadas
        if ($this->relationLoaded('subscribedUniversities')) {
            $data['subscribed_universities'] = $this->subscribedUniversities->map(function ($university) {
                return [
                    'id' => $university->id,
                    'name' => $university->name,
                    'slug' => $university->slug,
                    'description' => $university->description
                ];
            });
        }

        if ($this->relationLoaded('subscribedCareers')) {
            $data['subscribed_careers'] = $this->subscribedCareers->map(function ($career) {
                return [
                    'id' => $career->id,
                    'name' => $career->name,
                    'slug' => $career->slug,
                    'description' => $career->description,
                    'university' => [
                        'id' => $career->university->id,
                        'name' => $career->university->name,
                        'slug' => $career->university->slug
                    ]
                ];
            });
        }

        if ($this->relationLoaded('subscribedSubjects')) {
            $data['subscribed_subjects'] = $this->subscribedSubjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'slug' => $subject->slug,
                    'description' => $subject->description,
                    'career' => [
                        'id' => $subject->career->id,
                        'name' => $subject->career->name,
                        'slug' => $subject->career->slug,
                        'university' => [
                            'id' => $subject->career->university->id,
                            'name' => $subject->career->university->name,
                            'slug' => $subject->career->university->slug
                        ]
                    ]
                ];
            });
        }

        return $data;
    }
}
