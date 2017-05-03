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

class FinanceController extends Controller{


   public function addFinance(Request $request){

       $res = $this->filter($request,[
           'department'=>'required|max:16',
           'content'=>'required|max:255',
           'money'=>'required|integer',
           'billing_time'=>'required|date_format:Y-m-d',
           'admin' => 'required|filled',
           'remark'=>'required|string|max:255',
           'campus'=>'required|string|max:2',
       ]);
       if(!$res) return $this->stdResponse('-1');

       //验证用户 token
       if(!$this->check_token($request->input('api_token')))
       {
           return $this->stdResponse(-3);
       }

       if($this->user_finance != 1){
           return $this->stdResponse('-6');
       }

       try{

           $obj = Finance::create($request->except('api_token'));

           $obj->u_id = $this->user_id;
           $obj->save();
           return $this->stdResponse("1");

       }catch (\Exception $exception){
           return $this->stdResponse("-12");
       }
   }
   
    public function editFinance(Request $request,$id){
        $res = $this->filter($request,[
            'department'=>'required|max:16',
            'content'=>'required|max:255',
            'money'=>'required|integer',
            'billing_time'=>'required|date_format:Y-m-d',
            'admin' => 'required|filled',
            'remark'=>'required|string|max:255',
            'campus'=>'required|string|max:2',
        ]);
        if(!$res) return $this->stdResponse('-1');

        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse(-3);
        }

        if($this->user_finance != 1){
            return $this->stdResponse('-6');
        }

        try{
            $res = Finance::findOrFail($id)
                    ->update($request->except('api_token'));

            return $this->stdResponse("1");
        }catch (\Exception $exception){
            return $this->stdResponse("-12");
        }
   }

   /*删除 财务*/
   public function deFinance(Request $request,$id){
       //验证用户 token
       if(!$this->check_token($request->input('api_token')))
       {
           return $this->stdResponse(-3);
       }

       	if($this->user_finance != 1){
       	    return $this->stdResponse('-6');
        }

        try{
            $obj = Finance::find($id);

            if(count($obj) == 0){
                return $this->stdResponse('-5');
            }

            $res = $obj->delete();

            return $res? $this->stdResponse("1") : $this->stdResponse("-4");

        }catch (\Exception $exception){
       	    return $this->stdResponse('-12');
        }
   }

   /*
    * 获取财务条目
    * */
   public function showFinance(Request $request){
       $filter=$this->filter($request,[
           'page'=>'required|filled|numeric',
           'rows'=>'required|filled|numeric'
       ]);
       if(!$filter) return $this->stdResponse('-1');

       //验证用户 token
       if(!$this->check_token($request->input('api_token')))
       {
           return $this->stdResponse(-3);
       }

       if($this->user_finance != 1){
           return $this->stdResponse('-6');
       }

       $finances = Finance::orderBy('id','desc')
            ->paginate($request->rows);

       return $this->stdResponse("1",$finances);

   }
}