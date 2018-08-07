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
  <h1 class="cover-heading">You're not logged in.</h1>
  <p class="lead">Use the links up top if you'd like to continue your quest.</p>


  <?php

} else {


}

require('../base/feet.php');

 ?>
