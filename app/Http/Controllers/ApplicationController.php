<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-10
 * Time: 10:36
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Application;
use App\Applicationtrain;
use App\Schedule;
class ApplicationController extends Controller
{
	
	/***************************** campus activities application *******************************************/
    //显示 申请表情况
    public function showApply(Request $request,$campus,$gym){
    	/*验证用户*/
    	if(!$this->check_token($request->input('api_token'))){
    		return $this->stdResponse('-3');
    	}
    	
        /*检查表单*/
        $res = $this->filter($request,[
            'start'=>'required|filled|date_format:"Y-m-d"',
            'end'=>'required|filled|date_format:"Y-m-d"'
        ]);
        if(!$res)
        {
            return $this->stdResponse('-1');
        }
        /*获取结果*/
        $schedules = Application::where('campus',$campus)
            ->where('gym',$gym)
            ->whereBetween('time',array($request->input('start'),$request->input('end')))
            ->get();
        if(!$schedules->count() > 0){
            return $this->stdResponse("-5");
        }
        return $this->stdResponse("1",$schedules);
    }

  
    
    //申请表提交（有安全检查吗这里，需不需要验证下浏览器发出的post？）
    public function doApply(Request $request){
    	/* all user allowed */
    	/* form checked*/
    	$res=$this->filter($request,[
    	'campus'=>'required|filled',
    	'gym'=>'required|filled',
    	'time'=>'required|filled|date_format:"Y-m-d"',
    	'classtime'=>'required|filled|string',
    	'major'=>'required|filled',
    	'content'=>'required|string|filled',
    	'pnumber'=>'required|filled|integer',
    	'charger'=>'required|filled|string|max:4',
    	'tel'=>'required|filled|digits:11',
    	'cost'=>'required|filled',
    	'remark'=>'required|filled|string|max:255'
    	
    	]);
    	if(!$res){
    		return $this->stdResponse('-1');
    	}
    	
    	$app=Application::create($request->all());
//  	$app=$request->all();

    	return $this->stdResponse('1');
    	
    }
    /* administor check_apply submit,params  $id*/
     public function checkApply(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$res=$this->filter($request,[
     		'state'=>'required|digits:1|filled']);
     	if(!$res){
     		return $this->stdResponse('-1');
     	}
     	
     	$item=Application::find($id);
     	$item->state=$request->input('state');
     	
     	if($request->state== 3){
     		$day=$item->time;
     		$classtime=$item->classtime;
     		/*最好保证这条日期记录存在 */
     		$sche=Schedule::where('date',$day)
     					->where('campus',$item->campus)
     					->where('gym',$item->gym)
     					->firstOrFail();
     		
     		$pieces=explode(',',$classtime);
     		
     		foreach ($pieces as $clst) {
     			if(strcmp($sche->$clst,"空闲")==0){
     				$sche->$clst='安排';
     			}else{
     				return $this->stdResponse('-10');
     			}
     		}
     		$sche->save();
     		
     	}
     	$item->save();
     	return $this->stdResponse('1');
     	
     }
     
     /* adminitor delete apply form*/
     public function deApply(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$item=Application::find($id);
     	$item->delete();
     	return $this->stdResponse('1');
     }
     
    /****************************************  train application  ********************************************/ 
    /* train apply form submit */
    public function  trainDoApply(Request $request){
     /* all visitor allowed*/
   
      	$filt=$this->filter($request,[
      		'campus'=>'required|size:2|filled',
       		'gym'=>'required|filled',
       		'department'=>'required|filled|string',
    		'content'=>'required|string|filled',
    		'time'=>'required|filled|date_format:"Y-m-d"',
    		'classtime'=>'required|filled|string',
    		'charger'=>'required|filled|string|max:4',
    		'tel'=>'required|filled|digits:11',
      	]);
      	if(!$filt)
      		return $this->stdResponse('-1');
    	
    	$item=Applicationtrain::create($request->all());
    	//$item=$request->all();
    	 
    	return $this->stdResponse('1');
    }
    /* show train application for administor */
    public function trainShowApply(Request $request,$campus,$gym){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}    	
    	
    	$applications = Applicationtrain::where('campus',$campus)
            ->where('gym',$gym)
            ->get();
        if(!($applications->count() > 0)){
            return $this->stdResponse("-5");
        }
        return $this->stdResponse("1",$applications);
    	
    }
    
    public function trainCheckApply(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$res=$this->filter($request,[
     		'state'=>'required|digits:1|filled']);
     	if(!$res){
     		return $this->stdResponse('-1');
     	}
     	
     	$item=Applicationtrain::find($id);
     	$item->state=$request->input('state');
     	$item->save();
     	return $this->stdResponse('1');    	
    }

     /* adminitor delete train apply form*/
     public function trainDeApply(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$item=Applicationtrain::find($id);
     	$item->delete();
     	return $this->stdResponse('1');
     }
}