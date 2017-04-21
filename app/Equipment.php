<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-15
 * Time: 14:36
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = "equipments";

    protected $primaryKey = "id";

    protected $fillable = ['campus','gym','equipment_name','buy_date','buy_number','in_number','no_number','use_campus','use_number','price','remark'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}