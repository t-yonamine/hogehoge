<?php

namespace App\Models;

use App\Enums\Seq;
use App\Enums\Status;
use App\Enums\TestType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AptitudeDriving extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'gaptitude_drvs';

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'ledger_id' => 'int',
    'school_id' => 'int',
    'seq' => Seq::class,
    'test_type' => TestType::class,
    'test_date' => 'datetime:Y-m-d',
    'score' => 'string',
    'od_persty_pattern_1' => 'string',
    'od_persty_pattern_2' => 'string',
    'od_drv_aptitude' => 'string',
    'od_safe_aptitude' => 'string',
    'od_specific_rxn' => 'string',
    'od_a' => 'string',
    'od_b' => 'string',
    'od_c' => 'string',
    'od_d' => 'string',
    'od_e' => 'string',
    'od_f' => 'string',
    'od_g' => 'string',
    'od_h' => 'string',
    'od_i' => 'string',
    'od_j' => 'string',
    'od_k' => 'string',
    'od_l' => 'string',
    'od_m' => 'string',
    'od_n' => 'string',
    'od_o' => 'string',
    'od_p' => 'string',
    'status' => Status::class,
    'created_user_id' => 'int',
    'updated_user_id' => 'int',
  ];

  protected $fillable = [
    'ledger_id',
    'seq',
    'school_id',
    'test_type',
    'test_date',
    'score',
    'od_persty_pattern_1',
    'od_persty_pattern_2',
    'od_drv_aptitude',
    'od_safe_aptitude',
    'od_specific_rxn',
    'od_a',
    'od_b',
    'od_c',
    'od_d',
    'od_e',
    'od_f',
    'od_g',
    'od_h',
    'od_i',
    'od_j',
    'od_k',
    'od_l',
    'od_m',
    'od_n',
    'od_o',
    'od_p',
    'status',
    'created_user_id',
    'updated_user_id'
  ];

  public static function handleSave(array $aptitudeDrvs, AptitudeDriving $mode = null)
  {
    try {
      $mode = new AptitudeDriving();
      DB::transaction(function () use ($aptitudeDrvs, $mode) {
        $userId = Auth::id();
        $aptitudeDrvs['created_user_id'] = $userId;
        $aptitudeDrvs['updated_user_id'] = $userId;
        $mode->fill($aptitudeDrvs);
        $mode->save();
      });
    } catch (Exception $e) {
      throw $e;
    }
  }

  public static function handleSaveFile(array $data, int $userId, $model = null)
  {
    try {
      $model = $model ?: new static();
      DB::transaction(function () use ($data, $userId, $model) {
        $aptitudeDrvs['created_user_id'] = $userId;
        $aptitudeDrvs['updated_user_id'] = $userId;
        $model->fill($data);
        $model->save();
      });
    } catch (Exception $e) {
      throw $e;
    }
  }
}
