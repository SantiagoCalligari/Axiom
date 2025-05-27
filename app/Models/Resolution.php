<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'file_path',
        'original_file_name',
        'mime_type',
        'file_size',
    ];

    public function getDownloadUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        $exists = Storage::disk('public')->exists($this->file_path);
        if (!$exists) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    protected $appends = ['download_url'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
