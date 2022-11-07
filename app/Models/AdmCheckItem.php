<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmCheckItem extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'gadm_check_items';
    protected $perPage = 20;

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
        'status' => Status::class,
        'created_at' => 'datetime',
        'created_user_id' => 'int',
        'expy_date' => 'datetime:Y-m-d',
    ];

    public static function buildQuery(array $params): Builder
    {
        return static::when(isset($params['student_no']), function (Builder $query) use ($params) {
            return $query->where('gadm_check_items.student_no', 'like', "%{$params['student_no']}%");
        })->when(isset($params['name_kana']), function (Builder $query)  use ($params) {
            return $query->where('gadm_check_items.name_kana', 'like', "%{$params['name_kana']}%");
        });
    }

    public function ledger()
    {
        return $this->hasOne(Ledger::class, 'id', 'ledger_id');
    }

    public function licenseType()
    {
        return $this->HasOne(LicenseType::class, 'license_cd', 'target_license_cd');
    }
}
