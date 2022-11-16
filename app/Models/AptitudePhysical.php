<?php

namespace App\Models;

use App\Enums\Seq;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AptitudePhysical extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'gaptitude_phys';

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'seq' => Seq::class,
    'confirm_date' => 'datetime',
  ];

  protected $fillable = [
    'eyesight_naked_left',
    'eyesight_naked_right',
    'eyesight_naked_both',
    'eyesight_correct_left',
    'eyesight_correct_right',
    'eyesight_correct_both',
    'field_of_view_left',
    'field_of_view_right',
    'field_of_view_both',
  ];

  public function confirmationRecs()
  {
    return $this->hasOne(ConfirmationRecord::class, 'target_id', 'id');
  }

  public static function hanldeSave(AptitudePhysical $model = null)
  {
    $model = $model ?? new static();
    try {

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getConfirmRec() {
    return $this->confirmationRecs()->where('conf_target', $this->table)->where('target_id_seq', $this->seq)->first();
  }
}
