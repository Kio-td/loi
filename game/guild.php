<?php require('../base/head.php');
//Set the price of making a guild
$mg = 2550;
?>
<a class="nav-link" href="index">Home</a>
<a class="nav-link" href="login?lo">Logout</a>
</nav>
</div>
</header> <?php
$p = $conn->query("select guild from users where username = '".$username."'");
$p = $p->fetch_assoc();
if($p["guild"] == 0 ) {
  if(isset($_GET["c"])) {
    if ($bal >= $mg) {

    } else {
      echo "<div class='alert alert-danger'>You need ". $mg - $bal . "Tn. more to create a guild.</div>";
    }
  }
  ?>
  <main role="main" class="inner cover">
    <h1 class="cover-heading">Guildmaster's home</h1>
    <p class="lead">Please choose an option.</p>
    <p class="lead"><a href="?c">Register a Guild</a>&emsp;<a href="?j">Join a Guild</a></p>
    <?php
  } else {


  }

  require('../base/feet.php');
