<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Power extends Model
{

    protected $table = "power";

    protected $primaryKey = "u_id";
    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'u_d','zx','bt', 'qf', 'rj','xl','hj','news','finance','equipment'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
