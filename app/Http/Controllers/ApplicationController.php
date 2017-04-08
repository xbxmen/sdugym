<?php
/**
 * Created by PhpStorm.
 * User: zhaoshuai
 * Date: 17-3-29
 * Time: 下午12:56
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    //显示 校区场馆的安排情况
    public function showData(Request $request,$campus,$gym){

    }

    //录入 每个校区场馆的安排情况
    public function logData(Request $request,$campus,$gym){
        $excelReader = new ExcelController();

        return $excelReader->import("qwe.xlsx");
    }

    //
}