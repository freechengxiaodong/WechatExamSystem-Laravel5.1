@extends('layout.father')
@section('title','学生信息完善')
@section('content')
    <style>
        *{
            margin: 0 auto;
        }
    </style>
    <form action="" method="" style="border: 1px solid darkgray;border-radius: 10px;height: 300px;width: 80%;margin-top: 100px">
        <div class="weui-cells__title" style="font-size: 22px;text-align: center;color: green">学生信息完善</div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label" style="width: 60px">姓名</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" pattern="[0-9]*" placeholder="请输入姓名">
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label" style="width: 60px">班级</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" pattern="[0-9]*" placeholder="请输班级">
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label" style="width: 60px">学号</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" pattern="[0-9]*" placeholder="请输学号">
            </div>
        </div>
    </form>

    @endsection