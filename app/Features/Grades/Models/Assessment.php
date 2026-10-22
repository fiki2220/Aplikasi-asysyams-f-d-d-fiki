<?php

namespace App\Features\Grades\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_group_id',
        'created_by',           // ⭐ BARU: pengajar yang input
        'assessment_type',
        'month',
        'year',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classGroup()
    {
        return $this->belongsTo(\App\Features\Academic\Models\ClassGroup::class);
    }

    // ⭐ BARU: Relasi ke pengajar yang input
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}