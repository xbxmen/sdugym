<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('index');
});


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

/*Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');
});*/

/*用户 有关路由 以“/api/user”开头*/
Route::group(['prefix' => '/api/users'],function (){

    Route::post('/i/auth',"UserController@login");
    ///auth
    Route::delete('/i/auth','UserController@logout');/*注销登录*/
    //delete auth

    Route::post('/','UserController@create');/*管理员 创建用户*/
    Route::get('/i/info','UserController@info'); /*返回用户个人信息*/
    ///:userid/info
    Route::put('/i/info','UserController@setinfo');/*修改个人信息*/
    //put
    Route::get('/info','UserController@allinfo');/*获取当前用户权限下面所有用户的信息*/
    ////get  /users/i/users
    Route::delete('/i/people/{schoolnum}','UserController@delete');/*删除 权限下的用户*/
    ///user/i/people/::peropleid
    Route::get('/i/gp','UserController@getGP');/* h*/
    // post users/
    /*内务管理员*/
 //   Route::post('/addfin','UserController@addFin');/*root 创建财务管理员*/
    Route::put('/i/people/{schoolnum}/auth','UserController@powerFin');/*root 为财务管理员添加权限或删除权限*/
});

/*场馆日常管理表格 以/api/schedules 开头*/
Route::group(['prefix' => '/api/schedules/campus/{campus}/gym/{gym}'],function (){
    Route::get('/','ApplicationController@showData'); /*根据 日期获取 校区场馆的使用情况*/
    Route::post('/','ApplicationController@logData'); /*录入数据*/
    Route::put('/','ApplicationController@setData'); /*修改 场馆的使用情况*/
});


/*场馆申请表格 申请*/


/*文件管理接口*/
Route::group(['prefix' => '/api/documents/'],function (){

    Route::post('/','DocumentController@upload');
});
