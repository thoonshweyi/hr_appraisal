<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>New Year Count Down</title>
    <link href="{{ asset('assets/libs/bootstrap-5.3.3/bootstrap.min.css') }}" rel="stylesheet" >

    <style>

        *{
        box-sizing: border-box;
        }
        body{
            /* font-family: sans-serif; */
            background: url(" {{ asset('images/assessor.jpg') }}");
            background-repeat: no-repeat;
            background-size: cover;/*full background*/
            background-position: center center;
            height: 100vh;
            color: #fff;

            display: flex;/*flex display elements left to right*/
            justify-content: center;
            align-items: center;

            flex-direction: column;/*flex elements to display as original*/

            margin: 0;
            overflow: hidden;
        }
        body::after{ /*make body into two layer with after element*/
            content: '';
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);

            position: absolute;
            top:0;
            left: 0;

            z-index: -1;
        }
        h1{
            font-size: 60px;
        }
        .year{
            font-size: 200px;
            opacity: 0.2;

            position: absolute;
            top: 20px;

            left: 50%;
            transform: translate(-50%);/*move element left again to 50% of its elements*/
            z-index: -1;
        }
        .dashboard-btn{
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .countdown{
            /*display elements in the countdown left to right(display:flex)*/
            /* display: flex; */
            transform: scale(2);
            display: none;
        }
        .time{
            display: flex;
            justify-content: center;
            align-items: center;        /*move elements of time class into center*/

            flex-direction: column;	   /*flex elements to display as original*/
            margin: 15px;
        }
        .time h2{
            margin: 0 5px; /*by adding margin from time the total margin is 20px*/
        }
        @media(max-width:500px){/*in mobile size the following code will execute*/
            h1{
                font-size: 45px;
            }
            .time{
                margin: 5px;
            }
            .time h2{
                font-size: 12px;
                margin: 0px;
            }
            .time small{
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
	<div id="year" class="year"></div>


	<h1>Appraisal Countdown</h1>

	<div id="countdown"  class="countdown">
		<div class="time">
			<h2 id="days">00</h2>
			<small>days</small>
		</div>
		<div class="time">
			<h2 id="hours">00</h2>
			<small>hours</small>
		</div>
		<div class="time">
			<h2 id="minutes">00</h2>
			<small>minutes</small>
		</div>
		<div class="time">
			<h2 id="seconds">00</h2>
			<small>seconds</small>
		</div>
	</div>
    <div class="">
        <a href="{{ route('home') }}" class="btn btn-lg btn-primary dashboard-btn">Go To Dashboard</a>
    </div>
	<img src="{{ asset('images/spinner.gif') }}" id="loading" class="loading" alt="loading"/>



    <script src="{{ asset('assets/libs/bootstrap-5.3.3/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
        //UI
        const days = document.getElementById("days");
        const hours = document.getElementById("hours");
        const minutes = document.getElementById("minutes");
        const seconds = document.getElementById("seconds");

        const year = document.getElementById("year");
        const countdown = document.getElementById("countdown");
        const loading = document.getElementById("loading");

        const currentyear = new Date().getFullYear();
        // console.log(currentyear);

        {{-- const newyeartime = new Date(`January 01 ${currentyear+1} 00:00:00`); --}}
        const newyeartime = new Date(`{{ $startdate }}`);

        // console.log(newyeartime);

        {{-- year.textContent = currentyear+1; --}}
        year.textContent = {{ \Carbon\Carbon::parse($appraisalcycle->start_date)->format('Y') }};

        function updatecountdown(){
            // console.log("hay");
            const currenttime = new Date();
            // console.log(currenttime);

            const diff = newyeartime - currenttime ;/*get different in miliseconds*/

            // console.log(diff)
                            //   ms      s     m    hr   d
            const d = Math.floor(diff / 1000 / 60 / 60 / 24) ;// convert different into day
            // console.log(d);
            const h = Math.floor(diff / 1000 / 60 / 60) % 24 ; //calculate modulus hours after converting to day
            // console.log(h)

            const m = Math.floor(diff / 1000 / 60) % 60 ;//calculate modulus minutes after converting to hours
            // console.log(m);

            const s = Math.floor(diff / 1000) % 60;//calculate modulus seconds after converting to minutes
            // console.log(s)

            days.textContent = d ;
            hours.textContent  = h < 10 ? "0"+ h : h;//add leading zero in hours less than 10
            minutes.textContent = m < 10 ? "0"+ m : m ;// add leading zero in minutes less than 10
            seconds.textContent = s < 10 ? "0"+ s : s;// add leading zero in seconds less than 10
        }
        setInterval(updatecountdown,1000);//recall updatecountdown in every 1 seconds
        setTimeout(() => {
            loading.remove();
            countdown.style.display = "flex" ;
        }, 1000);//do nameless function in comming 1 second
	</script>
</body>
</html>
