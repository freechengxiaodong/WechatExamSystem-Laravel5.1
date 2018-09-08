<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
class WechatController extends Controller
{
    /** 
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.');
        $app = app('wechat');
        $app->server->setMessageHandler(function($message) use ($app){
            if ($message->MsgType=='event') {
                $user_openid = $message->FromUserName;
                if ($message->Event=='subscribe') {
                    //下面是你点击关注时，进行的操作
                $user_info['unionid'] = $message->ToUserName;
                $user_info['openid'] = $user_openid;
                $userService = $app->user;
                $user = $userService->get($user_info['openid']);
                $user_info['subscribe_time'] = $user['subscribe_time'];
                $user_info['nickname'] = $user['nickname'];
                $user_info['avatar'] = $user['headimgurl'];
                $user_info['sex'] = $user['sex'];
                $user_info['province'] = $user['province'];
                $user_info['city'] = $user['city'];
                $user_info['country'] = $user['country'];
                $user_info['is_subscribe'] = 1;
                //下面有些是WxStudent相关的方法，就是一些数据库的操作，由于数据库不同，要执行的操作也不一样，所以就只写了一个方法名
                if (WxStudent::weixin_attention($user_info)) {
                    return '欢迎关注';
                }else{
                    return '您的信息由于某种原因没有保存，请重新关注';
                }
            }else if ($message->Event=='unsubscribe') {
                    //取消关注时执行的操作，（下面返回的信息用户不会收到，因为你已经取消关注，但别的操作还是会执行的<如：取消关注的时候，要把记录该用户从记录微信用户信息的表中删掉>）
                if (WxStudent::weixin_cancel_attention($user_openid)) {
                    return '已取消关注';
                }
            }
            }

        });

        Log::info('return response.');
        return $app->server->serve();
    }
    //生成菜单
    public  function  menu_add(){
        $app = app('wechat');
        $menu = $app->menu;
        $buttons = [
            [
                "type"=>"view",
                "name"=>"进入课堂",
                "url"=>BASE_URL."###"
            ],
        ];
        $menu->add($buttons);
    }
    //查看当前菜单
    public  function  menu_current(){
        $app = app('wechat');
        $menu = $app->menu;
        $menus = $menu->all();
        var_dump($menus);
    }
    //发送模板消息
    public function sendMsg(){
        $app = app('wechat');
        $notice = $app->notice;
        $template_id = 'XQ3uJilYd5elz-TUHzkvKF4-nfB6Yu3WBm0B45dRtbY';//消息模板的id

        $url = BASE_URL.'/wx_student#/bulletininfo/'.$course_id.'/'.$bu_id;//点击模板消息的跳转的地址

        //循环给多个用户发送消息
        foreach ($users as $user){
            if ($user['openid']!=""&&$user['openid']!='0'&&!empty($user['openid'])){
                $open_id = $user['openid'];

                //注：不同的模板，$data的内容可能不太一样，具体要看你微信公众号后台所使用的模板，上面都有示例的
                $data = array(
                "first"=>$user['name']."同学你好,你的".$course_nam.'课教师'.$create_name.'发布了一个新的班级公告',
                "keyword1"=>'',
                "keyword2"=>'',
                "remark"=>'请及时查看班级公告',
                );
                $notice->uses($template_id)->withUrl($url)->andData($data)->andReceiver($open_id)->send();
            }
        }

    }
}
