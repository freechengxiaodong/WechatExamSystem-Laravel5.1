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
        $zhangjie = $res->zhangjie;
        $count = $res->count;
        //有测试的话根据教师本次的选题进行试题生成
        $openid = $request->session()->get('openid');
        $usr = new Student();
        $usrinfo = $usr->where('openid','=',"$openid")->first();

        $obj = DB::table('tests')
            ->where('chapter','=',"$zhangjie")
            ->orderBy(DB::raw('RAND()'))
            ->take("$count")
            ->get();
        return view('Index.shijuan',[
           'obj' => $obj,
            'user' => $usrinfo,
        ]);
    }
    public function dafen(Request $request){
        $openid = $request->session()->get('openid');
        $usr = new Student();
        $usrinfo = $usr->where('openid','=',"$openid")->first();
        $stuid = $usrinfo->id;
        $dui = $request->input('dui');
        $count = $request->input('count');
        $score = (integer)($dui/$count*100);

        DB::table('counts')->insert([
            'student_id' => $stuid,
            'grade' => $score,
            'content' => array(
                'correct' => $dui,
                'default' => $count-$dui,
            ),
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
            echo "<script>alert('未检测到教师信息,请联系管理员添加!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        }
    }
    public function chooseshijuan(){
        //$obj = DB::table('tests')->where('id', '>' ,'0')->get();
        return view('Index.chooseshijuan');
    }
    public function shijuanInsert(Request $request){
        $zhangjie = $request->input('zhangjie');
        $count = $request->input('count');
        $res = DB::table('shijuans')->insert([
            'zhangjie' => $zhangjie,
            'count' => $count,
        ]);
        if ($res){
            echo "<script>alert('试题已生成');</script>";
        }else{
            echo "<script>alert('数据写入失败!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        }
    }
}
