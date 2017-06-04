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
    	$res = $this->filter($request,[
            'campus'=>'required|filled',
            'type'=>'required|filled',
            'gym'=>'required|filled',
            'gym_number'=>'required|filled',
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
        //获取 校区对应的 ID
        $res = $this->selectID($request->input('campus'),$request->input('type'),$request->input('gym'));
        if(count($res) == 0){
            return $this->stdResponse("-5");
        }
        $ID = $res[0]->id;

        $str = $request->all();
        $str['campus_gym_id'] = $ID;
    	try{
            $app = ApplicationInside::create($str);
            return $app ? $this->stdResponse('1') : $this->stdResponse('-4');

        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }catch (\Error $error){
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
            'end'=>'required|filled|date_format:"Y-m-d"',
            'type'=>'required|filled',
        ]);
        if(!$res)
        {
            return $this->stdResponse('-1');
        }

        try{
            /*获取结果*/
            //获取 校区对应的 ID
            $res = $this->selectID($campus,$request->input('type'),$gym);
            if(count($res) == 0){
                return $this->stdResponse("-5");
            }
            $ID = $res[0]->id;

            $schedules = ApplicationInside::whereBetween('time',array($request->input('start'),$request->input('end')))
                ->leftJoin('campus_gym','applications_inside.campus_gym_id','=','campus_gym.id')
                ->where('campus_gym.id',$ID)
                ->orderBy('id','desc')
                ->select(['applications_inside.id as id','campus_gym.campus_chinese','campus_gym.gym','campus_gym.type',
                    'gym_number','time','classtime','major','content','pnumber','teacher','teacher_tel','charger',
                    'tel','money','applications_inside.remark','state','teacher_remark','applications_inside.created_at'])
                ->get();

            if(!$schedules->count() > 0){
                return $this->stdResponse("-5");
            }
            return $this->stdResponse("1",$schedules);

        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
    }

    /* administor check_apply submit,params  $id*/
     public function checkApply(Request $request,$id){
         $res = $this->filter($request,[
             'money' => 'required|filled',
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

             /*判断 申请 在哪一个流程*/
             if($power < abs($item->state)){
                 return $this->stdResponse('-13');
             }

             if($item->state < 0  && $power > abs($item->state)){
                return $this->stdResponse("-17");
             }
             if($power - abs($item->state) > 1){
                 return $this->stdResponse('-16');
             }

             $item->state = $request->input('state') > 0 ? $power : 0 - $power;
             $item->teacher_remark = $request->input('teacher_remark');
             $item->money = $request->input('money');

             //当状态 为 3 时，代表该申请表 已经被通过
           /*  if($item->state == 3){
                 $day = $item->time;
                 $classtime = $item->classtime;*/

                 /*
                  * 在这里我们直接改变 场馆的使用情况
                  * */
/*
                 $sche = Schedule::where('date',$day)
                     ->where('campus',$item->campus)
                     ->where('gym',$item->gym)
                     ->firstOrFail();

                 $pieces = explode(',',$classtime);

                 foreach ($pieces as $clst) {
                     $sche->$clst = '占用';
                 }
                 $sche->save();
             }*/

             $res = $item->save();

             DB::commit();

             return $res ? $this->stdResponse('1',"成功修改~") : $this->stdResponse('-14');
         } catch (\Error $error){
             return $this->stdResponse('-12');
         } catch (\Exception $exception){
             DB::rollback();
             return $this->stdResponse('-4');
         }
     }
     
     /* adminitor delete apply form*/
     public function delApply(Request $request,$id){

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
     public function getApplyByPhone($tel){

         $schedules = ApplicationInside::whereBetween('time',array($request->input('start'),$request->input('end')))
             ->leftJoin('campus_gym','applications_inside.campus_gym_id','=','campus_gym.id')
             ->where('campus_gym.id',$ID)
             ->orderBy('id','desc')
             ->select(['applications_inside.id as id','campus_gym.campus_chinese','campus_gym.gym','campus_gym.type',
                 'gym_number','time','classtime','major','content','pnumber','teacher','teacher_tel','charger',
                 'tel','money','applications_inside.remark','state','teacher_remark','applications_inside.created_at'])
             ->get();

         if(count($schedules) > 0){
            return $this->stdResponse('1',$schedules);
         }else{
             return $this->stdResponse('-5');
         }



     }
     /*导出申请表*/
    public function exportApply(Request $request){
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

        $schedules = ApplicationInside::where('tel','=',$tel)
            ->leftJoin('campus_gym','applications_inside.campus_gym_id','=','campus_gym.id')
            ->orderBy('id','desc')
            ->select(['applications_inside.id as id','campus_gym.campus_chinese','campus_gym.type','campus_gym.gym',
                'gym_number','time','classtime','major','content','pnumber','teacher','teacher_tel','charger','tel',
                'money','applications_inside.remark','state','applications_inside.created_at'])
            ->get();

        if(count($schedules) == 0){
            return $this->stdResponse('-5');
        }
        $schedules->prepend(['序号','校区','场地分类','场地','申请的场地数量','使用场地日期','使用具体节次','申请学院','活动内容',
            '活动人数','学院老师','老师电话','负责人姓名','负责人电话','场地使用费用','申请表备注','申请表状态','提交申请表的日期']);

        $this->export($schedules->toArray(),$request->input('start').'到'.$request->input('end').'校内申请表导出信息');

    }


    /****************************************  out application  ********************************************/
    /* train apply form submit */
    public function  trainDoApply(Request $request){
     /* all visitor allowed*/
   
      	$filter = $this->filter($request,[
      		'campus'=>'required|size:2|filled',
            'type'=>'required|filled',
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

        //获取 校区对应的 ID
        $res = $this->selectID($request->input('campus'),$request->input('type'),$request->input('gym'));
        if(count($res) == 0){
            return $this->stdResponse("-5");
        }
        $ID = $res[0]->id;

        $str = $request->all();
        $str['campus_gym_id'] = $ID;

      	try{
            $item = ApplicationOutside::create($str);

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

        /*检查表单*/
        $res = $this->filter($request,[
            'start'=>'required|filled|date_format:"Y-m-d"',
            'end'=>'required|filled|date_format:"Y-m-d"',
            'type'=>'required|filled'
        ]);
        if(!$res)
        {
            return $this->stdResponse('-1');
        }


        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        /*获取结果*/
        try{
            $schedules = ApplicationOutside::whereBetween('time',array($request->input('start'),$request->input('end')))
                ->leftJoin('campus_gym','applications_outside.campus_gym_id','=','campus_gym.id')
                ->where('campus_gym.campus',$campus)
                ->where('campus_gym.type',$request->input('type'))
                ->where('campus_gym.gym',$gym)
                ->orderBy('id','desc')
                ->select(['applications_outside.id as id','campus_gym.campus_chinese','campus_gym.gym','campus_gym.type',
                    'gym_number','department','classtime','content','time','charger','tel','money',
                    'applications_outside.remark','state','teacher_remark','applications_outside.created_at'])
                ->get();

            if(!$schedules->count() > 0){
                return $this->stdResponse("-5");
            }
            return $this->stdResponse("1",$schedules);

        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
    }
    
    public function trainCheckApply(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$res=$this->filter($request,[
            'money' => 'required|filled',
            'state' => 'required|max:2|filled',
            'teacher_remark' => 'required|filled',
        ]);
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
            $item->money = $request->input('money');
            //当状态 为 3 时，代表该申请表 已经被通过
           /* if($item->state == 3){
                $day = $item->time;
                $classtime = $item->classtime;*/

                /*
                 * 在这里我们直接改变 场馆的使用情况
                 * */

            /*    $sche = Schedule::where('date',$day)
                    ->where('campus',$item->campus)
                    ->where('gym',$item->gym)
                    ->firstOrFail();

                $pieces = explode(',',$classtime);

                foreach ($pieces as $clst) {
                    $sche->$clst = '占用';
                }
                $sche->save();
            }*/

            $res = $item->save();

            DB::commit();
            return $res ? $this->stdResponse('1') : $this->stdResponse('-4');
        }catch (\Exception $exception){
            DB::rollback();
            return $this->stdResponse('-12');
        }
    }

     /* adminitor delete train apply form*/
     public function trainDelApply(Request $request,$id){
     	 /* administor api_token checked*/
         if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
         }

         try{

             $item = ApplicationOutside::find($id);

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
    public function getTrainApplyByPhone($tel){
        try{
            $item = ApplicationOutside::where('tel',$tel)
                ->leftJoin('campus_gym','applications_outside.campus_gym_id','=','campus_gym.id')
                ->orderBy('id','desc')
                ->select(['applications_outside.id as id','campus_gym.campus_chinese','campus_gym.gym','campus_gym.type',
                    'gym_number','department','classtime','content','time','charger','tel','money','applications_outside.remark','state','applications_outside.created_at'])
                ->get();

            if(count($item) > 0){
                return $this->stdResponse('1',$item);
            }else{
                return $this->stdResponse('-5');
            }
        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }catch (\Error $error){
            return $this->stdResponse('-12');
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