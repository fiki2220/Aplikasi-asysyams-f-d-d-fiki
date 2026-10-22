<?php

namespace App\Features\Meetings\Models;

use App\Features\Academic\Models\ClassGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_group_id',
        'user_id',
        'title',
        'date',
        'notes',
        'teacher_check_in_at',  // ✅ TAMBAHAN
        'teacher_check_out_at', // ✅ TAMBAHAN
    ];

    protected $casts = [
        'date' => 'date',
        'teacher_check_in_at' => 'datetime',
        'teacher_check_out_at' => 'datetime',
    ];

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}