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
        'ocr_text',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'exam_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Scopes para filtrar exámenes por estado de aprobación
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    // Scope para filtrar exámenes por universidad
    public function scopeByUniversity($query, $universityId)
    {
        return $query->whereHas('subject.career.university', function ($q) use ($universityId) {
            $q->where('id', $universityId);
        });
    }

    // Scope para filtrar exámenes por carrera
    public function scopeByCareer($query, $careerId)
    {
        return $query->whereHas('subject.career', function ($q) use ($careerId) {
            $q->where('id', $careerId);
        });
    }

    // Scope para filtrar exámenes por materia
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    // Relación con el aprobador
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Método para verificar si un usuario puede aprobar este examen
    public function canBeApprovedBy(User $user): bool
    {
        // Si el usuario es admin general
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        // Si el usuario es admin de la universidad
        if ($user->adminUniversities()->where('universities.id', $this->subject->career->university_id)->exists()) {
            return true;
        }

        // Si el usuario es admin de la carrera
        if ($user->adminCareers()->where('careers.id', $this->subject->career_id)->exists()) {
            return true;
        }

        // Si el usuario es admin de la materia
        if ($user->adminSubjects()->where('subjects.id', $this->subject_id)->exists()) {
            return true;
        }

        return false;
    }

    // Método para aprobar un examen
    public function approve(User $approver, ?string $rejectionReason = null): bool
    {
        if (!$this->canBeApprovedBy($approver)) {
            return false;
        }

        $this->approval_status = 'approved';
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->rejection_reason = null;
        
        return $this->save();
    }

    // Método para rechazar un examen
    public function reject(User $rejector, string $rejectionReason): bool
    {
        if (!$this->canBeApprovedBy($rejector)) {
            return false;
        }

        $this->approval_status = 'rejected';
        $this->approved_by = $rejector->id;
        $this->approved_at = now();
        $this->rejection_reason = $rejectionReason;
        
        return $this->save();
    }

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
