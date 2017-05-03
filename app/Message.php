<?php
/**
 * User: hefan
 * Date: 17-04-12
 * Time: 16:00
 */	
	
namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "messages";

    protected $primaryKey = "id";

    public $timestamps = true;

    protected $fillable = [ 'type','title','content','name','tel','email','state','remark'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

}