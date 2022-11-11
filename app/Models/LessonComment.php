<?php

namespace App\Models;

use App\Enums\CommentType;
use App\Enums\StageType;
use App\Enums\Status;
use App\Enums\PublicLevelStatus;
use App\Enums\Status;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonComment extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'glesson_comments';

  protected $casts = [
    'ledger_id' => 'int',
    'school_id' => 'int',
    'comment_date' => 'datetime:Y-m-d',
    'school_staff_id' => 'int',
    'comment_type' => CommentType::class,
    'comment_text' => 'string',
    'stage' => StageType::class,
    'lesson_attend_id' => 'int',
    'public_level' => 'int',
    'status' => Status::class,
  ];
  
  public static function handleSave(array $data, Ledger $ledgers, LessonAttend $lessonAttends, LessonComment $model = null)
  {
    try {
      $model = $model ?: new static;
      DB::transaction(function () use ($data, $model, $ledgers, $lessonAttends) {
        $model['school_id'] = $ledgers->school_id;
        $model['ledger_id'] = $ledgers->id;
        $model['comment_date'] = now();
        $model['school_staff_id'] = Auth::id();
        $model['comment_text'] = $data['comment_text'];
        $model['stage'] = $lessonAttends->stage;
        $model['lesson_attend_id'] = $lessonAttends->id;
        $model['public_level'] = PublicLevelStatus::INTERNAL;
        $model['status'] = Status::ENABLED;
        $model->save();
      });
    } catch (Exception $e) {
      throw $e;
    }
  }
}
