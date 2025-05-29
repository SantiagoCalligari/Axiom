<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UniversityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'aliases' => $this->whenLoaded('aliases', function () {
                return $this->aliases->pluck('alias');
            }),
            'careers' => CareerResource::collection($this->whenLoaded('Careers')),
            'administrators' => UserResource::collection($this->whenLoaded('administrators')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
