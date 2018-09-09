<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class WeixinController extends Controller
{
    //测试公众号
    public $appid = 'wxcdbf45f934f6fc22';
	public $appsecret = 'f3b667a994f344c78050518f009ccf86';

	public $url = 'http://exam.delin0.cn';

	//session获取access_token
    function WxAccessToken(){
        if(session('access_token')&&session('expire_time')>time()){
            //如果access_token未过期
            return $_SESSION['access_token'];
        }else{
            //access_token已过期，需重新获取
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $res=$this->http_curl($url,'get','json');
            $access_token=$res['access_token'];
            //将access_token存到session
            session(['access_token'=>$access_token]);
            session(['expire_time'=>time()+7000]);
            return $access_token;
        }

    }


	//数据库获取全局access_token
	public function getWxAccessToken(){
        echo 222;die;
		$res = DB::table('token')->whereId(1)->first();
		if(strtotime($res->updated_at) > time()){
			return $res->content;
		}else{
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;
			$res1 = $this->http_curl($url,'get','json');
			$access_token = $res1['access_token'];
			$content=$access_token;
			$updated_at=date("Y-m-d H:i:s",time()+7000);
			DB::table('token')->where('id', 1)->update(compact('content','updated_at'));
			return $access_token;
		}
	}

    //返回curl
    public function http_curl($url,$type="get",$res="json",$arr=""){
        //1.初始化curl
        $ch = curl_init();
        //2.设置url的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($type == "post") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //3.采集
        $output = curl_exec($ch);

        //4.关闭curl

        if ($res == "json") {
            if (curl_errno($ch)) {
                return curl_error($ch);
            } else {
                return json_decode($output, true);
            }
        }
        curl_close($ch);
    }

	//获取微信服务器ip
	public function getWxServerIp(){
		$accessToken = $this->getWxAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$accessToken;
		$ch = curl_init();
		//2.设置URL参数
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		//3.采集
		$res = curl_exec($ch);
		if(curl_errno($ch)){
			var_dump(curl_error($ch));
		}
		curl_close($ch);
		$arr = json_decode($res,true);
		echo '<pre>';
		var_dump($arr);
	}

	//微信授权
	function getUserDetail($redirect_uri){
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect ';
        echo '<script>window.open("'.$url.'",true);</script>';
    }

    //授权回调返回用户信息
    function getUserInfo(){
	    //获取网页授权的access_token
        $code=$_GET['code'];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code ';
        //拉取用户的openid
        $res=$this->http_curl($url,'get');

        if(isset($res['openid'])){
	        $openid=$res['openid'];
	        $access_token=$res['access_token'];
	        //拉取用户信息
	        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
	        $res=$this->http_curl($url); 
	        $user=[
	        		'openid'=>$res['openid'],
	        		'nickname'=>$res['nickname'],
	        		'sex'=>$res['sex'],
	        		'city'=>$res['province'].'--'.$res['city'],
	        		'headimgurl'=>$res['headimgurl'],
	        	];
	       $user['sex']= $user['sex']==1?'男':'女';
        }else{
        	$user=[
	        		'openid'=>'',
	        		'nickname'=>'',
	        		'sex'=>'',
	        		'city'=>'',
	        		'headimgurl'=>'',
	        	];
        }
        return $user;
    }


    //模板消息接口
    /**
     * id 模板id
     * openid 发送方openid
     * urls 跳转链接
     * name data值
     */
    function sendTemplateMsg($openid,$urls,$name){
        //1.获取access_token
        $access_token=$this->getWxAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        /*模板ID
        *0-权利转移
        */
        $templateID=array('XX3Hant-oGZT6pc1GfMiZNZodndNQcopHeK7X8SNuJE');
        //2.组装数组
        $arr=array(
            'touser'=>$openid,
            'template_id'=>$templateID[0],
            'url'=>$urls,
            'data'=>array(
                'name'=>array('value'=>$name,'color'=>'#de5b7b'),
            ),
        );
        $postJson=json_encode($arr);
        $res=$this->http_curl($url,'post','json',$postJson);
        return $res;
    }



	/**
	 * [getQrCode 获取tiket和临时或永久二维码$QrCodeUrl]
	 * @param  [int] $expire_seconds [该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。]
	 * @param  [str] $action_name    [二维码类型，QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值]
	 * @param  [int] $scene_id       [场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）]
	 * @return [URL]                 [图片URL地址]
	 */
	public function getQrCode($expire_seconds = 6000, $action_name = 'QR_SCENE', $scene_id = 1000){
		//获取tiket票据
		//全局票据access_token 网页授权access_token 微信js_SDK jsapi_tickt
		$access_token = $this->getWxAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
		//POST数据例子：{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
		$postArr = array(
			'expire_seconds' => $expire_seconds,
			'action_name' => $action_name,
			'action_info' => array(
				'scene' => array(
					'scene_id' => $scene_id,
				),
			),
		);
		$postJson = json_encode($postArr);
		$res = $this->http_curl($url,'post','json',$postJson);
		$ticket = $res['ticket'];
		$QrCodeUrl = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket); //获取二维码
		echo $QrCodeUrl;
		echo "<img src=".$QrCodeUrl.">";
	}

	//获取jsApi Ticket
	public function getJsApiTicket(){
		$res = Redis::hmGet('LjsapiTickt', array('id', 'jsapi_tickt', 'expire_time'));
		if($res['expire_time'] > time()){
			$jsapi_tickt = $res['jsapi_tickt'];
		}else{
			$access_token = $this->getWxAccessToken();
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
			$res = $this->http_curl($url);
			$jsapi_tickt = $res['ticket'];
			Redis::hMset('LjsapiTickt', array('id' => 1, 'jsapi_tickt' => $jsapi_tickt, 'expire_time' => time()+7000));
		}
		return $jsapi_tickt;
	}

	/**
	 * [getRandCode 获取随机N为随机码 ]
	 * @param  [num] $num [是获取的长度]
	 * @return [str]      [description]
	 */
	public function getRandCode($num){
		$str = array( 'a','b','c','d','e','f','g','h','i','j','k','l','n','m','o','p','q','r','s','t','w','v','u','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','N','M','O','P','Q','R','S','T','W','V','U','X','Y','Z','0','1','2','3','4','5','6','7','8','9');
		$length = count($str);
		$code = '';
		for($i = 0; $i < $num; $i++){
			$key = rand(0, $length - 1);
			$code .= $str[$key];
		}
		return $code;
	}


	//回复多图文
    function responseNews($postObj,$arr)
    {
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;

        $template = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <ArticleCount>" . count($arr) . "</ArticleCount>
                <Articles>";
        foreach ($arr as $k => $v) {
            $template .= " <item>
                    <Title><![CDATA[" . $v['title'] . "]]></Title>
                    <Description><![CDATA[" . $v['description'] . "]]></Description>
                    <PicUrl><![CDATA[" . $v['picUrl'] . "]]></PicUrl>
                    <Url><![CDATA[" . $v['url'] . "]]></Url>
                    </item>";
        }
        $template .= " </Articles>
</xml>";

        echo sprintf($template, $toUser, $fromUser, time(), 'news');
    }

    //回复单文本
    public function responseText($postObj,$content){

        $template='<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                </xml>';
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time = time();
        $msgType = 'text';
        echo sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
    }

    function responseSubscribe($postObj,$arr){
        //回复用户的关注
        $this->responseNews($postObj,$arr);
    }


}
