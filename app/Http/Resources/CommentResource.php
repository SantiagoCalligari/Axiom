<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'exam_id' => $this->exam_id,
            'parent_id' => $this->parent_id,
            'content' => $this->when(!$this->trashed(), $this->content, '[Este comentario fue eliminado]'),
            'upvotes' => $this->upvotes,
            'downvotes' => $this->downvotes,
            'comment_type' => $this->comment_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'is_deleted' => $this->trashed(),
            'user' => new UserResource($this->whenLoaded('user')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
            'parent' => new CommentResource($this->whenLoaded('parent')),
        ];

        return $data;
    }
}