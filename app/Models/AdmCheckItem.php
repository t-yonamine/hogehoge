<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmCheckItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gadm_check_items';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ledger_id' => 'int',
        'school_id' => 'int',
        'student_no' => 'string',
        'target_license_cd' => 'string',
        'target_license_name' => 'string',
        'target_license_names' => 'string',
        'admission_date' => 'datetime:Y-m-d',
        'lesson_start_date' => 'datetime:Y-m-d',
        'lesson_limit' => 'datetime:Y-m-d',
        'lesson_end_date' => 'datetime:Y-m-d',
        'test_limit' => 'datetime:Y-m-d',
        'moving_out_date' => 'datetime:Y-m-d',
        'moving_in_date' => 'datetime:Y-m-d',
        'discharge_date' => 'datetime:Y-m-d',
        'age_adm' => 'int',
        'name' => 'string',
        'name_kana' => 'string',
        'birth_date' => 'datetime:Y-m-d',
        'gender' => 'int',
        'zip_code' => 'string',
        'address' => 'string',
        'citizen_card_check_sw' => 'int',
        'license_check_sw' => 'int',
        'other_check_sw' => 'int',
        'other_check_text' => 'string',
        'lic_issue_date' => 'datetime:Y-m-d',
        'lic_expy_date' => 'datetime:Y-m-d',
        'lic_psc_name' => 'string',
        'lic_num' => 'string',
        'lic_cond_text' => 'string',
        'lesson_cond_glasses' => 'int',
        'lesson_cond_contact_lens' => 'int',
        'lesson_cond_hearing_aid' => 'int',
        'first_aid_crse_exempt_sw' => 'int',
        'first_aid_crse_exempt_txt' => 'string',
        'conf_sts' => 'int',
        'status' => 'int',
        'created_at' => 'datetime',
        'created_user_id' => 'int',
    ];
}
