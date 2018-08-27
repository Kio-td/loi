<?php require('../base/head.php');
//Set the price of making a guild
$mg = 5250;
?>
<a class="nav-link" href="index">Home</a>
<a class="nav-link" href="login?lo">Logout</a>
</nav>
</div>
</header> <?php
$p = $conn->query("select guild from users where username = '".$username."'");
$p = $p->fetch_assoc();
if($p["guild"] == 0 ) {
  echo "a";
  if(isset($_GET["c"])) {
    echo "b";
    if ($bal >= $mg) {
      ?>
        <main role="main" class="inner cover">
          <h1 class="cover-heading">Create a Guild</h1>

      <?php
    die();
  }
}elseif (isset($_GET["j"])) {

}
  ?>
  <main role="main" class="inner cover">
    <?php if (isset($_GET["c"])) {  echo "<div class='alert alert-danger'>You need " . ($mg - $bal) . "Tn. more to create a guild.</div>";}?>
    <h1 class="cover-heading">Guildmaster's home</h1>
    <p class="lead">Please choose an option.</p>
    <p class="lead"><a href="?c">Register a Guild</a>&emsp;<a href="?j">Join a Guild</a></p>
    <?php
  } else {


  }

  require('../base/feet.php');
