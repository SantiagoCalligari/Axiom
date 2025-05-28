<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['career_id', 'name', 'slug', 'description'];
    protected static function boot(): void
    {
        parent::boot();

        // Listen for the 'creating' event to set the slug on initial creation
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });

        // Listen for the 'updating' event to potentially regenerate the slug
        static::updating(function ($model) {
            // Only update the slug if the 'name' attribute has changed
            if ($model->isDirty('name')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function Career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function Exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function administrators()
    {
        return $this->belongsToMany(User::class, 'subject_admin');
    }
}
