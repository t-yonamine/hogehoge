<?php

namespace App\Models;

use App\Enums\ConfgInformationType;
use App\Enums\Status;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ConfirmationRecord extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'gconfirmation_recs';

  protected $casts = [
    'conf_type' => ConfgInformationType::class,
  ];

  protected $fillable = [
    'ledger_id',
    'school_id',
    'conf_target',
    'target_id',
    'conf_type',
    'conf_role',
    'status',
    'created_user_id',
    'updated_at',
    'updated_user_id'
  ];

  public static function handleSave(array $data, ConfirmationRecord $model = null)
  {
    try {
      $model = $model ?: new static;
      DB::transaction(function () use ($data, $model) {
        $model->fill($data);
        $model->save();
      });
    } catch (Exception $e) {
      throw $e;
    }
  }
}
