<?php
//引入composer入口文件
include '/var/www/html/exam/vendor/autoload.php';
//引入我们的主项目的入口类。
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;
use EasyWeChat\Message\Raw;

//在options中填入配置信息
$options = [
    //打开调试模式
    'debug'     => true,
//微信基本配置，从公众平台获取
    'app_id'    => 'wxcdbf45f934f6fc22',
    'secret'    => 'f3b667a994f344c78050518f009ccf86',
    'token'     => 'wechat',
//日志配置
    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/easywechat.log',
    ],

];
//使用配置初始化一个项目实例
$app = new Application($options);
//从项目实例中得到一个服务端应用实例
$server = $app->server;
//用户实例，可以通过类似$user->nickname这样的方法拿到用户昵称，openid等等
$user = $app->user;
//接收用户发送的消息
$server->setMessageHandler(function ($message) use ($user){
//对用户发送的消息根据不同类型进行区分处理
    switch ($message->MsgType) {
        //事件类型消息（点击菜单、关注、扫码），略
        case 'event':
            switch ($message->Event) {
                case 'subscribe':
                    // code...
                    break;

                default:
                    // code...
                    break;
            }
            break;
        //文本信息处理
        case 'text':
            //获取到用户发送的文本内容
            $content = $message->Content;
            //发送到图灵机器人接口
            $url = "http://www.tuling123.com/openapi/api?key=【图
                   灵机器人API KEY】&info=".$content;
            //获取图灵机器人返回的内容
            $content = file_get_contents($url);
            //对内容json解码
            $content = json_decode($content);
            //把内容发给用户
            return new Text(['content' => $content->text]);
            break;
        //图片信息处理，略
        case 'image':
            $mediaId  = $message->MediaId;
            return new Image(['media_id' => $mediaId]);
            break;
        //声音信息处理，略
        case 'voice':
            $mediaId  = $message->MediaId;
            return new Voice(['media_id' => $mediaId]);
            break;
        //视频信息处理，略
        case 'video':
            $mediaId  = $message->MediaId;
            return new Video(['media_id' => $mediaId]);
            break;
        //坐标信息处理，略
        case 'location':
            return new Text(['content' => $message->Label]);
            break;

        //链接信息处理，略
        case 'link':
            return new Text(['content' => $message->Description]);
            break;

        default:
            break;
    }
});
//响应输出
$server->serve()->send();