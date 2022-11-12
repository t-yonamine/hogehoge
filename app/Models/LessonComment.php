<?php

namespace App\Models;

use App\Enums\CommentType;
use App\Enums\StageType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
