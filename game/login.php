<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
if (isset($_GET["auth"])) {
    require('/var/www/no-access/loi/config.php');
  if (isset($_GET["register"])) {
    if(!isset($_POST["un"]) || !isset($_POST["pw"]) || !isset($_POST["em"])) {
      header("Location: login.php?reg&x=3");
    }
    if (strlen($_POST["un"]) > 30) {
      header("Location: login.php?reg&x=5");
    }
    $upl = array(
      "username" => $_POST["un"],
      "password" => password_hash($_POST["pw"]),
      "email" => $_POST["em"]
    );
    $blacklist = array("ikaros", "admin", "console", "sysadmin", "owner", "dev", "developer", "support", "superuser", "root", "system", "bot", "npc");
    if(in_array(strtolower($upl["username"]), $blacklist)) {
      header("Location: login.php?reg&x=1");
    }
    if(strlen($upl["password"] > 8)) {
      header("Location: login.php?reg&x=2");
    }
    if($conn->query("select username from users where username = '" . $upl["username"] . "'")->num_rows) {header("localtion: login.php?reg&x=4");}

    $conn->query("INSERT INTO `users`(`username`, `password`, `email`, `cemail`) VALUES ('".$upl["username"]."','".$upl["password"]."','".$upl["email"]."', 0)");


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
      if(isset($_GET["x"])) {
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
    <input type="checkbox" class="form-control" required>I'm over 18.<br>
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
