@extends('layout.father')
@section('title','正在答题')
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
        .question{
            font-size: 25px;
            margin-bottom: 10px;
        }
        .answers {
            margin-bottom: 18px;
            text-align: left;
            display: inline-block;
        }
        .answers label{
            display: block;
            margin-bottom: 10px;
        }
        button{
            font-family: 'Work Sans', sans-serif;
            font-size: 16px;
            background-color: green;
            color: #fff;
            border: 0px;
            border-radius: 3px;
            padding: 10px;
            cursor: pointer;
            margin-bottom: 20px;
            margin-top: 120px;
        }
        button:hover{
            background-color: #38a;
        }
        .slide{
            position: absolute;
            left: 0px;
            top: 0px;
            width: 100%;
            z-index: 1;
            opacity: 0;
            transition: opacity 0.5s;
        }
        .active-slide{
            opacity: 1;
            z-index: 2;
        }
        .quiz-container{
            position: relative;
            height: 200px;
            margin-top: 40px;
        }
    </style>



    <SCRIPT type="text/javascript">
        var maxtime = 60 * 60; //一个小时，按秒计算，自己调整!
        function CountDown() {
            if (maxtime >= 0) {
                minutes = Math.floor(maxtime / 60 / 6);
                seconds = Math.floor(maxtime % 60);
                msg =  minutes + "分" + seconds + "秒";
                document.all["timer"].innerHTML = msg;
                if (maxtime == 5 * 60)alert("还剩5分钟");
                --maxtime;
            } else{
                clearInterval(timer);
                alert("时间到，结束!");
            }
        }
        timer = setInterval("CountDown()", 1000);
    </SCRIPT>
    <h1>姓名:{{$user->name}}&nbsp;&nbsp;&nbsp;&nbsp;<span style="float: right">剩余时间:<span id="timer" style="color:red;"></span></span></h1>
<div class="quiz-container">
    <div id="quiz"></div>
</div>
<button id="previous">前一题</button>
<button id="next">下一题</button>
<button id="submit">交卷</button>
<div id="results"></div>
<script type="text/javascript">
    (function() {
        const myQuestions = [
            @foreach($obj as $k => $v)
            {
                question: "  第"+"{{$k+1}}"+"题:  "+"{{$v->title}}    ",
                answers: {
                    A: "{{$v->a}}",
                    B: "{{$v->b}}",
                    C: "{{$v->c}}",
                    D: "{{$v->d}}"
                },
                correctAnswer: "{{$v->answer}}"
            },
                @endforeach
        ];

        function buildQuiz() {
            // we'll need a place to store the HTML output
            const output = [];

            // for each question...
            myQuestions.forEach((currentQuestion, questionNumber) => {
                // we'll want to store the list of answer choices
                const answers = [];

                // and for each available answer...
                for (letter in currentQuestion.answers) {
                    // ...add an HTML radio button
                    answers.push(
                        `<label>
                     <input type="radio" name="question${questionNumber}" value="${letter}">
                      ${letter} :
                      ${currentQuestion.answers[letter]}
                   </label>`
                    );
                }

                // add this question and its answers to the output
                output.push(
                    `<div class="slide">
                   <div class="question"> ${currentQuestion.question} </div>
                   <div class="answers"> ${answers.join("")} </div>
                 </div>`
                );
            });

            // finally combine our output list into one string of HTML and put it on the page
            quizContainer.innerHTML = output.join("");
        }

        function showResults() {
            // gather answer containers from our quiz
            const answerContainers = quizContainer.querySelectorAll(".answers");

            // keep track of user's answers
            let numCorrect = 0;

            // for each question...
            myQuestions.forEach((currentQuestion, questionNumber) => {
                // find selected answer
                const answerContainer = answerContainers[questionNumber];
                const selector = `input[name=question${questionNumber}]:checked`;
                const userAnswer = (answerContainer.querySelector(selector) || {}).value;

                // if answer is correct
                if (userAnswer === currentQuestion.correctAnswer) {
                    // add to the number of correct answers
                    numCorrect++;

                    // color the answers green
                    //answerContainers[questionNumber].style.color = "lightgreen";
                } else {
                    // if answer is wrong or blank
                    // color the answers red
                    //answerContainers[questionNumber].style.color = "red";
                }
            });

            // show number of correct answers out of total
            //resultsContainer.innerHTML = `你答对了${myQuestions.length}中的${numCorrect}`;
            window.location.href = "/dafen?dui="+numCorrect+"&count="+myQuestions.length+"&shijuanid="+"{{$shijuanid}}"";
        }

        function showSlide(n) {
            slides[currentSlide].classList.remove("active-slide");
            slides[n].classList.add("active-slide");
            currentSlide = n;

            if (currentSlide === 0) {
                previousButton.style.display = "none";
            } else {
                previousButton.style.display = "inline-block";
            }

            if (currentSlide === slides.length - 1) {
                nextButton.style.display = "none";
                submitButton.style.display = "inline-block";
            } else {
                nextButton.style.display = "inline-block";
            }
        }

        function showNextSlide() {
            showSlide(currentSlide + 1);
        }

        function showPreviousSlide() {
            showSlide(currentSlide - 1);
        }

        const quizContainer = document.getElementById("quiz");
        const resultsContainer = document.getElementById("results");
        const submitButton = document.getElementById("submit");

        // display quiz right away
        buildQuiz();

        const previousButton = document.getElementById("previous");
        const nextButton = document.getElementById("next");
        const slides = document.querySelectorAll(".slide");
        let currentSlide = 0;

        showSlide(0);

        // on submit, show results
        submitButton.addEventListener("click", showResults);
        previousButton.addEventListener("click", showPreviousSlide);
        nextButton.addEventListener("click", showNextSlide);
    })();
</script>

    @endsection