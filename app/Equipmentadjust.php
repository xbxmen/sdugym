<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-15
 * Time: 14:36
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipmentadjust extends Model
{
    protected $table = "equipment_adjust";

    protected $primaryKey = "id";

    protected $fillable = ['belong_campus','use_campus','belong_gym','use_gym','equipment_name','use_number','remark','adminname'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}