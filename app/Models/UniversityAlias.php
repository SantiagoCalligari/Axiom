<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UniversityAlias extends Model
{
    use HasFactory;

    protected $fillable = ['university_id', 'alias'];

    public function university()
    {
        return $this->belongsTo(University::class);
    }
}
