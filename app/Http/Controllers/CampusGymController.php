<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-10
 * Time: 10:36
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\CampusGym;
use Illuminate\Support\Facades\DB;

class CampusGymController extends Controller
{

    //获取所有校区对应场馆
    public function selectCG(Request $request){
        /* all user allowed */
        $res = $this->filter($request,[
        //    'api_token' => 'required|filled',
        ]);
        if(!$res){
            return $this->stdResponse('-1');
        }
        try{
            $res = CampusGym::get();
            if(count($res) <= 0){
                return $this->stdResponse('-5');
            }
            return $this->stdResponse('1',$res);
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }catch (\Error $error){
            return $this->stdResponse('-4');
        }
    }

    public function updateCG(Request $request){
        $res=$this->filter($request,[
            'api_token' => 'required|filled',
            'id' => 'required|filled',
        ]);
        if(!$res){
            return $this->stdResponse('-1');
        }
        /* admin api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }
        try{
            $cg = CampusGym::find($request->input('id'));
            if(count($res) == 0){
                return $this->stdResponse('-5');
            }

            $res = $cg->update($request->all());

            return $res ? $this->stdResponse('1') : $this->stdResponse('-14');
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }catch (\Error $error){
            return $this->stdResponse('-4');
        }
    }

    public function addCG(Request $request){
        $res=$this->filter($request,[
            'api_token' => 'required|filled',
            'campus' => 'required|filled',
            'campus_chinese'=>'required|filled',
            'gym_chinese'=>'required|filled',
            'type'=>'required|filled',
        ]);
        if(!$res){
            return $this->stdResponse('-1');
        }

        /* admin api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }
        try{
            $res = CampusGym::create($request->except('api_token'));

            return $res ? $this->stdResponse('1') : $this->stdResponse('-4');
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }catch (\Error $error){
            return $this->stdResponse('-4');
        }
    }

}