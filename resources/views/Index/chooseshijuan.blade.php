@extends('layout.father')
@section('title','试卷选择')
@section('content')
    <style>
        *{
            margin: 0 auto;
        }
    </style>
    <div class="weui-tab">
        <div class="weui-navbar">
            <div class="weui-navbar__item weui-bar__item--on">
                选项一
            </div>
            <div class="weui-navbar__item">
                选项二
            </div>
            <div class="weui-navbar__item">
                选项三
            </div>
        </div>
        <div class="weui-tab__bd">

            <input type="text" id="a">

        </div>

        <div class="weui-tabbar">
            <a href="javascript:;" class="weui-tabbar__item weui-bar__item--on">
                <span class="weui-badge" style="position: absolute;top: -.4em;right: 1em;">8</span>
                <div class="weui-tabbar__icon">
                    <img src="./images/icon_nav_button.png" alt="">
                </div>
                <p class="weui-tabbar__label">微信</p>
            </a>
            <a href="javascript:;" class="weui-tabbar__item">
                <div class="weui-tabbar__icon">
                    <img src="./images/icon_nav_msg.png" alt="">
                </div>
                <p class="weui-tabbar__label">通讯录</p>
            </a>
            <a href="javascript:;" class="weui-tabbar__item">
                <div class="weui-tabbar__icon">
                    <img src="./images/icon_nav_article.png" alt="">
                </div>
                <p class="weui-tabbar__label">发现</p>
            </a>
            <a href="javascript:;" class="weui-tabbar__item">
                <div class="weui-tabbar__icon">
                    <img src="./images/icon_nav_cell.png" alt="">
                </div>
                <p class="weui-tabbar__label">我</p>
            </a>
        </div>
    </div>
    <div class="weui-tab">
        <div class="weui-tab__bd">
            <div id="tab1" class="weui-tab__bd-item weui-tab__bd-item--active">
                <h1>页面一</h1>
            </div>
            <div id="tab2" class="weui-tab__bd-item">
                <h1>页面二</h1>
            </div>
            ...
        </div>

        <div class="weui-tabbar">
            <a href="#tab1" class="weui-tabbar__item weui-bar__item--on">
                ...
            </a>
            <a href="#tab2" class="weui-tabbar__item">
                ...
            </a>
            ...
        </div>
    </div>

















    <form action="{{url('/shijuanInsert')}}" method="post" style="border: 1px solid darkgray;border-radius: 10px;height: 400px;width: 80%;margin-top: 100px">
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

    @endsection