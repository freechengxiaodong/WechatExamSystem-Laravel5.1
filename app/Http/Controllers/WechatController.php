<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WeixinController;
use Illuminate\Support\Facades\DB;
use Log;

class WechatController extends Controller
{
    public $url = 'http://exam.delin0.cn';
    public $appid = 'wxcdbf45f934f6fc22';
    public $appsecret = 'f3b667a994f344c78050518f009ccf86';

    public function index() {
        //1.将timestamp,nonce,token按字典序排列
        $timestamp= $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token  = 'weixin';
        $signature = $_GET['signature'];
        $echostr = $_GET['echostr'];

        //形成数组，然后按字典序排序
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str  == $signature  ){
            //第一次接入weixin api接口的时候
            $this->reponseMsg();
            $this->definedItem();
            echo  $echostr;
            exit;
        }else{
            $this->reponseMsg();
            $this->definedItem();
        }
    }

    //接收事件推送并回复
    public function reponseMsg(){
        $postArr = file_get_contents('php://input');
        $postObj=simplexml_load_string($postArr);
        //判断该数据包是否是订阅的事件推送
        if(strtolower($postObj->MsgType)=='event') {
            //如果是关注事件
            if (strtolower($postObj->Event) == 'subscribe') {
                $content='欢迎关注';
                $weixin=new WeixinController;
                $weixin->responseText($postObj,$content);
            }
        }

        //用户回复文本
        if(strtolower($postObj->MsgType)=='text'){
            $text='123';
            if($postObj->Content==$text){
                $content='你好，123';
                $weixin=new WeixinController();
                $weixin->responseText($postObj,$content);
            }
        }

        //回复多图文消息
        if(strtolower($postObj->MsgType)=='text'){
            $content='456';
            if($postObj->Content==$content)
                $arr=array(
                    array(
                        'title'=>'123',
                        'description'=>'百度',
                        'picUrl'=>'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=930273838,2930834021&fm=27&gp=0.jpg',
                        'url'=>'http://www.baidu.com',
                    ),
                    array(
                        'title'=>'234',
                        'description'=>'淘宝',
                        'picUrl'=>'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=930273838,2930834021&fm=27&gp=0.jpg',
                        'url'=>'http://www.hao123.com',
                    ),
                    array(
                        'title'=>'45436',
                        'description'=>'好',
                        'picUrl'=>'https://www.baidu.com/img/bd_logo1.png',
                        'url'=>'http://www.qq.com',
                    ),
                );
            //子图文不能超过十个
            //实例化Model
            $indexModel=new WeixinController;
            //回复多图文
            $indexModel->responseNews($postObj,$arr);

        }else{
            $content='以收到你的回复';
            $weixin=new WeixinController();
            $weixin->responseText($postObj,$content);

        }
    }
    //创建微信菜单
    public function definedItem(){
        $obj=new WeixinController;
        $access_token = $obj->getWxAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $postJson = '{ 
                "button":[
                            { 
                                "type":"click",
                                "name":"待定",
                                "key":"item1"
                           },
                           { 
                                "name":"随堂测试",
                                "type":"view",
                                "url":"http://exam.delin0.cn/loginConfirm"
                            },
                            { 
                                "name":"生成试卷",
                                "type":"view",
                                "url":"http://exam.delin0.cn/createshijuan?flag=0"
                            }
                        ]
                    }';
        $res = $obj->http_curl($url,'post','json',$postJson);
    }
}
