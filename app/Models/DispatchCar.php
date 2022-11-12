<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchCar extends Model
{
  use HasFactory;

  protected $table = 'gdispatch_cars';

  public function lessonCar()
  {
    return $this->hasOne(LessonCar::class, 'id', 'lesson_car_id');
  }
}
