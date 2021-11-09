<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function progressReports()
    {
        return $this->hasMany(ProgressReport::class);
    }
}
