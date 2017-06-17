<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-10
 * Time: 10:36
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class GymRule extends Model
{
    protected $table = "gym_rules";

    protected $primaryKey = "id";

    protected $fillable = ['title','time','article','writer','state','u_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}