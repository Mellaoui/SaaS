<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function scopeAdmin() {
        return $this->belongsToMany(User::class)->wherePivot('role', 'admin');
    }

    public function scopeEmployees() {
        return $this->belongsToMany(User::class)->wherePivot('role', 'employee');
    }
}
