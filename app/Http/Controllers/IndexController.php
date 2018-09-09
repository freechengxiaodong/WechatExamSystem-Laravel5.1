<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function loginConfirm(){
        //扫码关注后来到这里,检测是否需要手动填入班级学号信息
        $openid = 'freecheng123';
        $usr = new User();
        $usrinfo = $usr->where('openid','=',"$openid")->first();
        if($usrinfo){
            //已注册过,直接考试
            //上一页$_SERVER["HTTP_REFERER"]
            echo "location.href='".'shijuan'."';</script>";
        }else{
            //尚未注册,需要先注册信息
            view('Index.resgister',[
                'openid' => $openid,
            ]);
        }
    }
    public function shijuan(){

    }
}
