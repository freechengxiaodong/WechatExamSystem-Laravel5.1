@extends('layout.father')
@section('title','教师面板')
@section('content')
    <style>
        *{
            margin: 0 auto;
        }
    </style>
    <style type="text/css">
        table.altrowstable {
            font-family: verdana,arial,sans-serif;
            font-size:11px;
            color:#333333;
            border-width: 1px;
            border-color: #a9c6c9;
            border-collapse: collapse;
            width: 100%;
        }
        table.altrowstable th {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #a9c6c9;
        }
        table.altrowstable td {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #a9c6c9;
            text-align: center;
        }
        .oddrowcolor{
            background-color:#d4e3e5;
        }
        .evenrowcolor{
            background-color:#c3dde0;
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
                        <script type="text/javascript">
                            function altRows(id){
                                if(document.getElementsByTagName){

                                    var table = document.getElementById(id);
                                    var rows = table.getElementsByTagName("tr");

                                    for(i = 0; i < rows.length; i++){
                                        if(i % 2 == 0){
                                            rows[i].className = "evenrowcolor";
                                        }else{
                                            rows[i].className = "oddrowcolor";
                                        }
                                    }
                                }
                            }

                            window.onload=function(){
                                altRows('alternatecolor');
                            }
                        </script>
                        <table class="altrowstable" id="alternatecolor">
                            <tr>
                                <th>排名</th><th>姓名</th><th>分数</th>
                            </tr>
                            @foreach($obj as $k => $v)
                                <tr>
                                    <td>{{$k+1}}</td><td>{{$v->name}}</td><td>{{$v->grade}}</td>
                                </tr>
                                @endforeach
                        </table>

                        <!--  The table code can be found here: http://www.textfixer/resources/css-tables.php#css-table03 -->









                    @endif
            @else
            <form action="{{url('/shijuanInsert')}}" method="post" style="height: 400px;width: 80%;margin-top: 50px">
                <div class="weui-cells__title" style="font-size: 22px;text-align: center;color: green">测试内容</div>
                <div class="weui-cells__title" style="margin-top: 20px">选择章节</div>
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