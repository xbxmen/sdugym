<?php 
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-15
 * Time: 13:57
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Equipment;
use App\User;
use App\Equipmentadjust;
class EquipmentController extends Controller{
/************************* Equipment manage  prefix /api/equipments***************************/	

	public function postRegistry(Request $request){	
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
    	$res=$this->filter($request,[
    	'campus'=>'required|filled',
    	'gym'=>'required|filled',
    	'equipment_name'=>'required|filled',
    	'buy_date'=>'required|filled|date_format:"Y-m-d',
    	'buy_number'=>'required|filled|integer',
    	'in_number'=>'required|integer|filled',
    	'no_number'=>'required|filled|integer',
    	'use_campus'=>'required|filled|string',
    	'use_number'=>'required|filled|integer',
    	'price'=>'required|filled|integer',
    	'remark'=>'required|filled|string|max:255'
    	
    	]);
    	if(!$res){
    		return $this->stdResponse('-1');
    	}
	      
     	$eqp=Equipment::create($request->all());

	  	return $this->stdResponse('1');  
	}
	
	public function getRegistry(Request $request,$campus){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	$eqpmts=Equipment::where('campus',$campus)->get();		
		return $this->stdResponse('1',$eqpmts);
	
	}
      /* adminitor delete apply form*/
     public function deRegistry(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$item=Equipment::find($id);
     	$item->delete();
     	
     	return $this->stdResponse('1');
     }
 /************************* Equipment adjust  ,prefix /api/equipments/adjust ***************************/	
   	public function postAdjust(Request $request){
      	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}  
    	
    	$res=$this->filter($request,[
    	'belong_campus'=>'required|filled',
    	'use_campus'=>'required|filled',
    	'belong_gym'=>'required|filled',
    	'use_gym'=>'required|filled',
    	'equipment_name'=>'required|filled|string',
    	'use_number'=>'required|integer|filled',
    	'remark'=>'required|filled|string|max:255'
    	
    	]);
    	if(!$res){
    		return $this->stdResponse('-1');
    	}     	
     	$admin=	User::where('api_token',$request->api_token)->first();		
     	
     	$newitem=new Equipmentadjust;
     	
     	$newitem->belong_campus=$request->belong_campus;
     	$newitem->use_campus=$request->use_campus;
     	$newitem->belong_gym=$request->belong_gym;
     	$newitem->use_gym=$request->use_gym;
     	$newitem->equipment_name=$request->equipment_name;
     	$newitem->use_number=$request->use_number;
     	$newitem->remark=$request->remark;
     	$newitem->adminname=$admin->schoolnum;
     	
     	$newitem->save();
  
     	return $this->stdResponse('1');
   		
   	}
	public function getAdjust(Request $request,$campus){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	$eqpmts=Equipmentadjust::where('belong_campus',$campus)->get();		
		return $this->stdResponse('1',$eqpmts);
	
	}
    
    /* adminitor delete apply form*/
    public function deAdjust(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$item=Equipmentadjust::find($id);
     	$item->delete();
     	
     	return $this->stdResponse('1');
     }

}
