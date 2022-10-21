<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolStaff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "gschool_staffs";

    protected $fillable = [
        'id',
        'school_id',
        'school_staff_no',
        'name',
        'role',
    ];
}
