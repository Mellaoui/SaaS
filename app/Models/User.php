<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class)->withPivot('role')->withTimestamps();
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class)->withTimestamps();
    }

    public function isSuperAdmin()
    {
        return $this->super_admin == true;
    }

    public function userReports()
    {
        return $this->hasMany(UserReport::class);
    }
}
