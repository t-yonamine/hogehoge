<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DispatchCar extends Model
{
  use HasFactory;

  protected $table = 'gdispatch_cars';

  protected $fillable = [
    'lesson_car_id',
    'school_id',
    'use_date',
    'target_type',
    'period_id',
    'lesson_attend_id',
    'school_staff_id',
    'ledger_id',
  ];

  public function lessonCar()
  {
    return $this->hasOne(LessonCar::class, 'id', 'lesson_car_id');
  }

  public static function handleDelete(DispatchCar $model)
  {
    try {
      DB::transaction(function () use ($model) {
        $model->delete();
      });
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public static function handleSave(array $data, DispatchCar $model = null)
  {
    try {
      $model = $model ?: new static;
      DB::transaction(function () use ($data, $model) {
        DB::table('gdispatch_cars')->insert($data);
      });
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
