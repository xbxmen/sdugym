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
		$res = $this->filter($request,[
            'title'=>'required|filled|string|max:25',
            'content'=>'required|filled|string|max:255',
            'name'=>'required|filled|string',
            'tel'=>'required|filled|digits:11',
            'email'=>'required|filled|email',
            'type'=>'required|filled|string'
        ]);
        if(!$res)
        {
            return $this->stdResponse('-1');
        }

        try{
            $res = Message::create($request->all());

            return $res ? $this->stdResponse('1') : $this->stdResponse('-4');

        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
	}
	
	/*Administor delete outside messages, param $id */
	public function delMessage(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

     	try{
            $item = Message::find($id);
            if(count($item) == 0){
                return $this->stdResponse('-5');
            }

            $res = $item->delete();

            return $res? $this->stdResponse('1') : $this->stdResponse('-4');
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
	}
	
	/*Show messages*/
	public function showMessages(Request $request,$type){
     	/* administor api_token checked*/
		
		$filter=$this->filter($request,[
			'page'=>'required|filled|numeric',
			'rows'=>'required|filled|numeric'
		]);
		if(!$filter) return $this->stdResponse('-1');

        /*make sure that $page is within the limits of [1, lastpage] */
        if(!($request->page >= 1))
            return $this->stdResponse('-1');

		$allmess = Message::select(['title','content','created_at','id'])
                ->where('type',$type)
                ->where('state','1')
				->orderBy('id','desc')
				->paginate($request->rows);
				

		if($request->page > $allmess->lastPage()){
            return $this->stdResponse('1');
        } else{
            return $this->stdResponse('1',$allmess);
        }
	}

    /*Show All messages*/
    public function showAllMessages(Request $request,$type){

        $filter=$this->filter($request,[
            'page'=>'required|filled|numeric',
            'rows'=>'required|filled|numeric',
            'api_token'=>'required|filled'
        ]);
        if(!$filter) return $this->stdResponse('-1');

        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        /*make sure that $page is within the limits of [1, lastpage] */
        if(!($request->page >= 1))
            return $this->stdResponse('-1');

        $allmess = Message::orderBy('id','desc')
            ->paginate($request->rows);


        if($request->page > $allmess->lastPage()){
            return $this->stdResponse('1');
        } else{
            return $this->stdResponse('1',$allmess);
        }
    }


    /*获取联系方式*/
	public function getContactInfo(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

     	try{
            $contact = Message::where('id',$id)
                        ->select(['name','tel','email'])->first();

            if(count($contact) == 0)
                return $this->stdResponse('-5');

            return $this->stdResponse('1',$contact);

        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
	}

	/**/
	public function checkMessage(Request $request,$id){

        /*form check*/
        $res = $this->filter($request,[
            'state'=>'required|filled',
        //    'remark'=>'required|filled|string'
        ]);
        if(!$res)
        {
            return $this->stdResponse('-1');
        }

        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        try{
            $contact = Message::find($id);
            if(count($contact) == 0){
                return $this->stdResponse('-5');
            }

            $contact->state = $request->state;
        //    $contact->remark = $request->remark;

            $res = $contact->save();

            return $res ? $this->stdResponse('1') : $this->stdResponse('-14');

        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }

    }
}
