<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WeixinController;
use Log;

class WechatController extends Controller
{
    public function index() {
        //1.将timestamp,nonce,token按字典序排列
        $timestamp= $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token  = 'wechat';   //宗教
        $signature = $_GET['signature'];
        $echostr = $_GET['echostr'];

        //形成数组，然后按字典序排序
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str  == $signature  ){
            //第一次接入weixin api接口的时候
            echo  $echostr;
            exit;
        }else{

        }
    }
    public function checkSignature(){
        //获得参数 signature nonce token timestamp echostr
        $nonce     = $_GET['nonce'];
        $token     = 'weixin';    //填写设置的token值
        $timestamp = $_GET['timestamp'];
        $echostr   = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        //$array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str  == $signature && $echostr ){
            //第一次接入weixin api接口的时候
            echo  $echostr;
            exit;
        }else{
            $this->reponseMsg();
        }
    }
    public function reponseMsg(){
        //1.获取到微信推送过来post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);
        //判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注 subscribe 事件
            if (strtolower($postObj->Event == 'subscribe')) {

                $arr=array(
                    array(
                        'title'=>'感谢你关注',
                        'description'=>'vbs is very good',
                        'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                        'url'=>'http://www.baidu.com',
                    ),
                );
                $arr1='dasf';
                $indexModel=new IndexModel();
                $indexModel->responseText($postObj,$arr1);

            }
        }
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve(Application $wechat)
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
        $response->send(); // Laravel 里请使用：return $response;
        //以下是正文
//        Log::info('request arrived.');
//        $app = app('wechat');
//        $app->server->setMessageHandler(function($message) use ($app){
//            if ($message->MsgType=='event') {
//                $user_openid = $message->FromUserName;
//                if ($message->Event=='subscribe') {
//                    //下面是你点击关注时，进行的操作
//                $user_info['unionid'] = $message->ToUserName;
//                $user_info['openid'] = $user_openid;
//                $userService = $app->user;
//                $user = $userService->get($user_info['openid']);
//                $user_info['subscribe_time'] = $user['subscribe_time'];
//                $user_info['nickname'] = $user['nickname'];
//                $user_info['avatar'] = $user['headimgurl'];
//                $user_info['sex'] = $user['sex'];
//                $user_info['province'] = $user['province'];
//                $user_info['city'] = $user['city'];
//                $user_info['country'] = $user['country'];
//                $user_info['is_subscribe'] = 1;
//                //下面有些是WxStudent相关的方法，就是一些数据库的操作，由于数据库不同，要执行的操作也不一样，所以就只写了一个方法名
//                if (WxStudent::weixin_attention($user_info)) {
//                    return '欢迎关注';
//                }else{
//                    return '您的信息由于某种原因没有保存，请重新关注';
//                }
//            }else if ($message->Event=='unsubscribe') {
//                    //取消关注时执行的操作，（下面返回的信息用户不会收到，因为你已经取消关注，但别的操作还是会执行的<如：取消关注的时候，要把记录该用户从记录微信用户信息的表中删掉>）
//                if (WxStudent::weixin_cancel_attention($user_openid)) {
//                    return '已取消关注';
//                }
//            }
//            }
//
//        });
//
//        Log::info('return response.');
//        return $app->server->serve();
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
