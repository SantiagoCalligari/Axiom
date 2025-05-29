<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function aliases(): HasMany
    {
        return $this->hasMany(UniversityAlias::class);
    }

    /**
     * Busca universidades por nombre o alias usando búsqueda fuzzy
     */
    public static function fuzzySearch($query)
    {
        $universities = self::where('name', 'LIKE', "%{$query}%")
            ->orWhereHas('aliases', function ($q) use ($query) {
                $q->where('alias', 'LIKE', "%{$query}%");
            })
            ->get();

        // Si no hay resultados exactos, intentamos búsqueda fuzzy
        if ($universities->isEmpty()) {
            $universities = self::with('aliases')->get()->filter(function ($university) use ($query) {
                // Buscamos coincidencias fuzzy en el nombre
                $nameSimilarity = similar_text(strtolower($university->name), strtolower($query), $namePercent);
                
                // Buscamos coincidencias fuzzy en los alias
                $aliasSimilarity = $university->aliases->max(function ($alias) use ($query) {
                    similar_text(strtolower($alias->alias), strtolower($query), $aliasPercent);
                    return $aliasPercent;
                });

                // Retornamos true si hay una coincidencia mayor al 70%
                return max($namePercent, $aliasSimilarity) >= 70;
            });
        }

        return $universities;
    }
}
