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

	//access_token
	public function getWxAccessToken(){
		$res = DB::table('token')->whereId(1)->first();
		if(strtotime($res->updated_at) > time()){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;
            $res1 = $this->http_curl($url,'get','json');
            $access_token = $res1['access_token'];
            $content=$access_token;
            $updated_at=date("Y-m-d H:i:s",time()+7000);
            DB::table('token')->where('id', 1)->update(compact('content','updated_at'));
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
    public function http_curl($url,$type="get",$res="json",$arr=""){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($type == "post") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        if ($res == "json") {
            if (curl_errno($ch)) {
                return curl_error($ch);
            } else {
                return json_decode($output, true);
            }
        }
        curl_close($ch);
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
}
