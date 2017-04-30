<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Excel;
use Storage;

class ExcelController extends Controller
{
    public $flag = true;
    /*
     * 导入数据
     * */
    public function import(Request $request,$campus,$gym){
        //验证用户 token
        if(!$this->check_token($request->input('api_token')))
        {
            return $this->stdResponse(-3);
        }
        if($this->user_campus != $campus){
            return $this->stdResponse("-6");
        }
        //获取文件
        $excel = $request->file("excel");
        $rules = array(
            'excel' => 'required|mimes:xlsx,xls|max:20000'
        );
        $validator = \Validator::make(array('excel'=> $excel), $rules);

        if(!$validator->passes())
        {
            return $this->stdresponse("-1");
        }
       // $path = public_path()."/schedules";
        $filename = uniqid().'.'.$excel->getClientOriginalExtension();
        $realpath=$excel->getRealPath();
           
        $bool =Storage::put($filename,file_get_contents($realpath));
        if(!$bool){
            return $this->stdResponse("-8");
        }

        $filePath = "storage/public/schedules/".$filename;
        Excel::load($filePath,function ($reader){
            $reader = $reader->getSheet(0);
            $data = $reader->toArray();
            $key = array_shift($data);
            foreach ($data as $row){
                $arrTime = explode("-",$row[0]);
                $row[0] = date("Y-m-d",mktime(0,0,0,$arrTime[0],$arrTime[1],$arrTime[2]));
                $row = array_combine($key,$row);
                DB::beginTranSacTion();
                try{
                    DB::table('schedules')->insert($row);
                    DB::commit();
                }catch (\Exception $e){
                    $this->flag = false;
                }
            }
        },'UTF-8');

        Storage::delete($filename);
        if($this->flag){
        	/*delete  upload file*/
            return $this->stdresponse("1");
        }
        return $this->stdresponse("-4");
    }
}
