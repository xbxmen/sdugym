<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-15
 * Time: 14:36
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class CampusGym extends Model
{
    protected $table = "campus_gym";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = ['campus','campus_chinese','gym','gym_chinese','type','number','use_area','build_area','material','build_year','design_year'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}