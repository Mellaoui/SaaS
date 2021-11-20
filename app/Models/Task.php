<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $dateFormat = 'Y-MMM-DD HH:mm';

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

    // protected $casts = [
    //     'start_date' => 'immutable_datetime:Y-M-d H:i',
    //     'due_date' => 'immutable_datetime:Y-M-d H:i'
    // ];

    public function progressReports()
    {
        return $this->hasMany(ProgressReport::class);
    }
}
