<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/bootstrap-material-design.min.css">
  <title>Legend of Ikaros</title>

  <style>
  body {
    background: #353535;
    transition: 4s;
    color: white;
  }
  .box {
    position: relative;
    border: 1px solid white;
  }
  .spritebox {
    top: 35vh;
    height: 15vh;
    width: 5vw;
  }
  .writingbox {
    text-align: left;
    padding: 2vh;
    top: 45vh;
    height: 15vh;
    width: 50vw;
  }
  .btn {
    position: fixed;
    top: 42vh;
    left: 42vw;

  }
  .btn-secondary,
  .btn-secondary:hover,
  .btn-secondary:focus {
    color: #333;
    text-shadow: none;
    background-color: #fff;
    border: .05rem solid #fff;
  }
  </style>
</head>
<body>
    <a href="/game/login?reg" class="btn btn-lg btn-secondary">Continue Anyways</a>
  <center>
  <div class=" spritebox"></div>
  <div class="box writingbox"></div>
</center>
  <script src="../assets/popper.min.js"></script>
  <script src="../assets/jquery.slim.min.js"></script>
  <script src="../assets/bootstrap-material-design.min.js"></script>
  <script src="../assets/howler.min.js"></script>
  <script src="../assets/typeit.min.js"></script>
  <script>
  $('.writingbox').hide();
   $("body").css("background", "black");
  $('.writingbox').delay(2200).fadeIn();
  setTimeout(function(){ document.title = "Interesting." }, 2200);
  $(".btn").hide();
  var beep = new Howl({volume: 0.05, src: ["../assets/beep.ogg"]});
  var underworld = new Howl({src: ["../assets/underworld.mp3"], autoplay: true, loop: false,});
    x = 0;
    y = 1;
function t (s,q,i) {
  console.log(s);
    if(s[2] == "first-of-string") {x = 1; y += 1;}
    if(s[2] == "last-of-string") {x = 2}
    if(x) {
      if (x == 2) {x = 0}
      if (y == 2 || y == 0) {y = 0; beep.play()}
    }
  }
  function o () {
    $(".spritebox").fadeOut();
    $(".writingbox").fadeOut();
    $(".btn").fadeIn();
  }
  new TypeIt('.writingbox', {speed: 90, afterComplete: function(s,q,i) {o()}, afterStep: function (s,q,i) {t(s,q,i)}})
  .pause(3000)
  .type("Interesting.")
  .pause(500)
  .break()
  .type("You think you're special.")
  .pause(500)
  .break()
  .type("You think you can save her.")
  .pause(1500)
  .empty()
  .type("...")
  .pause(1500)
  .empty()
  .type("I hope you understand that the rules apply only to you.")
  .pause(500)
  .break()
  .pause(1500)
  .options({speed: 400})
  .type("Not to me.")
  .pause(1000);


  $(document).ready(function() { $('body').bootstrapMaterialDesign(); });

  </script>
</body>
</html>
