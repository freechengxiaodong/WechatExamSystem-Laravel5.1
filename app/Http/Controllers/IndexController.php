<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function loginConfirm(){
        //扫码关注后来到这里,检测是否需要手动填入班级学号信息
        $openid = 'freecheng123';
        $usr = new Student();
        $usrinfo = $usr->where('openid','=',"$openid")->first();
        if($usrinfo){
            //已注册过,直接考试
            //上一页$_SERVER["HTTP_REFERER"]
            echo "location.href='".'shijuan'."';</script>";
        }else{
            //尚未注册,需要先注册信息
            return view('Index.resgister',[
                'openid' => $openid,
            ]);
        }
    }
    public function shijuan(){

    }
}
