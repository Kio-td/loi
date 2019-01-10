<?php
if(!isset($_GET["pid"])) {
  header("Location: index");
  die();
}
require '/var/www/no-access/loi/config.php' ;
$x = (int) $_GET["pid"];
$p = $conn->query("SELECT username, bal, pfp from users where uid = ". $x);
if($p->num_rows == 0) {
  header("Location: index");
} else {
  $n = $p->fetch_assoc();
}
require '../base/head.php';



 ?>

 <h1 class="cover-heading">Welcome home, <?php echo ucfirst(htmlspecialchars($username)); ?>.</h1>
 <p class="lead">Please choose an option.</p>
 <?php
 require '../base/feet.php';
