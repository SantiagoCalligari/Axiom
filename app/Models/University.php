<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function careers()
    {
        return $this->hasMany(Career::class);
    }
}
