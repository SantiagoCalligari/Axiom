<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


/**
 * @property string $password
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'display_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function get_by_email($email): ?User
    {
        return self::query()->where('email', $email)->first();
    }

    public function validate_password($password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    // Relaciones para roles administrativos específicos
    public function adminUniversities()
    {
        return $this->belongsToMany(University::class, 'university_admin');
    }

    public function adminCareers()
    {
        return $this->belongsToMany(Career::class, 'career_admin');
    }

    public function adminSubjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_admin');
    }

    // Relaciones para suscripciones
    public function subscribedUniversities()
    {
        return $this->belongsToMany(University::class, 'university_user');
    }

    public function subscribedCareers()
    {
        return $this->belongsToMany(Career::class, 'career_user');
    }

    public function subscribedSubjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_user');
    }
}
