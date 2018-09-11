@extends('layout.father')
@section('title','学生信息完善')
@section('content')
    <style>
        *{
            margin: 0 auto;
        }
    </style>
    <form action="{{url('/studentInfoInsert')}}" method="post" style="height: 300px;width: 80%;margin-top: 60px">
        <div class="weui-cells__title" style="font-size: 22px;text-align: center;color: green;">学生信息绑定</div>
        <div class="weui-cell" style="border: 1px solid grey;border-radius: 3px;margin-top: 100px;">
            <div class="weui-cell__hd"><label class="weui-label" style="width: 60px">姓名</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="name" placeholder="请输入姓名">
            </div>
        </div>
        <div class="weui-cell" style="border: 1px solid grey;border-radius: 3px;margin-top: 5px">
            <div class="weui-cell__hd"><label class="weui-label" style="width: 60px">学号</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" name="number" pattern="[0-9]*" placeholder="请输学号">
            </div>
        </div>
        {{csrf_field()}}
        <input type="hidden" name="openid" value="{{$openid}}">
        <button class="weui-btn weui-btn_plain-primary" style="width: 100%;margin-top: 80px">确认提交</button>
    </form>

    @endsection