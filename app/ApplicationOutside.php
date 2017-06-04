<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicationOutside extends Model
{
    protected $table = "applications_outside";

    protected $primaryKey = "id";

    protected $fillable = ['campus','campus_chinese','campus_gym_id','money','gym','gym_number','department','content','state','remark','teacher_remark','time','classtime','charger','tel','created_at','updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}