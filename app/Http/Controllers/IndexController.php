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
        $res1 = DB::table('teachers')->where('openid','=',"$openid")->first();
        if($res1){
            //进入老师页
            echo "<script>location.href='".'/createshijuan'."';</script>";
        }
        if($usrinfo){
            //上一页$_SERVER["HTTP_REFERER"]
            echo "<script>location.href='".'/shijuan'."';</script>";
        }else{
            //判断是否为老师
            $res = DB::table('teachers')->where('openid','=',"$openid")->first();
            if($res){
                //进入老师页
                echo "<script>location.href='".'/createshijuan'."';</script>";
            }
            //尚未注册,需要先注册信息
            return view('Index.resgister',[
                'openid' => $openid,
            ]);
        }
    }
    public function studentInfoInsert(Request $request){
        $name = $request->input('name');
        $number = $request->input('number');
        $res = DB::table('students')->where('name','=',$name)->where('number','=',$number)->first();
        if($res){
            $id = $res->id;
            $jieguo = DB::table('students')->where('id','=',$id)->update([
                'openid' => $request->session()->get('openid'),
            ]);
            if($jieguo){
                echo "<script>location.href='".'/shijuan'."';</script>";
            }else{
                $title = 'error';
                $content = '提交信息含有非法参数,请重新绑定!';
                return view('warning.msg',[
                    'title' => $title,
                    'content' => $content,
                ]);
            }
        }else{
            $title = 'error';
            $content = '未找到学生信息,请填写正确的姓名以及学号信息,或者请联系管理员老师重新录入信息!';
            return view('warning.msg',[
                'title' => $title,
                'content' => $content,
            ]);
        }
    }
    public function shijuan(Request $request){
        //检测教师是否有推出试卷
        $id = DB::table('shijuans')->max('id');
        $res = DB::table('shijuans')->find($id);

//        $startdate=$res->created_at;
//        $enddate=date("y-m-d H:i:s");
//        $startyear = date("Y-m-d",strtotime($startdate));
//        $currentyear = date("Y-m-d",strtotime($enddate));
//        if($startyear != $currentyear){
//            $title = 'error';
//            $content = '试卷已过期!';
//            return view('warning.msg',[
//                'title' => $title,
//                'content' => $content,
//            ]);
//        }
//        echo round(($enddate-$startdate)/3600/24)*24*60;die;
//        $startminute = date("H",strtotime($startdate))*60+date("i",strtotime($startdate));
//        $endminute = date("H",strtotime($enddate))*60+date("i",strtotime($enddate));

//        $date=floor((strtotime($enddate)-strtotime($startdate))/86400);
//        $hour=floor((strtotime($enddate)-strtotime($startdate))%86400/3600);
//        $minute=floor((strtotime($enddate)-strtotime($startdate))%86400/60);
        //echo $minute;
//        echo $endminute-$startminute;die;
//        if($endminute-$startminute>=30){
//            $title = 'error';
//            $content = '试卷已超过30分钟,过期!';
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
        $avgscore = '';
        if($request->input('flag')){
            if($request->input('flag') == '1'){
                $shijuanid = DB::table('shijuans')->max('id');
                $avgscore = DB::table('counts')->where('shijuan_id','=',$shijuanid)->avg('grade');
                $avgscore = (int)($avgscore);
                $obj = DB::table('counts')->where('shijuan_id','=',$shijuanid)->orderBy('grade','DESC')->get();
                //遍历数据对象,组合带学生姓名的数据
                foreach($obj as $k => $v){
                    $info = DB::table('students')->where('id','=',$v->student_id)->first();
                    $v->name = $info->name;
                }
            }
        }
        return view('Index.chooseshijuan',[
            'obj' => $obj,
            'avgscore' => $avgscore,
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
