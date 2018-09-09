<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
use EasyWeChat\Foundation\Application;

class WechatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        //验证
        $config = [
            'app_id' => 'wxcdbf45f934f6fc22',
            'secret' => 'f3b667a994f344c78050518f009ccf86',
            'token' => 'wechat',
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__.'/wechat.log',
            ],
        ];

        $app = Factory::officialAccount($config);

        $response = $app->server->serve();

// 将响应输出
        $response->send(); // Laravel 里请使用：return $response;
        //以下是正文
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
                "name"=>"测试菜单",
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
    //调用方法    WeChat::sendAlertMsg("param1", "param2", "param3", "param4", "param5");
    public static function sendAlertMsg($title, $service, $status, $message, $remark) {
        $config = Config::get("wechat.official_account.default");
        date_default_timezone_set('Asia/Shanghai');
        $app = Factory::officialAccount($config); // 公众号
        $templateId = "12335454";   //这里是模板ID，自行去公众号获取
        $currentTime = date('Y-m-d H:i:s',time());
        $host = "baidu123.com";   //你的域名

        $openids = ["1256456965252"];   //关注微信公众号的openid，前往公众号获取
        foreach ($openids as $v) {
            $result = $app->template_message->send([
                'touser' => $v,
                'template_id' => $templateId,
                'url' => 'baidu.com',  //上边的域名
                'data' => [
                    'first' => $title,
                    'keyword1' => $currentTime,
                    'keyword2' => $host,
                    'keyword3' => $service,
                    'keyword4' => $status,
                    'keyword5' =>$message,
                    'remark' => $remark,
                ]
            ]);
            Log::info("template send result:", $result);
        }
        return Config::get("error.0");
    }
}
