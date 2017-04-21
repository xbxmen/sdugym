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
    protected $table = "mess_boards";

    protected $primaryKey = "id";

    protected $fillable = [ 'type','title','content','name','tel','email'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    public $timestamps = true;

}