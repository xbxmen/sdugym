<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Applicationtrain extends Model
{
    protected $table = "applications_train";

    protected $fillable = ['campus','gym','department','content','time','classtime','charger','tel'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    public  $timestamps = false;
}