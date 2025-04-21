<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Career extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'university_id'];

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
    public function University(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
