<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'title',
        'professor_name',
        'semester',
        'year',
        'is_resolved',
        'exam_type',
        'exam_date',
        'file_path',
        'original_file_name',
        'mime_type',
        'file_size',
        'ocr_text', // Include this even if null for now
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'exam_date' => 'date',
    ];
    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }

        return null;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = ['download_url']; // Add 'download_url' to the appended attributes


    public function uploader()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    public function resolution()
    {
        return $this->hasOne(Resolution::class);
    }
}
