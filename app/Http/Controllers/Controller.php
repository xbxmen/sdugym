<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use App\CampusGym;
use Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $stdStatus = [
        1 => '成功',
        -1 => '表单错误',
        -2 => '身份信息错误',
        -3 => 'api_token校验失败',
        -4 => '数据库操作失败',
        -5 => '记录不存在',
        -6 => '权限不足',
        -7 => '用户名或密码错误',
        -8 => '文件上传失败',
        -9 => '申请场馆失败',
        -10=> '存在时间冲突',
        -11=> '没有更多内容',
        -12=> '服务器错误',
        -13=> '申请流程已经进入下一个流程，您没有权限进行该操作~',
        -14=> '请不要重复提交',
        -15 => '两次密码不一致',
        -16 => '请等待上一级申请流程~',
        -17 => '申请流程已经截止了，请联系上一级管理员',
        -18 => '原密码不正确',
        -19 => '请等待其他财务管理员审核',
        -20 => '场馆安排中已经存在相同的记录,仔细检查,请不要重复添加',
        -21 => '场馆数量不正确'
    ];

    public $filterFail = false;
    public $backMeg;
    public $api_token =  "";
    public $user_campus = "";
    public $user_schoolnum = "";
    public $user_id = "";

    public $user_mu = "";
    public $user_zx = "";
    public $user_hj = "";
    public $user_xl = "";
    public $user_bt = "";
    public $user_qf = "";
    public $user_rj = "";
    public $user_finance = "";
    public $user_equipment = "";
    public $user_news = "";
    public $user_document = "";

    public $user_password = "";

    public function stdResponse($code='',$result='')
    {
        $hashCode = ($code || $code === 1);
        return response()->json(
            ['code'=> $hashCode ? $code : -1,'status' => $this->filterFail? $this->backMeg:$this->stdStatus[$code],'data'=>$result]
        );
    }
    //验证 表单
    public function filter(Request $request,$arr)
    {
        $validator =  Validator::make($request->all(),$arr);

        if($validator->fails())
        {
            $this->backMeg = implode($validator->errors()->all(),',');
            $this->filterFail = true;
            return false;
        }
        return true;
    }

    /*public function setPassword($password){
        $this->user_password = $password;
    }

    public function getPassword(){
        return $this->user_password;
    }*/

    //验证 user 权限
    public function check_token($api_token)
    {
        try{
            $res = DB::table('users')
                ->where('api_token','=',$api_token)
                ->where('token_expire','>',date('Y-m-d H:i:s'))
                ->leftJoin('power','users.u_id','=','power.u_id')->first();

            if(count($res) > 0){
                $this->api_token = $api_token;
                $this->user_campus = $res->campus;
                $this->user_schoolnum = $res->schoolnum;
                $this->user_id = $res->u_id;

                /*用户权限*/
                $this->user_mu = $res->mu;
                $this->user_zx = $res->zx;
                $this->user_hj = $res->hj;
                $this->user_xl = $res->xl;
                $this->user_bt = $res->bt;
                $this->user_qf = $res->qf;
                $this->user_rj = $res->rj;
                $this->user_finance = $res->finance;
                $this->user_equipment = $res->equipment;
                $this->user_news = $res->news;
                $this->user_password = $res->password;
                $this->user_document = $res->document;
           //     $this->setPassword($res->password);

                return true;
            }

        }catch (\Error $error){
            return false;
        }catch (\Exception $exception){
            return false;
        }

    }

    /*删除 token*/
    /**
     * @return string
     */
    /*获取用户信息*/
    public function getInfo()
    {
        $res = User::where('api_token',$this->api_token)
            ->where('token_expire','>',date('Y-m-d H:i:s'))->first();
        if($res->count() > 0){
            return $res;
        }
        return false;
    }

    //选择ID
    public function selectID($campus,$type,$gym){
        try{
            $res = CampusGym::where('campus',$campus)
                ->where('type',$type)
                ->where('gym',$gym)
                ->get();
            return $res;
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }catch (\Error $error){
            return $this->stdResponse('-4');
        }
    }

    //Excel文件导出功能 By Laravel学院
    public function export($cellData,$filename){
        Excel::create($filename,function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows(array_values($cellData));
            });
        })->export('xls');
    }

}
