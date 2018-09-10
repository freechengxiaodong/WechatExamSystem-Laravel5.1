@extends('layout.father')
@section('title','跳转提示')
@section('content')
    <div class="page msg_success js_show">
        <div class="weui-msg">
            <div class="weui-msg__icon-area">
                @if($title == 'success')
                    <i class="weui-icon-success weui-icon_msg"></i>
                    @else
                    <i class="weui-icon-warn weui-icon_msg"></i>
                    @endif
            </div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">{{$content}}</h2>
            </div>
            <div class="weui-msg__opr-area" style="margin-top: 100px">
                <p class="weui-btn-area">
                    <a onclick="WeixinJSBridge.call('closeWindow');" class="weui-btn weui-btn_primary">关闭本页面</a>
                </p>
            </div>
        </div>
    </div>
    @endsection