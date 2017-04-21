<?php
/**
 * Created by PhpStorm.
 * User: zhaoshuai
 * Date: 17-3-29
 * Time: 下午12:56
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Schedule;

class ScheduleController extends Controller
{
    //显示 校区场馆的安排情况
    public function showData(Request $request,$campus,$gym){
        /*检查表单*/
        $res = $this->filter($request,[
            'start'=>'required|filled|date_format:"Y-m-d"',
            'end'=>'required|filled|date_format:"Y-m-d"'
        ]);
        if(!$res)
        {
            return $this->stdResponse();
        }
        /*获取结果*/
        $schedules = Schedule::where('campus',$campus)
            ->where('gym',$gym)
            ->whereBetween('date',array($request->input('start'),$request->input('end')))
            ->get();
        if(!$schedules->count() > 0){
            return $this->stdresponse("-5");
        }
        return $this->stdresponse("1",$schedules);
    }

    //更新 校区场馆的安排情况
    public function updateData(Request $request,$campus,$gym){
        $res = $this->filter($request,[
            'date'=>'required|filled|date_format:"Y-m-d"',
            'api_token'=>'required|filled'
        ]);
        if(!$res)
        {
            return $this->stdResponse();
        }
        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse("-3");
        }
        if(!$this->user_campus == $campus && $this->user_grade > 1){
            return $this->stdresponse("-6");
        }
       /* $sche = Schedule::where("date",$request->input('date'))
            ->where('campus',$campus)
            ->where('gym',$gym)
            ->where();*/

        //更新数据
        $res = Schedule::where("date",$request->input('date'))
            ->where('campus',$campus)
            ->where('gym',$gym)
            ->update($request->except('api_token'));
        if($res == 0){
            return $this->stdresponse("-9");
        }
        return $this->stdresponse("1");
    }
}