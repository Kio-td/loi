<?php
require('./head/smail.php');
$auth = false;
if (isset($_COOKIE["token"])) {
  require("/var/www/no-access/loi/config.php");
  $n = $conn->escape_string(base64_decode($_COOKIE["token"]));
  $c = $conn->query("SELECT bal, token, username from users where token = '".$n."'");
  if (!$c->num_rows) {
    $na = array("index.php", "login.php");
    if(in_array(basename($_SERVER['PHP_SELF'], $na))) {header("Location: index.php");}
} else {
  $auth = true;
  $c = $c->fetch_assoc();
  $username = ucfirst($c["username"]);
  $bal = $c["bal"];
}
}
$x = false;
require('./head/temp.php');
?>
