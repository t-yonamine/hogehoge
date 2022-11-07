<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'gcertificates';
 /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
      'ledger_id' => 'int',
      'school_id' => 'int',
      'adm_check_items_id' => 'int',
      'cert_type' => 'int',
      'issue_date' => 'datetime:Y-m-d',
      'expy_date' => 'datetime:Y-m-d',
      'cert_num' => 'string',
      'status' => Status::class,
  ];
}
