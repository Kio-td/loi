<?php

if (isset($_GET["auth"])) {
    require('/var/www/no-access/loi/config.php');
  if (isset($_GET["register"])) {


  }


}

require('../base/head.php');

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
    <input class="form-control" id="Un" placeholder="Username" value="" required="" style="cursor: auto;" type="text">&emsp;<input type="password" name="pw" placeholder="Password"><br><br>
    <input type="email" name="em" placeholder="Email">
  </form>


  <?php

} else {


}

require('../base/feet.php');

 ?>
