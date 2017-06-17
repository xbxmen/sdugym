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
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\DB;
class EquipmentController extends Controller{
    /************************* Equipment manage  prefix /api/equipments**************************
     * @param Request $request
     * @return
     */

	public function postRegistry(Request $request){

    	$res = $this->filter($request,[
            'campus'=>'required|filled',
            'campus_chinese'=>'required|filled',
            'type'=>'required|filled',
            'gym'=>'required|filled',
            'equipment_name'=>'required|filled',
            'buy_date'=>'required|filled|date_format:"Y-m-d',
            'buy_number'=>'required|filled',
            'price'=>'required|filled',
            'remark'=>'required|filled|string|max:255'
    	]);

        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        if($this->user_equipment != 2){
            return $this->stdResponse('-6');
        }

    	if(!$res){
    		return $this->stdResponse('-1');
    	}
        $arr = $request->except('api_token');
    	$arr['u_id'] = $this->user_id;

    	try{
            $res = Equipment::create($arr);
            return $res? $this->stdResponse('1') : $this->stdResponse('-4');

        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }
	}

	//修改 器材
	public function putRegistry(Request $request,$id){
        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        if($this->user_equipment != 2){
            return $this->stdResponse('-6');
        }

        $res = $this->filter($request,[
        //    'buy_date'=>'required|filled|date_format:"Y-m-d',
            'buy_number'=>'required|filled|integer',
            'in_number'=>'required|integer|filled',
            'no_number'=>'required|filled|integer',
            'price'=>'required|filled|integer',
            'unit'=>'required|filled|string|max:255',
            'remark'=>'required|filled|string|max:255'
        ]);

        if(!$res){
            return $this->stdResponse('-1');
        }
        try{
            $eqp = Equipment::where('id','=',$id)
                ->update($request->except('api_token'));

            return $this->stdResponse('1',$eqp);
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
    }

    /*获取某个校区的器材*/
	public function getRegistry(Request $request,$campus){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

        if($this->user_equipment == 0){
            return $this->stdResponse('-6');
        }

        $res = Equipment::where('campus',$campus)->get();
		return $this->stdResponse('1',$res);
	
	}

    /*通过名称查询器材*/
    public function getRegistryByName(Request $request,$name){
        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        if($this->user_equipment == 0){
            return $this->stdResponse('-6');
        }

        try{
            $res = Equipment::where('equipment_name','like','%'.$name.'%')->get();

            if(count($res) == 0){
                return $this->stdResponse('-5');
            }
            return $this->stdResponse('1',$res);

        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
    }

    /* adminitor delete apply form*/
    public function delRegistry(Request $request,$id){
        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }
        if($this->user_equipment != 2){
            return $this->stdResponse('-6');
        }

        try{
            $item = Equipment::find($id);

            if(count($item) == 0){
                return $this->stdResponse('-5');
            }
            $res = $item->delete();

            return $res ? $this->stdResponse('1') : $this->stdResponse('-4');

        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
    }

    public function exportEquipment(Request $request){
        try{
            $res = $this->filter($request,[
                'start'=>'required|filled|date_format:"Y-m-d"',
                'end'=>'required|filled|date_format:"Y-m-d"',
                'campus'=>'required|filled',
            ]);
            if(!$res)
            {
                return $this->stdResponse();
            }

            /* administor api_token checked*/
            if(!$this->check_token($request->input('api_token'))){
                return $this->stdResponse('-3');
            }
            if($this->user_equipment != 2){
                return $this->stdResponse('-6');
            }
            $item = Equipment::select(['campus_chinese as 校区','gym as 场馆','equipment_name as 器材名字','buy_date as 购买时间'
                ,'buy_number as 购买数量','in_number as 在用数量','no_number as 报废数量','out_number as 调出数量',
                'unit as 单位','price as 价格','total_price as 总价格','remark as 备注'])
                ->where('campus',$request->input('campus'))
                ->where('buy_date','>=',$request->input('start'))
                ->where('buy_date','<=',$request->input('end'))
                ->get();

            if(count($item) == 0){
                return $this->stdResponse('-5');
            }

            $item->prepend(['校区','场馆','器材名字','购买时间','购买数量','在用数量','报废数量','调出数量','单位','价格','总价格','备注']);

            $this->export($item->toArray(),$request->input('start').' to '.$request->input('end').' Equipments Records');

        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
    }

 /************************* Equipment adjust  ,prefix /api/equipments/adjust ***************************/	
   	public function postAdjust(Request $request){
      	/* administor api_token checked*/

    	$res=$this->filter($request,[
    	    'id'=>'required',
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

        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        if($this->user_equipment != 2){
            return $this->stdResponse('-6');
        }

     	$arr = $request->except('api_token')->except('id');
     	$arr['u_id'] = $this->user_id;

     	DB::beginTransaction();
     	try{
            $newitem = Equipmentadjust::insert($arr);

            $res = Equipment::where('id','=',$request->id)
                ->increment('out_number',$request->use_number);

            if($newitem && $res){
                return $this->stdResponse('1');
                DB::commit();
            }else{
                return $this->stdResponse('-4');
            }
            
        }catch (\Exception $exception){
            DB::rollback();
     	    return $this->stdResponse('-4');
        }
   	}

	public function getAdjust(Request $request,$campus){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
        if($this->user_equipment != 2){
            return $this->stdResponse('-6');
        }

     	try{
            $res = Equipmentadjust::where('belong_campus',$campus)->get();

            if(count($res)){
                return $this->stdResponse('-5');
            }
            return $this->stdResponse('1',$res);

        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
	}
    
    /* adminitor delete apply form*/
    public function delAdjust(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

        if($this->user_equipment != 2){
            return $this->stdResponse('-6');
        }

        try{
            $item = Equipmentadjust::find($id);
            if(count($item) == 0){
                return $this->stdResponse('-5');
            }

            $res = $item->delete();

            return $res ? $this->stdResponse('1') : $this->stdResponse('-4');
        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
     }

}