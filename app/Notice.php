<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-10
 * Time: 10:36
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $table = "notices";

    protected $primaryKey = "id";

    protected $fillable = ['title','time','article','writer','state','u_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}