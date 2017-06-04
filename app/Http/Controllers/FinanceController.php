<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-27
 * Time: 15:57
 */
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Finance;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ExcelController;


class FinanceController extends Controller{

   public function addFinance(Request $request){

       $res = $this->filter($request,[
           'department'=>'required|max:16',
           'content'=>'required|max:255',
           'money'=>'required|integer',
           'billing_time'=>'required|date_format:Y-m-d',
           'remark'=>'required|string|max:255',
           'campus'=>'required|string|max:2',
       ]);
       if(!$res) return $this->stdResponse('-1');

       //验证用户 token
       if(!$this->check_token($request->input('api_token')))
       {
           return $this->stdResponse(-3);
       }

       if($this->user_finance != 2){
           return $this->stdResponse('-6');
       }

       try{

           $obj = Finance::create($request->except('api_token'));

           $obj->u_id = $this->user_id;
           $obj->save();
           return $res? $this->stdResponse("1") : $this->stdResponse("-4");

       }catch (\Exception $exception){
           return $this->stdResponse("-4");
       }catch (\Error $error){
           return $this->stdResponse("-12");
       }
   }
   
    public function editFinance(Request $request,$id){
        $res = $this->filter($request,[
            'department'=>'required|max:16',
            'content'=>'required|max:255',
            'money'=>'required|integer',
            'billing_time'=>'required|date_format:Y-m-d',
            'remark'=>'required|string|max:255',
            'campus'=>'required|string|max:2',
        ]);
        if(!$res) return $this->stdResponse('-1');

        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse(-3);
        }

        if($this->user_finance <= 1){
            return $this->stdResponse('-6');
        }

        try{
            $res = Finance::findOrFail($id)
                    ->update($request->except('api_token'));

            return $res? $this->stdResponse("1") : $this->stdResponse("-4");
        }catch (\Exception $exception){
            return $this->stdResponse("-4");
        }catch (\Error $error){
            return $this->stdResponse("-12");
        }
   }

   /*删除 财务*/
   public function delFinance(Request $request,$id){
       //验证用户 token
       if(!$this->check_token($request->input('api_token')))
       {
           return $this->stdResponse(-3);
       }

       	if($this->user_finance <= 1){
       	    return $this->stdResponse('-6');
        }


        try{
            $obj = Finance::find($id);

            if(count($obj) == 0){
                return $this->stdResponse('-5');
            }
            if($obj->state == -1 && $obj->del_admin == $this->user_id){
                return $this->stdResponse('-19');
            }else if($obj->state == -1 && $obj->del_admin != $this->user_id){
                $res = $obj->delete();

                return $res? $this->stdResponse("1",'删除成功') : $this->stdResponse("-4");

            }else if($obj->state == 0){
                $obj->state = -1;
                $obj->del_admin = $this->user_id;
                $res = $obj->save();

                return $res? $this->stdResponse("1",'删除成功') : $this->stdResponse("-4");
            }

        }catch (\Exception $exception){
       	    return $this->stdResponse('-4');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
   }

   /*
    * 恢复财务
    *
    * */
    public function recoverFinance(Request $request,$id){
        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse(-3);
        }

        if($this->user_finance <= 1){
            return $this->stdResponse('-6');
        }
        try{
            $obj = Finance::find($id);

            if(count($obj) == 0){
                return $this->stdResponse('-5');
            }
            if($obj->state == -1 && $obj->del_admin == $this->user_id){
                $obj->state = 0;
                $res = $obj->save();

                return $res? $this->stdResponse("1") : $this->stdResponse("-4");

            }else if($obj->state == -1 && $obj->del_admin != $this->user_id){

                return $this->stdResponse("1");

            }else if($obj->state == 0){
                return $this->stdResponse('-14');
            }

        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
    }

   /*
    * 获取财务条目
    * */
   public function showFinance(Request $request){
       $filter=$this->filter($request,[
           'page'=>'required|filled|numeric',
           'rows'=>'required|filled|numeric',
           'start'=>'required|filled|date_format:"Y-m-d"',
           'end'=>'required|filled|date_format:"Y-m-d"',
           'campus' => 'required|filled'
       ]);
       if(!$filter) return $this->stdResponse('-1');

       //验证用户 token
       if(!$this->check_token($request->input('api_token')))
       {
           return $this->stdResponse('-3');
       }

       if($this->user_finance == 0){
           return $this->stdResponse('-6');
       }

       $finances = DB::table('finances')
            ->select(['finances.id','department','content','billing_time','money','remark','finances.campus as campus','state','finances.created_at as created_at','users.realname as admin','a.realname as del_admin'])
            ->leftJoin("users","finances.u_id",'=',"users.u_id")
            ->leftJoin("users as a","finances.del_admin",'=',"a.u_id")
            ->where('finances.campus',$request->input('campus'))
            ->where('billing_time','>=',$request->input('start'))
            ->where('billing_time','<=',$request->input('end'))
            ->orderBy('id','desc')
            ->paginate($request->rows);

       return $this->stdResponse("1",$finances);

   }

   //导出财务信息
    public function exportFinance(Request $request){
        $res = $this->filter($request,[
            'start'=>'required|filled|date_format:"Y-m-d"',
            'end'=>'required|filled|date_format:"Y-m-d"',
            'campus'=>'required|filled',
            'api_token'=>'required|filled'
        ]);
        if(!$res)
        {
            return $this->stdResponse();
        }

        //验证用户 token
     /*   if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse('-3');
        }

        if($this->user_finance == 0){
            return $this->stdResponse('-6');
        }*/

        $finances = Finance::select(['finances.id','department','content','billing_time','money','users.realname as admin','remark','finances.campus as campus','finances.created_at as created_at'])
            ->leftJoin("users","finances.u_id",'=',"users.u_id")
            ->leftJoin("users as a","finances.del_admin",'=',"a.u_id")
            ->where('finances.campus',$request->input('campus'))
            ->where('billing_time','>=',$request->input('start'))
            ->where('billing_time','<=',$request->input('end'))
            ->where('finances.state','<>','-2')
            ->orderBy('id','desc')
            ->get();

        $array = ["序号",'使用单位','内容','入账时间','费用','收费员','备注','校区','记录日期'];

        $finances->prepend($array);
        //return $this->stdResponse("1",$finances);


        $this->export($finances->toArray(),$request->input('start')."到".$request->input('end').'财务导出信息');
        try{


        }catch (\Exception $exception){
            return $this->stdResponse("-4");
        }catch (\Error $error){
            return $this->stdResponse("-12");
        }
    }
}