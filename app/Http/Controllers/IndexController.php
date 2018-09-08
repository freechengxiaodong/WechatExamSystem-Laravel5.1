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
        $openid = 'freecheng';
        $usr = new User();
        $usrinfo = $usr->where('openid',eq,"$openid")->first();
        dd($usrinfo);
    }
}
