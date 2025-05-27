<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResolutionResource extends JsonResource
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
            'exam_id' => $this->exam_id,
            'user_id' => $this->user_id,
            'file_path' => $this->file_path,
            'original_file_name' => $this->original_file_name,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'comments' => $this->comments,
            'download_url' => $this->download_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'uploader' => new UserResource($this->whenLoaded('uploader')),
        ];
    }
}
