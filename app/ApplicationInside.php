<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-10
 * Time: 10:36
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicationInside extends Model
{
    protected $table = "applications_inside";

    protected $primaryKey = "id";

    protected $fillable = ['campus','campus_chinese','campus_gym_id','gym_number','gym','time','classtime','major','content','pnumber','teacher','teacher_tel','charger','tel','money','state','remark','teacher_remark','created_at','updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}