<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonItemMastery extends Model
{
    use HasFactory;

    protected $table = "glesson_item_mastery";

    public function lessonItems() {
        return $this->hasOne(LessonItem::class, 'lesson_item_id', 'id');
    }

}
