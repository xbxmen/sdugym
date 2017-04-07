<?php
/**
 * Created by PhpStorm.
 * User: zhaoshuai
 * Date: 17-3-29
 * Time: 下午12:57
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = "applications";

    protected $primaryKey = "ap_id";


    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}