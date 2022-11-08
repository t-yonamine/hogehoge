<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ledger extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gledgers';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'int',
        'school_id' => 'int',
        'student_no' => 'string',
        'target_license_cd' => 'string',
        'admission_date' => 'datetime:Y-m-d',
        'effect_meas1_date' => 'datetime:Y-m-d',
        'ascertain1_date' => 'datetime:Y-m-d',
        'compltst_date' => 'datetime:Y-m-d',
        'pl_test_date' => 'datetime:Y-m-d',
        'effect_meas2_date' => 'datetime:Y-m-d',
        'ascertain2_date' => 'datetime:Y-m-d',
        'gradtst_date' => 'datetime:Y-m-d',
        'status' => Status::class,
    ];

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
        return $this->hasMany(AdmCheckItem::class);
    }

    public function lessonAttend()
    {
        return $this->hasMany(LessonAttend::class);
    }
}
