<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-27
 * Time: 16:22
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $table = "finances";

    protected $primaryKey = "id";

    protected $fillable = ['title','u_id','content','money','billing_time','remark','campus'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}