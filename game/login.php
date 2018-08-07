<?php

if (isset($_GET["auth"])) {
    require('/var/www/no-access/loi/config.php');
  if (isset($_GET["register"])) {


  } else {

  }


}

require('../base/head.php');
echo "<style>input{color: white !important;}</style>";

if (isset($_GET["reg"])) {

  ?>
  <a class="nav-link" href="login.php">Login</a>
  <a class="nav-link active" href="login.php?reg">Register</a>
  </nav>
  </div>
  </header>

  <main role="main" class="inner cover">
  <h1 class="cover-heading">Registration</h1>
  <p class="lead">Register your existance with the department.</p>
  <form action="?auth&register" method="post">
    <input class="form-control" name="un" placeholder="Username" required="" type="text"><br>
    <input class="form-control" type="password" name="pw" required placeholder="Password"><br>
    <input class="form-control" type="email" name="em" required placeholder="Email"><br>
    <input class="btn btn-secondary" type="submit" value="Register">
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
<p class="lead">Log back in to continue your quest.</p>

<?php

}

require('../base/feet.php');

 ?>
