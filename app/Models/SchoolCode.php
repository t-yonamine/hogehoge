<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolCode extends Model
{
    use HasFactory;

    protected $table = "gschool_codes";

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'school_id' => 'int',
        'cd_name' => 'string',
        'cd_value' => 'string',
        'access_key' => 'string',
        'display_order' => 'string',
        'cd_text' => 'string',
        'cd_comment' => 'string',
        'status' => 'int',
    ];
}
