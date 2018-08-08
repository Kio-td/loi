<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (isset($_GET["auth"])) {
    require('/var/www/no-access/loi/config.php');
    if (isset($_GET["register"])) {
        if (!isset($_POST["un"]) || !isset($_POST["pw"]) || !isset($_POST["em"])) {
            header("Location: login.php?reg&x=3");
            die();
        }
        if (strlen($_POST["un"]) > 30) {
            header("Location: login.php?reg&x=5");
            die();
        }
        if (strlen($_POST["pw"]) < 8) {
            header("Location: login.php?reg&x=2");
            die();
        }
        $upl       = array(
            "username" => strtolower($_POST["un"]),
            "password" => password_hash($_POST["pw"], PASSWORD_DEFAULT),
            "email" => strtolower($_POST["em"]),
            "token" => password_hash(rand(), PASSWORD_DEFAULT),
            "cfe" => md5(rand())
        );
        $blacklist = array(
            "ikaros",
            "admin",
            "console",
            "sysadmin",
            "owner",
            "dev",
            "developer",
            "support",
            "superuser",
            "root",
            "system",
            "bot",
            "npc"
        );
        if (in_array(strtolower($upl["username"]), $blacklist)) {
            header("Location: login.php?reg&x=1");
            die();
        }
        $x = $conn->query("select username from users where username = '" . $upl["username"] . "'");
        if ($x->num_rows) {
            header("Location: login.php?reg&x=4");
            die();
        }

        $conn->query("INSERT INTO `users`(`username`, `password`, `email`, `token`, `ce`) VALUES ('" . $upl["username"] . "','" . $upl["password"] . "','" . $upl["email"] . "', '" . $upl["token"] . "', '" . $upl["cfe"] . "')");

        require('../base/head.php');
        sendmail("LOI>> Confirm your Email.", $upl["email"], $upl["username"], "Hello, " . $upl["username"] . ".\n\nThis is the Department of life and birth.\nTo completely be born as a citizen of Arven, please click the following link:\nhttps://" . $_SERVER['HTTP_HOST'] . "/game/login.php?confirm&username=" . $upl["username"] . "&confirm=" . $upl["cfe"] . "\n\nThank you,\nArven DOLB");
?>
     <a class="nav-link" href="login.php">Login</a>
    </nav>
  </div>
</header>
<main role="main" class="inner cover">
  <h1 class="cover-heading">Verify your email.</h1>
  <p class="lead">Your new life is awaiting. Please verify your email.</p>
  <?php
        require('../base/feet.php');


    } else {

    }

} else {
    echo "<style>input{color: white !important;}</style>";
    require('../base/head.php');
    if (isset($_GET["reg"])) {
?>
   <a class="nav-link" href="login.php">Login</a>
    <a class="nav-link active" href="login.php?reg">Register</a>
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
    <input class="form-control" name="un" placeholder="Username" required="" type="text"><br>
    <input class="form-control" id="pw" type="password" name="pw" required placeholder="Password"><br>
    <input class="form-control" type="email" name="em" required placeholder="Email"><br>
    <button class="btn btn-secondary">register</button>
  </form>

  <?php

    } else {
?>
 <a class="nav-link active" href="login.php">Login</a>
  <a class="nav-link" href="login.php?reg">Register</a>
</nav>
</div>
</header>
<main role="main" class="inner cover">
  <h1 class="cover-heading">Login</h1>
  <p class="lead">Welcome back. Login to continue your adventure.</p>
  <form id="log" action="?auth" method="post">
    <input class="form-control" name="un" placeholder="Username" required type="text"><br>
    <input class="form-control" name="pw" placeholder="Password" required type="password"><br>
    <button class="btn btn-secondary">Login</button>
    <?php

    }

    require('../base/feet.php');
}
?>
