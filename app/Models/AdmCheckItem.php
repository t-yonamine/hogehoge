<?php

namespace App\Models;

use App\Enums\ConfirmationRecsStatus;
use App\Enums\Gender;
use App\Enums\LicenseType as licenseTypeEnum;
use App\Enums\SchoolStaffRole;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class AdmCheckItem extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'gadm_check_items';
    protected $perPage = 20;

    const LESSON_LIMIT_MONTH_KEY =  'lesson_limit_month';
    const APTITUDE_CONF_TARGET =  'gaptitude_phys';
    const PSC_CD = 'psc_cd';

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
        'gender' => Gender::class,
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
        'conf_sts' => ConfirmationRecsStatus::class,
        'status' => Status::class,
        'created_at' => 'datetime',
        'created_user_id' => 'int',
        'expy_date' => 'datetime:Y-m-d',
    ];

    protected $fillable = [
        'ledger_id',
        'school_id',
        'student_no',
        'target_license_cd',
        'target_license_name',
        'target_license_names',
        'admission_date',
        'lesson_start_date',
        'lesson_limit',
        'lesson_end_date',
        'test_limit',
        'moving_out_date',
        'moving_in_date',
        'discharge_date',
        'age_adm',
        'name',
        'name_kana',
        'birth_date',
        'gender',
        'zip_code',
        'address',
        'citizen_card_check_sw',
        'license_check_sw',
        'other_check_sw',
        'other_check_text',
        'lic_issue_date',
        'lic_expy_date',
        'lic_psc_name',
        'lic_num',
        'lic_cond_text',
        'lesson_cond_glasses',
        'lesson_cond_contact_lens',
        'lesson_cond_hearing_aid',
        'first_aid_crse_exempt_sw',
        'first_aid_crse_exempt_txt',
        'conf_sts',
        'status',
        'created_at',
        'created_user_id',
        'expy_date',
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
        return $this->hasOne(LicenseType::class, 'license_cd', 'target_license_cd');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'adm_check_items_id', 'id');
    }

    public function aptitudePhys()
    {
        return $this->hasMany(AptitudePhysical::class, 'adm_check_items_id', 'id');
    }

    public function curLicTypes()
    {
        return $this->hasMany(CurrentLicenseType::class, 'adm_check_items_id', 'id');
    }

    public static function checkExistLicense($curLicTypes, $licenseCds)
    {
        return $curLicTypes->filter(function ($rs) use ($licenseCds) {
            return in_array($rs->license_cd, $licenseCds);
        })->count() > 0;
    }

    public static function handleUpdateStudent(array $data, AdmCheckItem $model)
    {
        try {
            DB::beginTransaction();
            $aptitudePhySeq1 = AptitudePhysical::where('id', $data['gaptitude_phys_id_1'])->first();
            $aptitudePhySeq2 = AptitudePhysical::where('id', $data['gaptitude_phys_id_2'])->first();
            // 2.入所時確認項目(gadm_check_items)の確認状態が確認済みでない場合は、関連テーブルを上書き保存する。
            $model = static::storeAdmCheck($data, $model);
            if ($model->conf_sts != ConfirmationRecsStatus::CONFIRMED()) {
                static::storeAptitudePhys($aptitudePhySeq1, $aptitudePhySeq1->getConfirmRec(), $aptitudePhySeq2,$aptitudePhySeq2->getConfirmRec(), $data);
                static::storeCurLicType($data, $model, Status::ENABLED());
            } else {
                if ($aptitudePhySeq1) {
                    // 既存の関連データをコピーし
                    $aptitudePhySeq1Clone = $aptitudePhySeq1->replicate();

                    //こちらを状態=有効とし、変更内容を上書き保存する。
                    $aptitudePhySeq1->status = Status::DISABLED();
                    $aptitudePhySeq1->save();
                    $aptitudePhySeq1Clone->push();

                    $confRecSeq1 = ConfirmationRecord::where('conf_target', self::APTITUDE_CONF_TARGET)->where('target_id', $aptitudePhySeq1->id)->where('target_id_seq', $aptitudePhySeq1->seq->value)->first();
                    $confRecSeq1Clone = $confRecSeq1->replicate();

                    $confRecSeq1->status = ConfirmationRecsStatus::WAITING();
                    $confRecSeq1->staff_id = null;
                    $confRecSeq1->staff_name = null;
                    $confRecSeq1->confirm_date = null;
                    $confRecSeq1->save();
                }

                if ($aptitudePhySeq2) {
                    // 既存の関連データをコピーし
                    $aptitudePhySeq2Clone = $aptitudePhySeq2->replicate();

                    //こちらを状態=有効とし、変更内容を上書き保存する。
                    $aptitudePhySeq2->status = Status::DISABLED();
                    $aptitudePhySeq2->save();

                    $confRecSeq2 = ConfirmationRecord::where('conf_target', self::APTITUDE_CONF_TARGET)->where('target_id', $aptitudePhySeq2->id)->where('target_id_seq', $aptitudePhySeq2->seq->value)->first();

                    $confRecSeq2Clone = $confRecSeq2->replicate();
                    $confRecSeq2->status = ConfirmationRecsStatus::WAITING();
                    $confRecSeq2->staff_id = null;
                    $confRecSeq2->staff_name = null;
                    $confRecSeq2->confirm_date = null;
                    $confRecSeq2->save();
                }

                static::storeAptitudePhys($aptitudePhySeq1Clone, $confRecSeq1Clone, $aptitudePhySeq2Clone, $confRecSeq2Clone, $data);

                static::storeCurLicType($data, $model, Status::DISABLED());
            }
            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
        }
        return $model;
    }

    protected static function storeAdmCheck(array $data, $model): AdmCheckItem
    {
        $model->fill($data);
        $model->citizen_card_check_sw = isset($data['citizen_card_check_sw']) && $data['citizen_card_check_sw'] == 'on';
        $model->license_check_sw = isset($data['license_check_sw']) && $data['license_check_sw'] == 'on';
        $model->other_check_sw = isset($data['other_check_sw']) && $data['other_check_sw'] == 'on';
        // 「教習開始年月日」が入力されたとき、再計算。
        $lessonLimitMonth = SystemValue::where('sv_key', self::LESSON_LIMIT_MONTH_KEY)->first();
        $model->lesson_limit = $model->lesson_start_date?->addMonths((int)$lessonLimitMonth->sv_value)->subDay();
        if (!$model->other_check_sw) {
            $model->other_check_text = '';
        }
        $model->lesson_cond_glasses = isset($data['lesson_cond_glasses']) && $data['lesson_cond_glasses'] == 'on';
        $model->lesson_cond_contact_lens = isset($data['lesson_cond_contact_lens']) && $data['lesson_cond_contact_lens'] == 'on';
        $model->lesson_cond_hearing_aid = isset($data['lesson_cond_hearing_aid']) && $data['lesson_cond_hearing_aid'] == 'on';
        $model->first_aid_crse_exempt_sw = $data['first_aid_crse_exempt_sw'];
        $model->save();
        return $model;
    }

    private static function storeAptitudePhys($aptitudePhySeq1, $confirmRec1, $aptitudePhySeq2, $confirmRec2, array $data)
    {
        $aptitudePhySeq1->eyesight_naked_left = $data['eyesight_naked_left_1'];
        $aptitudePhySeq1->eyesight_naked_right = $data['eyesight_naked_right_1'];
        $aptitudePhySeq1->eyesight_naked_both = $data['eyesight_naked_both_1'];
        $aptitudePhySeq1->eyesight_correct_left = $data['eyesight_correct_left_1'];
        $aptitudePhySeq1->eyesight_correct_right = $data['eyesight_correct_right_1'];
        $aptitudePhySeq1->eyesight_correct_both = $data['eyesight_correct_both_1'];
        $aptitudePhySeq1->field_of_view_left = $data['field_of_view_left_1'];
        $aptitudePhySeq1->field_of_view_right = $data['field_of_view_right_1'];
        $aptitudePhySeq1->field_of_view_both = $data['eyesight_naked_left_1'];
        $aptitudePhySeq1->color_discimination_cd = $data['color_discimination_cd_1'];
        $aptitudePhySeq1->hearing_cd = $data['hearing_cd_1'];
        $aptitudePhySeq1->athletic_ability_cd = $data['athletic_ability_cd_1'];
        $aptitudePhySeq1->save();

        static::createConfirmRec($confirmRec1, $aptitudePhySeq1->id,  $data['staff_id_1'], $data['confirm_date_1']);

        $aptitudePhySeq2->eyesight_naked_left = $data['eyesight_naked_left_2'];
        $aptitudePhySeq2->eyesight_naked_right = $data['eyesight_naked_right_2'];
        $aptitudePhySeq2->eyesight_naked_both = $data['eyesight_naked_both_2'];
        $aptitudePhySeq2->eyesight_correct_left = $data['eyesight_correct_left_2'];
        $aptitudePhySeq2->eyesight_correct_right = $data['eyesight_correct_right_2'];
        $aptitudePhySeq2->eyesight_correct_both = $data['eyesight_correct_both_2'];
        $aptitudePhySeq2->field_of_view_left = $data['field_of_view_left_2'];
        $aptitudePhySeq2->field_of_view_right = $data['field_of_view_right_2'];
        $aptitudePhySeq2->field_of_view_both = $data['eyesight_naked_left_2'];
        $aptitudePhySeq2->color_discimination_cd = $data['color_discimination_cd_2'];
        $aptitudePhySeq2->hearing_cd = $data['hearing_cd_2'];
        $aptitudePhySeq2->athletic_ability_cd = $data['athletic_ability_cd_2'];
        $aptitudePhySeq2->save();

        static::createConfirmRec($confirmRec2, $aptitudePhySeq2->id,  $data['staff_id_2'], $data['confirm_date_2']);
    }

    private static function storeCurLicType(array $data, $admcheckItem, $status)
    {
        // 仮免許
        CurrentLicenseType::handleSave(licenseTypeEnum::PL_MVL, $admcheckItem, $status, isset($data['pl_mvl']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::PM_MVL, $admcheckItem, $status, isset($data['pm_mvl']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::PSM_MVL, $admcheckItem, $status, isset($data['psm_mvl']));

        CurrentLicenseType::handleSave(licenseTypeEnum::PS_MVL_MT, $admcheckItem, $status, isset($data['ps_mvl_mt']));
       
        // 一種免許
        CurrentLicenseType::handleSave(licenseTypeEnum::L_MVL, $admcheckItem, $status, isset($data['l_mvl']));
       
        CurrentLicenseType::handleSave(licenseTypeEnum::M_MVL, $admcheckItem, $status, isset($data['m_mvl']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::SM_MVL, $admcheckItem, $status, isset($data['sm_mvl']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::S_MVL_MT, $admcheckItem, $status, isset($data['s_mvl_mt']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::SL_MVL, $admcheckItem, $status, isset($data['sl_mvl']));
       
        CurrentLicenseType::handleSave(licenseTypeEnum::L_ML, $admcheckItem, $status, isset($data['l_ml']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::S_ML, $admcheckItem, $status, isset($data['s_ml']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::SS_MVL, $admcheckItem, $status, isset($data['ss_mvl']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::MBL, $admcheckItem, $status, isset($data['mbl']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::TOWING, $admcheckItem, $status, isset($data['towing']));

        // 二種免許
        CurrentLicenseType::handleSave(licenseTypeEnum::L_MVL_2, $admcheckItem, $status, isset($data['l_mvl_2']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::M_MVL_2, $admcheckItem, $status, isset($data['m_mvl_2']));
       
        CurrentLicenseType::handleSave(licenseTypeEnum::S_MVL_2, $admcheckItem, $status, isset($data['s_mvl_2']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::SL_MVL_2, $admcheckItem, $status, isset($data['sl_mvl_2']));
        
        CurrentLicenseType::handleSave(licenseTypeEnum::TOWING_2, $admcheckItem, $status, isset($data['towing_2']));
    }

    private static function createConfirmRec($confirmRec, $aptitudeId, $schoolStaffId, $confirmDate) 
    {
        $confirmRec = $confirmRec ?? new ConfirmationRecord();

        $testerFirst = SchoolStaff::where('id', $schoolStaffId)->where('role', '&', SchoolStaffRole::APTITUDE_TESTER)->where('status', Status::ENABLED())->first();
        $confirmRec->staff_id = $testerFirst?->id;
        $confirmRec->staff_name = $testerFirst?->name;
        $confirmRec->confirm_date = $confirmDate;
        $confirmRec->target_id = $aptitudeId;
        $confirmRec->save();
    }
}
