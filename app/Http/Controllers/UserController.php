<?php
/**
 * Created by PhpStorm.
 * User: zhaoshuai
 * Date: 17-3-26
 * Time: 下午6:13
 */
/*
 *       1 => 'OK',
        -1 => '表单错误',
        -2 => '身份信息错误',
        -3 => 'api_token校验失败',
        -4 => '数据库操作失败',
        -5 => '记录不存在',
        -6 => '权限不足'
 *
 * */

namespace App\Http\Controllers;


use App\User;
use App\Power;
use Illuminate\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //创建 管理员
    public function create(Request $request)
    {
        $res = $this->filter($request,[
            'schoolnum'=>'required|unique:users|numeric|digits:12|filled',
            'password'=>'required|digitsbetween:6,60|filled',
            'campus'=>'required|filled',
            'realname'=>'required|filled',
            'api_token'=>'required|filled'
        ]);
        if(!$res)
        {
            return $this->stdResponse();
        }
        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse(-3);
        }
        /*创建用户*/
        if($this->user_schoolnum = "root")
        {
            DB::beginTransaction();
            try{
                $user = new User();
                $user->schoolnum = $request->input('schoolnum');
                $user->password = md5($request->input('password')."#".$request->input('schoolnum'));
                $user->campus = $request->input('campus');
                $user->realname = $request->input('realname');
                $user->save();

                $power = new Power();
                $power->u_id = $user->u_id;
                $power->save();

                DB::commit();
                return $this->stdResponse(1);
            }catch (\Exception $exception){
                DB::rollback();
                return $this->stdResponse("-4");
            }
        }
        return $this->stdResponse("-6");
    }

    //登录接口
    public function login(Request $request)
    {

        //check form data
        $res = $this->filter($request,[
            'schoolnum'=>'required',
            'password'=>'required',
        ]);
        if(!$res)
        {
            return $this->stdResponse();
        }

        //check schoolnum and password
        $user = User::where('password',md5($request->input('password')."#".$request->input('schoolnum')))
            ->where('schoolnum',$request->input('schoolnum'))->first();
        if(!count($user) > 0)
        {
            return $this->stdResponse('-7');
        }
        //success
        if($user->token_expire < date('Y-m-d H:i:s'))
        {
            $user->api_token = Crypt::encrypt($user->u_id."&".time());
            $user->token_expire = date('Y-m-d H:i:s',strtotime("+24 hour"));
            $user->save();
        }
        return $this->stdResponse('1',$user->api_token);
    }

    //用户个人资料
    public function info(Request $request)
    {
        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse("-3");
        }
        return $this->stdResponse('1', $this->getInfo());
    }

    /*
     * 编辑个人信息接口
     *
     * */
    public function setinfo(Request $request)
    {
        //验证 表单
        $res = $this->filter($request,[
            'tel'=>'required|filled|digits:11',
            'api_token'=>'required|filled'
        ]);
        if(!$res){
            return $this->stdResponse();
        }
        $res = User::where('api_token',$request->input('api_token'))
                    ->update($request->all());
        if($res)
        {
            return $this->stdResponse("1");
        }
        return $this->stdResponse("-4");
    }
     /*
     *root用户 返回所有校区负责人
     *
     * */
    public function allinfo(Request $request)
    {
       /* $filter=$this->filter($request,[
            'page'=>'required|filled|numeric',
            'rows'=>'required|filled|numeric'
        ]);
        if(!$filter) return $this->stdResponse('-1');*/

        //验证用户 token
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse("-3");
        }
        $users = "";
        if($this->user_schoolnum == "root"){
            $users = DB::table('users')->leftJoin('power','users.u_id','=','power.u_id')
               /* ->where('users.schoolnum','<>','root')*/
                ->get();
        }
        return $this->stdResponse("1",$users);
    }
    /*删除管理员
     *
     * */
    public function delete(Request $request,$schoolnum)
    {
        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse("-3");
        }

        if($this->user_schoolnum != "root" ){
            return $this->stdResponse("-6");
        }
        $user = User::where('u_id',$schoolnum)
                ->get();
        if(!$user->count() > 0)
        {
            return $this->stdResponse("-5");
        }
        $res =  User::where('u_id',$schoolnum)->delete();
        if($res){
            return $this->stdResponse("1");
        }
        return $this->stdResponse("-4");
    }
    /*注销登录状态
     * */
    public function logout(Request $request)
    {
        //验证 表单
        $res = $this->filter($request,[
            'api_token'=>'required|filled'
        ]);
        if(!$res){
            return $this->stdResponse();
        }
       /* $res = User::where('api_token',$request->input('api_token'))
            ->update(['token_expire'=>'2000-01-01']);*/

        if($res == 1){
            return $this->stdResponse("1");
        }else{
            return $this->stdResponse("-4");
        }
    }
    /*获取 当前 用户级别 和 用户权限 默认是 1
     *
     * */
    public function getPower(Request $request)
    {
        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse("-3");
        }

        return $this->stdResponse("1",Power::find($this->user_id));
    }

    /*
     * root 用户为 财务管理员添加权限或删除权限  通过传递的id的不同
     * */
    public function changePower(Request $request,$schoolnum)
    {
        $res = $this->filter($request,[
            'api_token'=>'required|filled'
        ]);
        if(!$res)
        {
            return $this->stdResponse();
        }
        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse(-3);
        }
        if($this->user_schoolnum != "root")
        {
            return $this->stdResponse("-6");
        }

        try{
            Power::where('u_id',$request->input('u_id'))
                ->update($request->except('api_token'));
            return $this->stdResponse("1");
        }catch (\Exception $exception){
            return $this->stdResponse("-4");
        }
    }

}