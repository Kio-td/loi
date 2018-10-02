<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (isset($_GET["confirm"])) {
  if(!isset($_GET["username"]) || !isset($_GET["confirm"])) {
    header("location: login");
    die();
  } else {
    require('/var/www/no-access/loi/config.php');
    $un = $conn->escape_string($_GET["username"]);
    $token = $conn->escape_string($_GET["confirm"]);
    $x = $conn->query("SELECT ce FROM users where username = '".$un."' and ce = '".$token."'");
    if($x->num_rows) {
      //if exists
      $conn->query("UPDATE `users` SET `ce`=0 WHERE username = '".$un."'");
      header("Location: login?x=0");
      die();
    } else {
      //if doesnt exist
      header('Location: login');
      die();
    }
  }
}
elseif (isset($_GET["lo"])) {
  setcookie("token", '' , time() - 3600);
  header("Location: index");
} else {
    echo "<style>input{color: white !important;}</style>";
    require('../base/head.php');
    if (isset($_GET["reg"])) {
?>
   <a class="nav-link" href="login">Login</a>
    <a class="nav-link active" href="login?reg">Register</a>
  </nav>
</div>
</header>
<main role="main" class="inner cover">
  <h1 class="cover-heading">Registration</h1>
  <p class="lead">Register your existance with the department.</p>
    <input class="form-control" id="un" placeholder="Username" required="" onfocus="us()" oninput="cun(this)" type="text"><br>
    <input class="form-control" id="pw" type="password"required placeholder="Password"><br>
    <input class="form-control" id="em" type="email" onfocus="emai()" required oninput="cem(this)" placeholder="Email"><br>
    <select class="form-control" id="data" required onchange="f(this)"></select><br>
   <!-- <button onclick="sub()" class="btn btn-secondary">register</button>&emsp;<a href="/game/reset">Reset Password</a>-->
<span id="info" style="display: none"></span>
<script>
var s = new WebSocket("wss://ws.nayami.party/anon");
data = "";
i = 0
dx = 0
e = 0
u = 0
nrk = "";
function emai() {
  s.onmessage = function (evt) {
    data = json.parse(evt.data).data;
    if (data == false) {
      err("That email is already used.");
    }
  }
  console.log("Switched to Email.");
}
function us() {
  s.onmessage = function (evt) {
    data = json.parse(evt.data).data;
    if (data == "F") {
      err("Your username is already used.");
    } else if (data == "BL") {
      err("Your username is on the blacklist.");
    }
  }
  console.log("Switched to Username.");
}

function sub() {
  s.onmessage = function (evt) {
    data = json.parse(evt.data);
    if (data.ok == false) {
      if (data.msg == "ACCT_BLACKLIST") { err("This username has been blacklisted.");}
      else if (data.msg == "ACCT_EXISTS") { err("This Username or Email already exists.");}
    } else {
      suc("Check your email to confirm your account.<br>Welcome to Arden.");
    }
  }
  s.send(json.stringify({cmd: 'create', data: {un: document.getElementById('un').value, pw:document.getElementById('pw').value, em:document.getElementById('em').value, sp:document.getElementById('data').value}}));
}

 s.onmessage = function (evt) {
                if(json.parse(evt.data)["code"] == 3) {
                  s.send("{cmd:'species'}");
                } else {
                  nrk = json.parse(evt.data).data;
                  nrk.forEach(function(itm) {
                    i++;
                    x = document.getElementById('data');
                    h = document.createElement("option");
                    h.text = itm["sname"];
                    h.value = itm["sid"];
                    x.add(h);
                    if (i == 1) document.getElementById('info').innerText = itm["description"]
                  });
                }
                dx = 1;
             }
             function f (id) {
               nrk.forEach(function(itm) {
                 if (itm["sid"] == id.value) {
                   document.getElementById('info').innerText = itm["description"]
                 }
               });
             }

             function cun (id) {
               s.send(json.stringify({cmd:"cun", data:id.value}))
             }
             function cem (id) {
               s.send(json.stringify({cmd:"cem", data:id.value}));
             }
</script>
  <?php

    } else {
?>
 <a class="nav-link active" href="login">Login</a>
  <a class="nav-link" href="login?reg">Register</a>
</nav>
</div>
</header>
<main role="main" class="inner cover">

  <h1 class="cover-heading">Login</h1>
  <p class="lead">Welcome back. Login to continue your adventure. </p>
    <input class="form-control" id="un" placeholder="Username" required type="text"><br>
    <input class="form-control" id="pw" placeholder="Password" required type="password"><br>
    <button onclick="auth()" class="btn btn-secondary">Login</button>

    <script>
      s = new WebSocket("wss://ws.nayami.party/anon");
      function auth() {
        s.onmessage = function(evt) {
          data = json.parse(evt.data);
          if(data.ok == false) {
            if(data.msg == "CONF_EMAIL") {err("Please look in your inbox for the confirmation email.")}
            else if (data.msg == "INC_PASS") {err("Your username or password is incorrect.")}
            else if (data.msg == "NOBODY_FOUND") {err("Your username or password is incorrect.")}
          } else {
            document.cookie = "token=" + data.data;
            window.location = "index";
          }
        }
        s.send(json.stringify({cmd:'auth', data:{un:document.getElementById('un').value, pw:document.getElementById('pw').value}}))
      }
    </script>
    <?php

    }

    require('../base/feet.php');
}
?>
