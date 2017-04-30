<?php
/**
 * User: hefan
 * Date: 17-04-12
 * Time: 16:00
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
use Illuminate\Support\Collection;

class MessageController extends Controller
{
	/** Submit message for visitors**/
	public function addMessages(Request $request){
	/* all visitors allowed*/

		/*form check*/
		$formc = $this->filter($request,[
            'title'=>'required|filled|string|min:6|max:25',
            'content'=>'required|filled|string|max:255',
            'name'=>'required|filled|string',
            'tel'=>'required|filled|digits:11',
            'email'=>'required|filled|email',
            'type'=>'required|filled|string'
        ]);
        if(!$formc)
        {
            return $this->stdResponse('-1');
        }
        
        $mess=Message::create($request->all());
        
        return $this->stdResponse('1');

	}
	
	/*Administor delete outside messages, param $id */
	public function deMessage(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
    	$item=Message::find($id);
    	if(!$item) 
    		return $this->stdResponse('-1');
     	$item->delete();
     	return $this->stdResponse('1');
	}
	
	/*Show messages*/
	public function showMessages(Request $request,$type){
     	/* administor api_token checked*/
		
		$filter=$this->filter($request,[
			'page'=>'required|filled|numeric',
			'rows'=>'required|filled|numeric'
		]);
		if(!$filter) return $this->stdResponse('-1');
		
		$allmess=Message::where('type',$type)
				->orderBy('id','desc')
				->paginate($request->rows);
				
		/*make sure that $page is within the limits of [1, lastpage] */
		if(!($request->page>=1))
			return $this->stdResponse('-1');

		if($request->page>$allmess->lastPage()){
            return $this->stdResponse('1');
        }
		else
			return $this->stdResponse('1',$allmess);	
			
	}
	
	public function getContactInfo(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$contact=Message::find($id);
        if(!$contact) 
        	return $this->stdResponse('-1');
     	
     	$item=collect(['name'=>$contact->name,'tel'=>$contact->tel,'email'=>$contact->email]);
     	
     	return $this->stdResponse('1',$item->toJson());
				
	}


}
