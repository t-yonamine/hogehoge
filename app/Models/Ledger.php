<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ledger extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gledgers';

    public function gadmCheckItems(){
        return $this->hasOne(AdmCheckItem::class, 'ledger_id', 'id');
    }

    public function glessonAttends(){
        return $this->hasMany(LessonAttend::class, 'ledger_id', 'id');
    }
}
