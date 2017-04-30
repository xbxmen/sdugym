<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-26
 * Time: 12:47
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = "documents";

    protected $primaryKey = "id";

    protected $fillable = ['path','title','u_id','path'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['updated_at'];

}