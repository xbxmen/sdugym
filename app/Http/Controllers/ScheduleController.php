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
use App\CampusGym;
use Illuminate\Support\Facades\DB;


class ScheduleController extends Controller
{
    //显示 校区场馆的安排情况
    public function showData(Request $request,$campus,$gym){
        /*检查表单*/
        $res = $this->filter($request,[
            'start'=>'required|filled|date_format:"Y-m-d"',
       //     'end'=>'required|filled|date_format:"Y-m-d"',
            'type'=>'required|filled',
        ]);
        if(!$res)
        {
            return $this->stdResponse();
        }
        //获取 校区对应的 ID
        $res = $this->selectID($campus,$request->input('type'),$gym);
        if(count($res) == 0){
            return $this->stdResponse("-5");
        }
        $ID = $res[0]->id;
        /*获取结果*/
        $schedules = Schedule::where('campus_gym_id',$ID)
            ->where('date',$request->input('start'))
            ->get();
        if(!$schedules->count() > 0){
            return $this->stdresponse("-5");
        }
        return $this->stdresponse("1",$schedules);
    }

    //更新 校区场馆的安排情况
    public function updateData(Request $request,$campus){
        //data  sd_id    oue   two  three
        $res = $this->filter($request,[
            'records'=>'required|filled',
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
        if(!$this->user_campus == $campus && $this->user_grade > 1)
        {
            return $this->stdresponse("-6");
        }

        try{
            $data = json_decode($request->input('records'));
            $res = Schedule::where("sd_id",$data->sd_id)->get();

            if(count($res) == 0){
                return $this->stdresponse("-5");
            }

            /* $ID = $res[0]->campus_gym_id;

             $cg = CampusGym::where('id',$ID)->get();
             $number = preg_replace('/([\x80-\xff]*)/i','',$cg[0]->number);
             */
            $arr = $this->o2a($data);

            if(empty($arr)){
                return $this->stdresponse("-1");
            }

            $res = Schedule::where("sd_id",$data->sd_id)
                ->update($arr);
            return $res ? $this->stdresponse('1') : $this->stdresponse('-4');


        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }catch (\Error $error){
            return $this->stdResponse('-4');
        }

    }

    //对象转化成数组
    public function o2a($object){
        $str = array();
        foreach ($object as $key => $value){

            if($key != "sd_id"){
                $str[$key] = $value;
            }
        }
        return $str;
    }


    //生成表格
    public function addData(Request $request,$campus,$gym){
        $res = $this->filter($request,[
            'start'=>'required|filled|date_format:"Y-m-d"',
        //    'end'=>'required|filled|date_format:"Y-m-d"',
            'type'=>'required|filled',
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
        if(!$this->user_campus == $request->input('campus') && $this->user_grade > 1){
            return $this->stdresponse("-6");
        }
        $res = $this->selectID($campus,$request->input('type'),$gym);

        if(count($res) == 0){
            return $this->stdResponse("-5",'校区场馆不存在');
        }

        $ID = $res[0]->id;
        $number =  preg_replace('/([\x80-\xff]*)/i','',$res[0]->number);
        $schedule = Schedule::where('campus_gym_id',$ID)
            ->where('date','=',$request->input('start'))
         //   ->where('date','<=',$request->input('end'))
            ->orderBy('date','desc')
            ->get();
        if(count($schedule) > 0){
            return $this->stdResponse('-20',$schedule[0].'之前的已经安排');
        }
        $str = "1";
        //我们需要一个字符串设置状态  1代表开放 2代表已安排 3代表体育教学
        $array = Array(
                "date"=>"",
                "week"=>"",
                "one"=>str_pad($str,$number,'1'),
                "two"=>str_pad($str,$number,'1'),
                "three"=>str_pad($str,$number,'1'),
                "four"=>str_pad($str,$number,'1'),
                "five"=>str_pad($str,$number,'1'),
                "six"=>str_pad($str,$number,'1'),
                "seven"=>str_pad($str,$number,'1'),
                "eight"=>str_pad($str,$number,'1'),
                "nine"=>str_pad($str,$number,'1'),
                "ten"=>str_pad($str,$number,'1'),
                "eleven"=>str_pad($str,$number,'1'),
                "twelve"=>str_pad($str,$number,'1'),
                "thirteen"=>str_pad($str,$number,'1'),
                "fourteen"=>str_pad($str,$number,'1'),
                "campus_gym_id"=>$ID
        );

        try{
            DB::beginTranSacTion();
            for($i = $request->input('start'); $i <= $request->input('end');){
                $array['date'] = $i;
                $array['week'] = $this->get_week($i);

                $i = date("Y-m-d",strtotime($i."+1 day"));

                try{
                    Schedule::insert($array);
                }catch (\Exception $e){
                    DB::rollback();
                    return $this->stdResponse('-4');
                }
            }
            DB::commit();
            return $this->stdResponse('1');

        }catch (\Exception $exception){
            DB::rollback();
            return $this->stdResponse('-4');
        }catch (\Error $error){
            DB::rollback();
            return $this->stdResponse('-12');
        }

    }
//通过日期获取是星期几
    public function get_week($date){
        //强制转换日期格式
        $date_str=date('Y-m-d',strtotime($date));

        //封装成数组
        $arr=explode("-", $date_str);

        //参数赋值
        //年
        $year=$arr[0];

        //月，输出2位整型，不够2位右对齐
        $month=sprintf('%02d',$arr[1]);

        //日，输出2位整型，不够2位右对齐
        $day=sprintf('%02d',$arr[2]);

        //时分秒默认赋值为0；
        $hour = $minute = $second = 0;

        //转换成时间戳
        $strap = mktime($hour,$minute,$second,$month,$day,$year);

        //获取数字型星期几
        $number_wk=date("w",$strap);

        //自定义星期数组
        $weekArr=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");

        //获取数字对应的星期
        return $weekArr[$number_wk];
    }

}