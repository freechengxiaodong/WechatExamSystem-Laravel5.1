@extends('layout.father')
@section('title','教师面板')
@section('content')
    <style>
        *{
            margin: 0 auto;
        }
    </style>
    <div class="weui-tab">
        <div class="weui-navbar">
            <div class="weui-navbar__item
             @if(isset($_GET['flag']))
                    @else
                    weui-bar__item--on
            @endif
             ">
                <a href="{{url('/chooseshijuan')}}" style="display: block;text-decoration: none;color: black">试题选择</a>
            </div>
            <div class="weui-navbar__item
            @if(isset($_GET['flag']))
            @if($_GET['flag'] == 1)
                    weui-bar__item--on
                @endif
            @endif

                    ">
                <a href="{{url('/chooseshijuan?flag=1')}}" style="display: block;text-decoration: none;color: black">成绩单</a>
            </div>
        </div>
        <div class="weui-tab__bd">


            @if(isset($_GET['flag']))
                @if($_GET['flag'] == 1)
                    这是成绩表
                    @endif
            @else
            <form action="{{url('/shijuanInsert')}}" method="post" style="border: 1px solid darkgray;border-radius: 10px;height: 400px;width: 100%;margin-top: 70px">
                <div class="weui-cells__title" style="font-size: 22px;text-align: center;color: green">试题选择</div>
                <div class="weui-cells__title">选择章节</div>
                <div class="weui-cell weui-cell_select">
                    <div class="weui-cell__bd">
                        <select class="weui-select" name="zhangjie">
                            <option selected value="第一章">第一章</option>
                            <option value="第二章">第二章</option>
                            <option value="第三章">第三章</option>
                            <option value="第四章">第四章</option>
                            <option value="第五章">第五章</option>
                        </select>
                    </div>
                </div>
                <div class="weui-cells__title">选择题数</div>
                <div class="weui-cell weui-cell_select">
                    <div class="weui-cell__bd">
                        <select class="weui-select" name="count">
                            <option selected value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                        </select>
                    </div>
                </div>
                {{csrf_field()}}
                <input type="hidden" name="openid" value="">
                <button class="weui-btn weui-btn_plain-primary" style="width: 80%;margin-top: 40px">生成试卷</button>
            </form>
                @endif

        </div>





    @endsection