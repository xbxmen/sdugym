<?php

namespace App\Http\Controllers;


use App\User;
use Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
        -10=> '存在时间冲突'
    ];

    public $filterFail = false;
    public $backMeg;
    public $api_token =  "";
    public $user_grade = "";
    public $user_permission = "";
    public $user_campus = "";
    
    
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
    //验证 user 权限
    public function check_token($api_token)
    {
        $res = User::where('api_token',$api_token)
            ->where('token_expire','>',date('Y-m-d H:i:s'))->first();

        if(count($res) > 0){
            $this->api_token = $api_token;
            $this->user_grade = $res->grade;
            $this->user_permission = $res->permission;
            $this->user_campus = $res->campus;
            return true;
        }
        return false;
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

}
