<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeHistory extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'gchange_history';

}
