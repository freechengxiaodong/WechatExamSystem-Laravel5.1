<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::any('/','WechatController@serve');
Route::any('/loginConfirm','IndexController@loginConfirm');


Route::group(['middleware' => ['wechat.oauth']], function () {
	
});

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
Route::controller('index','Admin\IndexController');//后台首页
Route::controller('student','Admin\StudentsController');//后台学生管理
Route::controller('teacher','Admin\TeachersController');//后台老师管理
Route::controller('test','Admin\TestsController');//后台题库管理	

