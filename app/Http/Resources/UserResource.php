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
            $data['admin_universities'] = UniversityResource::collection($this->adminUniversities);
        } elseif ($this->relationLoaded('adminCareers')) {
            $data['admin_careers'] = CareerResource::collection($this->adminCareers);
        } elseif ($this->relationLoaded('adminSubjects')) {
            $data['admin_subjects'] = SubjectResource::collection($this->adminSubjects);
        }

        return $data;
    }
}
