<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (filter_input(INPUT_GET,"confirm")) {
  if(!isset(filter_input(INPUT_GET,"username")) || !isset(filter_input(INPUT_GET,"confirm"))) {
    header("location: login");
    die();
  } else {
    require '/var/www/no-access/loi/config.php';
    $un = $conn->escape_string(filter_input(INPUT_GET,"username"));
    $token = $conn->escape_string(filter_input(INPUT_GET,"confirm"));
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
elseif (isset(filter_input(INPUT_GET,"reset"))) {
    require '../base/head.php';
    echo "<span class='nav-link'>&emsp;</span>";
    ?>
  </nav>
</div>
</header>
<main role="main" class="inner cover">
  <h1 class="cover-heading">Reset</h1>
  <p class="lead">Reset your password, to gain entry.</p>
  <input class="form-control" id="pw" placeholder="Password" required=""><br>
  <input class="form-control" id="cpw" placeholder="Confirm" required=""><br>
  <input type="hidden" id="token" value="<?php echo htmlspecialchars(filter_input(INPUT_GET,'code'));?>"><br>
  <button onclick="zen()" class="btn btn-secondary">Reset</button>
    <?php
    require '../base/feet.php';
}
elseif (isset(filter_input(INPUT_GET,"lo"))) {
  setcookie("token", '' , time() - 3600);
  header("Location: index");
} else {
    echo "<style>input{color: white !important;}</style>";
    require '../base/head.php';
    if (isset(filter_input(INPUT_GET,"reg"))) {
?>
   <a class="nav-link" href="login">Login</a>
    <a class="nav-link active" href="#">Register</a>
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
   <button onclick="sub()" class="btn btn-secondary">register</button>
<span id="info" style="display: none"></span>
<script src="/assets/register.min.js"></script>
  <?php

    } else {
?>
 <a class="nav-link active" href="login">Login</a>
  <a class="nav-link" href="begin">Register</a>
</nav>
</div>
</header>
<main role="main" class="inner cover">

  <h1 class="cover-heading">Login</h1>
  <p class="lead">Welcome back. Login to continue your adventure. </p>
    <input class="form-control" id="un" placeholder="Username" required type="text"><br>
    <input class="form-control" id="pw" placeholder="Password" required type="password"><br>
    <button onclick="auth()" class="btn btn-secondary">Login</button>
    <script src="/assets/login.min.js"></script>
    <?php

    }

    require '../base/feet.php';
}
?>
