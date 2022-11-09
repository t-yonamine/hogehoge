<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Tests extends Model
{
    use HasFactory;

    protected $table = 'gtests';
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'school_id' => 'int',
        'num_of_days' => 'int',
        'test_date' => 'datetime',
        'test_type' => 'int',
        'period_num_from' => 'int',
        'period_num_to' => 'int',
        'created_at' => 'datetime',
        'created_user_id' => 'int',
        'updated_at' => 'datetime',
        'updated_user_id' => 'int',
    ];

    protected $fillable = [
        'school_id',
        'num_of_days',
        'test_date',
        'test_type',
        'period_num_from',
        'period_num_to',
        'created_at' ,
        'created_user_id',
        'updated_at',
        'updated_user_id',
    ];


    public static function handleSave(array $dataTest, Tests $model = null)
    {
        try {
            $model = $model ? : new static();
            DB::transaction(function () use ($dataTest, $model) {
                $model->fill($dataTest);
                $model->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
        return $model;
    }
}
