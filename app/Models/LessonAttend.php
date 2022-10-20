<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonAttend extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'glesson_attends';

    protected $casts = [
        'period_date' => 'datetime:Y-m-d',
        'period_from' => 'datetime:h:i',
    ];

    public function gschoolStaff(){
        return $this->hasOne(SchoolStaff::class, 'id', 'school_staff_id');
    }

}
