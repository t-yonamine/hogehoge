<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrentLicenseType extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'gcur_lic_types';

}
