<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CurrentLicenseType extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'gcur_lic_types';

  public static function handleSave($licenseCd, $admCheckItem, $status, $isCreate)
  {
    try {
      $model = CurrentLicenseType::where('adm_check_items_id', $admCheckItem->id)->where('license_cd', $licenseCd)->first();
      if (!$model) {
        $model = new static();
        $model->ledger_id = $admCheckItem->ledger_id;
        $model->school_id = $admCheckItem->school_id;
        $model->adm_check_items_id = $admCheckItem->id;
        $model->status = $status;
        $model->license_cd = $licenseCd;
        $model->created_user_id = Auth::id();
        $model->updated_user_id = Auth::id();
      } else if (!$isCreate) {
        $model->deleted_at = now();
        $model->deleted_user_id = Auth::id();
        $model->updated_user_id = Auth::id();
      }
      $model->save();
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
