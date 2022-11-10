<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DsipatchCar extends Model
{
  use HasFactory;

  protected $table = 'gdsipatch_cars';

  public function lessonCars()
  {
    return $this->belongsTo(LessonCar::class, 'lesson_car_id', 'id');
  }
}
