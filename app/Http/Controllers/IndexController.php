<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\TeachersController;
use App\Student;
use App\Teacher;
use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    //授权
    public function grant()
    {
        $url=url().'/shouquan';
        $weixin=new WeixinController();
        $weixin->getUserDetail($url);
    }
    public function grantt()
    {
        $url=url().'/shouquant';
        $weixin=new WeixinController();
        $weixin->getUserDetail($url);
    }
    public function loginConfirm(Request $request){
        //扫码关注后来到这里,检测是否需要手动填入班级学号信息
        $openid = $request->session()->get('openid');
        $usr = new Student();
        $usrinfo = $usr->where('openid','=',"$openid")->first();
        if($usrinfo){
            //已注册过,直接考试
            //上一页$_SERVER["HTTP_REFERER"]
            echo "<script>location.href='".'/shijuan'."';</script>";
        }else{
            //尚未注册,需要先注册信息
            return view('Index.resgister',[
                'openid' => $openid,
            ]);
        }
    }
    public function studentInfoInsert(Request $request){
        $res = DB::table('students')->insert([
            'openid' => $request->session()->get('openid'),
            'name' => $request->input('name'),
            'number' => $request->input('number'),
            ]);
        if ($res){
            echo "<script>location.href='".'/shijuan'."';</script>";
        }else{
            echo "<script>alert('非法参数,请新填写信息!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        }

    }
    public function shijuan(Request $request){
        //检测教师是否有推出试卷
        $id = DB::table('shijuans')->max('id');
        $res = DB::table('shijuans')->find($id);
//        $shijuantime = $res->created_at;
//        $currenttime = time();
//        $hour = floor($shijuantime/3600);
//        $minute = floor(($shijuantime-3600 * $hour)/60);
//        $shijuantime1 = $hour*60+$minute;
//        $hour1 = floor($currenttime/3600);
//        $minute1 = floor(($currenttime-3600 * $hour)/60);
//        $currenttime1 = $hour1*60+$minute1;
//        $cha = $currenttime1-$shijuantime1;
//        if($cha >= 60 ){
//            $title = 'error';
//            $content = '暂无试卷!';
//            return view('warning.msg',[
//                'title' => $title,
//                'content' => $content,
//            ]);
//        }
//        if($request->session()->get('flag'=='0') ){
//            $title = 'error';
//            $content = '不可重复考试!';
//            return view('warning.msg',[
//                'title' => $title,
//                'content' => $content,
//            ]);
//        }
        $zhangjie = $res->zhangjie;
        $count = $res->count;
        //有测试的话根据教师本次的选题进行试题生成
        $openid = $request->session()->get('openid');
        $usr = new Student();
        $usrinfo = $usr->where('openid','=',"$openid")->first();
        $uid = $usrinfo->id;
        $default = DB::table('counts')->where('shijuan_id','=',$id)->Where('student_id','=',$uid)->first();
        if($default){
            $title = 'error';
            $content = '已提交试卷,不可重复考试!';
            return view('warning.msg',[
                'title' => $title,
                'content' => $content,
            ]);
        }

        $obj = DB::table('tests')
            ->where('chapter','=',"$zhangjie")
            ->orderBy(DB::raw('RAND()'))
            ->take("$count")
            ->get();
        return view('Index.shijuan',[
           'obj' => $obj,
            'user' => $usrinfo,
            'shijuanid' => $id,
        ]);
    }
    public function dafen(Request $request){
        $shijuanid = $request->input('shijuanid');
        $openid = $request->session()->get('openid');
        $usr = new Student();
        $usrinfo = $usr->where('openid','=',"$openid")->first();
        $stuid = $usrinfo->id;
        $dui = $request->input('dui');
        $count = $request->input('count');
        $score = (integer)($dui/$count*100);
        $cuo = $count-$dui;
        $default = DB::table('counts')->where('shijuan_id','=',$shijuanid)->Where('student_id','=',$stuid)->first();
        if($default){
            $title = 'error';
            $content = '已提交试卷,不可重复提交!';
            return view('warning.msg',[
                'title' => $title,
                'content' => $content,
            ]);
        }
        DB::table('counts')->insert([
            'shijuan_id' => $shijuanid,
            'student_id' => $stuid,
            'grade' => $score,
            'content' => "对".$dui."道题,错"."$cuo"."道题",
        ]);
        return view('Index.chengji',[
            'dui' => $dui,
            'count' => $count,
            'score' => $score,
            'user' => $usrinfo,
        ]);
    }
    public function createshijuan(Request $request){
        $openid = $request->session()->get('openid');
        $usr = new Teacher();
        $info = $usr->where('openid','=',"$openid")->first();
        if($info){
            echo "<script>location.href='".'/chooseshijuan'."';</script>";
        }else{
            $title = 'error';
            $content = '未检测到教师信息,请联系管理员添加!';
            return view('warning.msg',[
                'title' => $title,
                'content' => $content,
            ]);
        }
    }
    public function chooseshijuan(Request $request){
        //$obj = DB::table('tests')->where('id', '>' ,'0')->get();
        $obj = [];
        if($request->input('flag')){
            if($request->input('flag') == '1'){
                $shijuanid = DB::table('shijuans')->max('id');
                $obj = DB::table('counts')->where('shijuan_id','=',$shijuanid)->orderBy('grade','DESC')->get();
            }
        }
        return view('Index.chooseshijuan',[
            'obj' => $obj,
        ]);
    }
    public function shijuanInsert(Request $request){
        $zhangjie = $request->input('zhangjie');
        $count = $request->input('count');
        $res = DB::table('shijuans')->insert([
            'zhangjie' => $zhangjie,
            'count' => $count,
        ]);
        if ($res){
            $title = 'success';
            $content = '试题已生成!';
            return view('warning.msg',[
                'title' => $title,
                'content' => $content,
            ]);
        }else{
            echo "<script>alert('数据写入失败!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        }
    }

}
