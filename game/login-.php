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
}
elseif (isset($_GET["auth"])) {
    require('/var/www/no-access/loi/config.php');
    if (isset($_GET["register"])) {
        if (!isset($_POST["un"]) || !isset($_POST["pw"]) || !isset($_POST["em"])) {
            header("Location: login?reg&x=3");
            die();
        }
        if (strlen($_POST["un"]) > 30) {
            header("Location: login?reg&x=5");
            die();
        }
        if (strlen($_POST["pw"]) < 8) {
            header("Location: login?reg&x=2");
            die();
        }
        $upl       = array(
            "username" => strtolower($_POST["un"]),
            "password" => password_hash($_POST["pw"], PASSWORD_DEFAULT),
            "email" => strtolower($_POST["em"]),
            "token" => password_hash(md5(rand()), PASSWORD_DEFAULT),
            "cfe" => md5(rand())
        );
        $blacklist = array("ikaros", "admin", "console", "sysadmin", "owner", "dev", "developer", "support", "superuser", "root", "system", "bot", "npc"
        );
        if (in_array(strtolower($upl["username"]), $blacklist)) {
            header("Location: login?reg&x=1");
            die();
        }
        $x = $conn->query("select username from users where username = '" . $upl["username"] . "'");
        if ($x->num_rows) {
            header("Location: login?reg&x=4");
            die();
        }

        $conn->query("INSERT INTO `users`(`username`, `password`, `email`, `token`, `ce`) VALUES ('" . $upl["username"] . "','" . $upl["password"] . "','" . $upl["email"] . "', '" . $upl["token"] . "', '" . $upl["cfe"] . "')");

        require('../base/head.php');
        sendmail("LOI>> Confirm your Email.", $upl["email"], $upl["username"], "Hello, " . $upl["username"] . ".\n\nThis is the Department of life and birth.\nTo completely be born as a citizen of Arven, please click the following link:\nhttps://" . $_SERVER['HTTP_HOST'] . "/game/login?confirm&username=" . $upl["username"] . "&confirm=" . $upl["cfe"] . "\n\nThank you,\nArven DOLB");
?>
     <a class="nav-link" href="login">Login</a>
    </nav>
  </div>
</header>
<main role="main" class="inner cover">
  <h1 class="cover-heading">Verify your email.</h1>
  <p class="lead">Your new life is awaiting. Please verify your email.</p>
  <?php
        require('../base/feet.php');


    } else {
      //logging in
      if(isset($_POST["un"]) && isset($_POST["pw"])) {
        //Username and password are present
        require("/var/www/no-access/loi/config.php");
        $u = $conn->escape_string(strtolower($_POST["un"]));
        $p = $conn->escape_string($_POST["pw"]);
        $n = $conn->query("SELECT password, ce, token from users where username = '".$u."'");
        if ($n->num_rows) {
          //User is present
          $r = $n->fetch_assoc();
          if (password_verify($p, $r["password"])) {
            if ($r["ce"] == "0") {
              //User can be fully authenticated
              setcookie("token", base64_encode($r["token"]));
              header("Location: index");
              require('../base/head.php');
              ?>
              <a class="nav-link" href="index">Home</a>
            </nav>
          </div>
          </header>
          <main role="main" class="inner cover">
            <h1 class="cover-heading">Logged in</h1>
            <p class="lead">You are now being redirected back to the game...</p>
              <?php
              require('../base/feet.php');
            } else {
              //User has uncomfirmed email
              header("Location: login?x=2");
            }
          } else {
            //Password is false
            header("Location: login?x=1");
          }
        } else {
          //User is not present
          header("Location: login?x=1");
        }

      } else {
        //Username and password are not present
        header("Location: login?x=1");
        die();
      }

    }

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
  <?php
        if (isset($_GET["x"])) {
            echo '<div class="alert alert-danger" role="alert">';
            switch ($_GET["x"]) {
                case '1':
                    echo "You attempted to use a blacklisted username.";
                    break;
                case '2':
                    echo "Your password is not long enough.";
                    break;
                case '3':
                    echo "Require is there for a reason.";
                    break;
                case '4':
                    echo "The username chosen is not available.";
                    break;
                case '5':
                    echo "The username is beyond 30 characters.";
            }
            echo "</div>";
        }

?>

  <h1 class="cover-heading">Registration</h1>
  <p class="lead">Register your existance with the department.</p>
  <form id="reg" action="?auth&register" method="post">
    <input class="form-control" name="un" placeholder="Username" required="" onfocus="u()" oninput="cun(this)" type="text"><br>
    <input class="form-control" id="pw" type="password" name="pw" required placeholder="Password"><br>
    <input class="form-control" type="email" name="em" onfocus="e()" required oniput="cem(this)" placeholder="Email"><br>
    <select class="form-control" id="data" name="sp" required onchange="f(this)"></select><br>
    <button class="btn btn-secondary">register</button>&emsp;<a href="/game/reset">Reset Password</a>
  </form>
<span id="info"></span>
<script>
json = JSON5;
var s = new WebSocket("wss://loi.nayami.party:2053/anon");
data = "";
i = 0
dx = 0
e = 0
u = 0
nrk = "";
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
             function e() {
               s.onmessage = function (evt) {
                 data = json.parse(evt.data).data;
                 console.log(data);
                 if (data == false) {
                   err("That email is already used.");
                 }
               }
             }
             function u() {
               s.onmessage = function (evt) {
                 data = json.parse(evt.data).data;
                 console.log(data);
                 if (data == "F") {
                   err("Your username is already used.");
                 } else if (data == "BL") {
                   err("Your username is on the blacklist.");
                 }
               }
             }
             function cun (id) {
               s.send(json.stringify({cmd:"cun", data:id.value}))
             }
             function cem (id) {
               s.send(json.stringify({cmd:"cem", data:id.value}))
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
  <?php
  if(isset($_GET["x"])) {
  switch ($_GET["x"]) {
    case '0':
      echo "<div class='alert alert-success' role='alert'>Your account has been activated.</div>";
      break;
    case '1':
      echo "<div class='alert alert-danger' role='alert'>The information provided didn't match our records.<br>
  Need a <a href='reset'>reset</a>?</div>";
      break;
    case '2':
      echo "<div class='alert alert-danger' role='alert'>Please confirm your email.</div>";
      break;
  }
}
   ?>
  <h1 class="cover-heading">Login</h1>
  <p class="lead">Welcome back. Login to continue your adventure. </p>
  <form id="log" action="?auth" method="post">
    <input class="form-control" name="un" placeholder="Username" required type="text"><br>
    <input class="form-control" name="pw" placeholder="Password" required type="password"><br>
    <button class="btn btn-secondary">Login</button>
  </form>
    <?php

    }

    require('../base/feet.php');
}
?>
