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
            echo "location.href='".'/shijuan'."';</script>";
        }else{
            //尚未注册,需要先注册信息
            return view('Index.resgister',[
                'openid' => $openid,
            ]);
        }
    }
    public function studentInfoInsert(Request $request){
        $res = DB::table('students')->insert([
            'openid' => 'john@example.com',
            'name' => $request->input('name'),
            'class' => $request->input('class'),
            'number' => $request->input('number'),
            ]);
        if ($res){
            echo "location.href='".'/shijuan'."';</script>";
        }else{
            echo "<script>alert('非法参数,请新填写信息!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        }

    }
    public function shijuan(){
        //检测教师是否有推出试卷


        //有测试的话根据教师本次的选题进行试题生成
        $obj = DB::table('tests')->where('id', '>' ,'0')->get();
        return view('Index.shijuan',[
           'obj' => $obj,
        ]);
    }
    public function dafen(Request $request){
        $dui = $request->input('dui');
        $count = $request->input('count');
        $score = (integer)($dui/$count*100);
        return view('Index.chengji',[
            'dui' => $dui,
            'count' => $count,
            'score' => $score,
        ]);
    }
}
