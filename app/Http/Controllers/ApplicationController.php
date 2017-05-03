<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-10
 * Time: 10:36
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\ApplicationInside;
use App\ApplicationOutside;
use App\Schedule;
use Illuminate\Support\Facades\DB;
use MongoDB\Driver\ReadConcern;

class ApplicationController extends Controller
{
	
	/***************************** campus activities application *******************************************/
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
            'pnumber'=>'required|filled',
            'teacher'=>'required|filled|string',
            'teacher_tel'=>'required|filled|digits:11',
            'charger'=>'required|filled|string',
            'tel'=>'required|filled|digits:11',
            'remark'=>'required|filled|string|max:500'
    	]);
    	if(!$res){
    		return $this->stdResponse('-1');
    	}

    	try{
            $app = ApplicationInside::create($request->all());
            if($app){
                return $this->stdResponse('1');
            }else{
                return $this->stdResponse('-4');
            }
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
    }

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
        $schedules = ApplicationInside::where('campus',$campus)
            ->where('gym',$gym)
            ->whereBetween('time',array($request->input('start'),$request->input('end')))
            ->orderBy('id','desc')
            ->get();

        if(!$schedules->count() > 0){
            return $this->stdResponse("-5");
        }
        return $this->stdResponse("1",$schedules);
    }


    /*修改校内申请表 费用 */
    public function editApply(Request $request,$id){
        $res = $this->filter($request,[
            'money' => 'required|filled',
            'api_token' => 'required|filled',
        ]);
        if(!$res){
            return $this->stdResponse('-1');
        }
        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }
        try{
            $item = ApplicationInside::find($id);

            $power = $this->checkPower($item->campus);
            if($power == 0){
                return $this->stdResponse('-6',"您没有该校区的管理权限~~");
            }

            /* 判断当前流程 */
            if($power < abs($item->state)){
                return $this->stdResponse('-13');
            }
            /* 判断是否越级 */
            if($power - abs($item->state) > 1){
                return $this->stdResponse('-14');
            }

            $item->money = $request->money;

            $res = $item->save();

            return $res ? $this->stdResponse("1") : $this->stdResponse("-14");

        } catch (\Exception $exception){
            return $this->stdResponse('-4');
        } catch (\Error $error){
            return $this->stdResponse('-12');
        }
    }

    /* administor check_apply submit,params  $id*/
     public function checkApply(Request $request,$id){
         $res = $this->filter($request,[
             'state' => 'required|max:2|filled',
             'teacher_remark' => 'required|filled',
         ]);
         if(!$res){
     		return $this->stdResponse('-1');
         }
         /* administor api_token checked*/
         if(!$this->check_token($request->input('api_token'))){
             return $this->stdResponse('-3');
         }

         DB::beginTransaction();
         try{
             $item = ApplicationInside::find($id);

             $power = $this->checkPower($item->campus);
             if($power == 0){
                 return $this->stdResponse('-6',"您没有该校区的管理权限~~");
             }

             if($power < abs($item->state)){
                 return $this->stdResponse('-13');
             }

             if($power - abs($item->state) > 1){
                 return $this->stdResponse('-14');
             }

             $item->state = $request->input('state') > 0 ? $power : 0 - $power;
             $item->teacher_remark = $request->input('teacher_remark');

             //当状态 为 3 时，代表该申请表 已经被通过
             if($item->state == 3){
                 $day = $item->time;
                 $classtime = $item->classtime;

                 /*
                  * 在这里我们直接改变 场馆的使用情况
                  * */

                 $sche = Schedule::where('date',$day)
                     ->where('campus',$item->campus)
                     ->where('gym',$item->gym)
                     ->firstOrFail();

                 $pieces = explode(',',$classtime);

                 foreach ($pieces as $clst) {
                     $sche->$clst = '占用';
                 }
                 $sche->save();
             }

             $res = $item->save();

             DB::commit();

             return $res ? $this->stdResponse('1') : $this->stdResponse('-14');
         } catch (\Error $error){
             return $this->stdResponse('-12');
         } catch (\Exception $exception){
             DB::rollback();
             return $this->stdResponse('-4');
         }
     }
     
     /* adminitor delete apply form*/
     public function deApply(Request $request,$id){

         /* administor api_token checked*/

         if(!$this->check_token($request->input('api_token'))){
             return $this->stdResponse('-3');
         }
         try{
             $item = ApplicationInside::find($id);

             $power = $this->checkPower($item->campus);
             if($power == 0){
                 return $this->stdResponse('-6',"您没有该校区的管理权限~~");
             }

             if($power <= abs($item->state)){
                 return $this->stdResponse('-13');
             }
             $res = $item->delete();
             return $res ? $this->stdResponse('1') : $this->stdResponse('-4');

         }catch (\Error $error){
             return $this->stdResponse('-12');

         }catch (\Exception $exception){
             return $this->stdResponse('-4');
         }
     }

     /*通过手机号 查询*/
     public function getOneApply($tel){
        /* $res=$this->filter($tel,[
             'tel'=>'required|filled|digits:11',
         ]);
         if(!$res){
             return $this->stdResponse('-1');
         }*/

         $item = ApplicationInside::where('tel','=',$tel)
             ->orderBy('id','desc')
            ->get();

         if(count($item) > 0){
            return $this->stdResponse('1',$item);
         }else{
             return $this->stdResponse('-5');
         }
     }
    /****************************************  out application  ********************************************/
    /* train apply form submit */
    public function  trainDoApply(Request $request){
     /* all visitor allowed*/
   
      	$filter = $this->filter($request,[
      		'campus'=>'required|size:2|filled',
       		'gym'=>'required|filled',
       		'department'=>'required|filled|string',
    		'content'=>'required|string|filled',
    		'time'=>'required|filled|date_format:"Y-m-d"',
    		'classtime'=>'required|filled|string',
    		'charger'=>'required|filled|string|max:4',
    		'tel'=>'required|filled|digits:11',
      	]);
      	if(!$filter)
      		return $this->stdResponse('-1');

      	try{
            $item = ApplicationOutside::create($request->all());
            if($item){
                return $this->stdResponse('1');
            }else{
                return $this->stdResponse('-4');
            }
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
    }

    /* show train application for administor */
    public function trainShowApply(Request $request,$campus,$gym){

        $filter = $this->filter($request,[
            'campus'=>'required|size:2|filled',
            'gym'=>'required|filled',
        ]);
        if(!$filter)
            return $this->stdResponse('-1');

        /* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

    	$applications = ApplicationOutside::where('campus',$campus)
            ->where('gym',$gym)
            ->orderBy('id','desc')
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


        DB::beginTransaction();
        try{
            $item = ApplicationOutside::findOrFail($id);

            $power = $this->checkPower($item->campus);
            if($power == 0){
                return $this->stdResponse('-6',"您没有该校区的管理权限~~");
            }

            if($power < abs($item->state)){
                return $this->stdResponse('-13');
            }

            if($power - abs($item->state) > 1){
                return $this->stdResponse('-14');
            }


            $item->state = $request->input('state') > 0 ? $power : 0 - $power;
            $item->teacher_remark = $request->input('teacher_remark');

            //当状态 为 3 时，代表该申请表 已经被通过
            if($item->state == 3){
                $day = $item->time;
                $classtime = $item->classtime;

                /*
                 * 在这里我们直接改变 场馆的使用情况
                 * */

                $sche = Schedule::where('date',$day)
                    ->where('campus',$item->campus)
                    ->where('gym',$item->gym)
                    ->firstOrFail();

                $pieces = explode(',',$classtime);

                foreach ($pieces as $clst) {
                    $sche->$clst = '占用';
                }
                $sche->save();
            }

            $res = $item->save();

            DB::commit();

            return $res ? $this->stdResponse('1') : $this->stdResponse('-4');
        }catch (\Exception $exception){
            DB::rollback();
            return $this->stdResponse('-12');
        }
     	
     	$item=ApplicationOutside::find($id);
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

         try{

             $item=ApplicationOutside::find($id);

             $power = $this->checkPower($item->campus);

             if($power == 0){
                 return $this->stdResponse('-6',"您没有该校区的管理权限~~");
             }

             if($power <= abs($item->state)){
                 return $this->stdResponse('-13');
             }

             $res = $item->delete();

             return $res ? $this->stdResponse('1') : $this->stdResponse('-4');
         }catch (\Exception $exception){
             return $this->stdResponse('-12');
         }
     }

    /*通过手机号 查询*/
    public function getOneTrainApply($tel){
        $item = ApplicationOutside::where('tel','=',$tel)
            ->orderBy('id','desc')
            ->get();

        if(count($item) > 0){
            return $this->stdResponse('1',$item);
        }else{
            return $this->stdResponse('-5');
        }
    }

     /*
      * 获取用户场馆管理权限的方法
      * */
     public function checkPower($school){
         $power = 0;

         switch ($school){
             case 'mu':
                 $power = $this->user_mu;
                 break;
             case 'zx':
                 $power = $this->user_zx;
                 break;
             case 'bt':
                 $power = $this->user_bt;
                 break;
             case 'rj':
                 $power = $this->user_rj;
                 break;
             case 'hj':
                 $power = $this->user_hj;
                 break;
             case 'xl':
                 $power = $this->user_xl;
                 break;
             case 'qf':
                 $power = $this->user_qf;
                 break;
         }
         return $power;
     }
}