@extends('layout.father')
@section('title','试卷')
@section('content')
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Work+Sans:300,600);

        body{
            font-size: 20px;
            font-family: 'Work Sans', sans-serif;
            color: #333;
            font-weight: 300;
            text-align: center;
            background-color: #f8f6f0;
        }
        h1{
            font-weight: 300;
            margin: 0px;
            padding: 10px;
            font-size: 16px;
            background-color: #444;
            color: #fff;
        }
    </style>
    <h1><span style="float: left">姓名:{{$user->name}}</span>&nbsp;&nbsp;</h1>
<div class="quiz-container">
    <div id="quiz"></div>
</div>
    <div style="margin-top: 60px">
        <h2 style="font-size: 20px">你答对了{{$count}}道题中的{{$dui}}道</h2>
        <h2 style="font-size: 16px">本次测试的最终得分为:</h2>
        <h2><span style="font-size: 60px;color: red"  class="timer count-title" id="count-number" data-to="{{$score}}" data-speed="1500">0</span>
            <span style="font-size: 20px">分</span></h2>
    </div>
    <script src="/other/js/jquery.min.js"></script>
    <script type="text/javascript" src="/other/js/count.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            if (window.history && window.history.pushState) {
                $(window).on('popstate', function () {
                    window.history.forward(1);
                });
            }
        });
    </script>
    <script>
        $.fn.countTo = function(a) {
            a = a || {};
            return $(this).each(function() {
                var c = $.extend({},
                    $.fn.countTo.defaults, {
                        from: $(this).data("from"),
                        to: $(this).data("to"),
                        speed: $(this).data("speed"),
                        refreshInterval: $(this).data("refresh-interval"),
                        decimals: $(this).data("decimals")
                    }, a);
                var h = Math.ceil(c.speed / c.refreshInterval),
                    i = (c.to - c.from) / h;
                var j = this,
                    f = $(this),
                    e = 0,
                    g = c.from,
                    d = f.data("countTo") || {};
                f.data("countTo", d);
                if (d.interval) {
                    clearInterval(d.interval)
                }
                d.interval = setInterval(k, c.refreshInterval);
                b(g);
                function k() {
                    g += i;
                    e++;
                    b(g);
                    if (typeof(c.onUpdate) == "function") {
                        c.onUpdate.call(j, g)
                    }
                    if (e >= h) {
                        f.removeData("countTo");
                        clearInterval(d.interval);
                        g = c.to;
                        if (typeof(c.onComplete) == "function") {
                            c.onComplete.call(j, g)
                        }
                    }
                }
                function b(m) {
                    var l = c.formatter.call(j, m, c);
                    f.html(l)
                }
            })
        };
        $.fn.countTo.defaults = {
            from: 0,
            to: 0,
            speed: 1000,
            refreshInterval: 100,
            decimals: 0,
            formatter: formatter,
            onUpdate: null,
            onComplete: null
        };
        function formatter(b, a) {
            return b.toFixed(2)
        }
        $("#count-number").data("countToOptions", {
            formatter: function(b, a) {
                //0是保留位数
                return b.toFixed(0).replace(/\B(?=(?:\d{3})+(?!\d))/g, ",")
            }
        });
        $(".timer").each(count);
        function count(a) {
            var b = $(this);
            a = $.extend({},
                a || {},
                b.data("countToOptions") || {});
            b.countTo(a)
        };
    </script>
    @endsection