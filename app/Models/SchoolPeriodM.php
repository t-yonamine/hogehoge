<?php

namespace App\Models;

use App\Enums\PeriodType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPeriodM extends Model
{
    use HasFactory;

    protected $table = "gschool_period_m";


    protected $casts = [
        'period_type' => PeriodType::class,
        'period_from' => 'datetime',
        'period_to' => 'datetime',
    ];

    public function period()
    {
        return $this->hasMany(Period::class, 'period_num', 'period_num');
    }
}
