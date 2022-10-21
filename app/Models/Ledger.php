<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ledger extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gledgers';

    protected $fillable = [
        'id',
        'school_id',
        'student_no',
        'target_license_cd',
        'admission_date',
        'lesson_sts',
        'lec1st_omit',
        'lec1st_date',
        'effect_meas1_date',
        'ascertain1_date',
        'compltst_date',
        'pl_test_date',
        'effect_meas2_date',
        'ascertain2_date',
        'gradtst_date',
        'status',
    ];

    public function admCheckItem()
    {
        return $this->hasOne(AdmCheckItem::class);
    }

    public function lessonAttend()
    {
        return $this->hasMany(LessonAttend::class);
    }
}
