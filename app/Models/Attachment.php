<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_path',
        'original_file_name',
        'mime_type',
        'file_size',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->file_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    protected $appends = ['download_url'];
} 