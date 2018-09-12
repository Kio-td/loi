<!doctype html>
<html>
<head>
  <title><?php echo http_response_code() ?> - LOI</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link href="http://necolas.github.io/normalize.css/3.0.2/normalize.css" rel='stylesheet'/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
  <style>
    * {
      font-family: 'Roboto';
    }
    body {
      padding: 2vh;
      background-color: black;
      color: white;
    }
    p {
      font-family: 'breaking-text' !important;
    }
    a {
      color: white;
      font-family: 'breaking-text' !important;
    }
    .glitch {
      color: white;
      font-size: 100px;
      margin: 0 auto;
      width: 180px;
      position: relative;
    }

    .glitch:after {
      animation: glitch-animation 2s infinite linear alternate-reverse;
      background: black;
      clip: rect( 0, 900px, 0, 0 );
      color: white;
      content: attr( data-text );
      left: 2px;
      overflow: hidden;
      position: absolute;
      text-shadow: -1px 0 red;
      top: 0;
    }
    .glitch:before {
      animation: glitch-animation-2 3s infinite linear alternate-reverse;
      background: black;
      clip: rect( 0, 900px, 0, 0 );
      color: white;
      content: attr( data-text );
      left: -2px;
      overflow: hidden;
      position: absolute;
      text-shadow: 1px 0 blue;
      top: 0;
    }
    /* Expanded Animations */
    @keyframes glitch-animation {
      0% {
        clip: rect(42px, 9999px, 44px, 0);
      }
      5% {
        clip: rect(12px, 9999px, 59px, 0);
      }
      10% {
        clip: rect(48px, 9999px, 29px, 0);
      }
      15.0% {
        clip: rect(42px, 9999px, 73px, 0);
      }
      20% {
        clip: rect(63px, 9999px, 27px, 0);
      }
      25% {
        clip: rect(34px, 9999px, 55px, 0);
      }
      30.0% {
        clip: rect(86px, 9999px, 73px, 0);
      }
      35% {
        clip: rect(20px, 9999px, 20px, 0);
      }
      40% {
        clip: rect(26px, 9999px, 60px, 0);
      }
      45% {
        clip: rect(25px, 9999px, 66px, 0);
      }
      50% {
        clip: rect(57px, 9999px, 98px, 0);
      }
      55.0% {
        clip: rect(5px, 9999px, 46px, 0);
      }
      60.0% {
        clip: rect(82px, 9999px, 31px, 0);
      }
      65% {
        clip: rect(54px, 9999px, 27px, 0);
      }
      70% {
        clip: rect(28px, 9999px, 99px, 0);
      }
      75% {
        clip: rect(45px, 9999px, 69px, 0);
      }
      80% {
        clip: rect(23px, 9999px, 85px, 0);
      }
      85.0% {
        clip: rect(54px, 9999px, 84px, 0);
      }
      90% {
        clip: rect(45px, 9999px, 47px, 0);
      }
      95% {
        clip: rect(37px, 9999px, 20px, 0);
      }
      100% {
        clip: rect(4px, 9999px, 91px, 0);
      }
    }
    @keyframes glitch-animation-2 {
      0% {
        clip: rect(65px, 9999px, 100px, 0);
      }
      5% {
        clip: rect(52px, 9999px, 74px, 0);
      }
      10% {
        clip: rect(79px, 9999px, 85px, 0);
      }
      15.0% {
        clip: rect(75px, 9999px, 5px, 0);
      }
      20% {
        clip: rect(67px, 9999px, 61px, 0);
      }
      25% {
        clip: rect(14px, 9999px, 79px, 0);
      }
      30.0% {
        clip: rect(1px, 9999px, 66px, 0);
      }
      35% {
        clip: rect(86px, 9999px, 30px, 0);
      }
      40% {
        clip: rect(23px, 9999px, 98px, 0);
      }
      45% {
        clip: rect(85px, 9999px, 72px, 0);
      }
      50% {
        clip: rect(71px, 9999px, 75px, 0);
      }
      55.0% {
        clip: rect(2px, 9999px, 48px, 0);
      }
      60.0% {
        clip: rect(30px, 9999px, 16px, 0);
      }
      65% {
        clip: rect(59px, 9999px, 50px, 0);
      }
      70% {
        clip: rect(41px, 9999px, 62px, 0);
      }
      75% {
        clip: rect(2px, 9999px, 82px, 0);
      }
      80% {
        clip: rect(47px, 9999px, 73px, 0);
      }
      85.0% {
        clip: rect(3px, 9999px, 27px, 0);
      }
      90% {
        clip: rect(26px, 9999px, 55px, 0);
      }
      95% {
        clip: rect(42px, 9999px, 97px, 0);
      }
      100% {
        clip: rect(38px, 9999px, 49px, 0);
      }
    }
  </style>
</head>
<body>
    <div class="glitch" data-text="<?php echo http_response_code() . '">' . http_response_code(); ?></div>
    <br><br>
    <center><p>The Ministry of information is not happy you're here.<br>
    Leave at once, or face the consequences.</p>
  <p><a href="//loi.nayami.party/">Home</a>&emsp;<a href="//loi.nayami.party/">Home (In-game)</a></center>
</body>
</html>
