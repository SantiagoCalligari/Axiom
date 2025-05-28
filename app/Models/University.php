<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

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
    /*
     *  @return HasMany
     * */
    public function Careers(): HasMany
    {
        return $this->hasMany(Career::class);
    }

    public function administrators()
    {
        return $this->belongsToMany(User::class, 'university_admin');
    }
}
