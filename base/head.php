<?php
header("X-Frame-Options: Deny");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
function sendmail($sub, $mail, $user, $body) {
require '/var/www/loi/vendor/autoload.php';
$email = new \SendGrid\Mail\Mail();
$email->setFrom("Noreply@loi.nayami.party", "Legend of Ikaros");
$email->setSubject($sub);
$email->addTo($mail, $user);
$email->addContent("text/plain", $body);
$sendgrid = new \SendGrid("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");
try {
    $response = $sendgrid->send($email);
} catch (Exception $e) {
    echo 'We were unable to send your email. Please, notify Kio.';
}
}
$auth = false;
if (isset($_COOKIE["token"])) {
  require("/var/www/no-access/loi/config.php");
  $n = $conn->escape_string(base64_decode($_COOKIE["token"]));
  $c = $conn->query("SELECT bal, token, username from users where token = '".$n."'");
  if (!$c->num_rows) {
    $na = array("index.php", "login.php", "index", "login", "login-");
  //  if(in_array(basename($_SERVER['PHP_SELF']), $na)) {header("Location: index.php");}
} else {
  $auth = true;
  $c = $c->fetch_assoc();
  $username = ucfirst($c["username"]);
  $bal = $c["bal"];
}
}
$x = false;
?>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Team LOI">
    <script src="/node_modules/jquery/dist/jquery.slim.min.js"></script>
    <script src="/node_modules/bootstrap-material-design/dist/js/bootstrap-material-design.min.js"></script>
    <script src="/node_modules/jdetects/jdetects.min.js"></script>
    <script src="/node_modules/popper.js/dist/popper.min.js"></script>
    <script src="/node_modules/tooltip.js/dist/tooltip.min.js"></script>
    <script src="/node_modules/json5/dist/index.min.js"></script>
    <script>
      json = JSON5;
      function err(info) {
  document.getElementById('ap').innerHTML += '<div class="alert alert-warning alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}
function inf(info) {
  document.getElementById('ap').innerHTML += '<div class="alert alert-info alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}
function suc(info) {
  document.getElementById('ap').innerHTML += '<div class="alert alert-success alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}

$(document).ready(function() { $('body').bootstrapMaterialDesign(); });
var t = false
jdetects.create({
  once: true,
	onchange: function(status) {
      console.clear();
      console.error("!!!GAME MAYBE TAMPERED WITH NOW!!!\nI have closed all connections. Please close DevConsole and restart the game.");
	}
});
    </script>
    <title>Legend of Ikaros</title>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css" integrity="sha384-wXznGJNEXNG1NFsbm0ugrLFMQPWswR3lds2VeinahP8N0zJw9VWSopbjv2x7WCvX" crossorigin="anonymous">
    <link href="/base/cover.css" rel="stylesheet">
    <link rel="author" href="humans.txt" />
  <style>#content > #right > .dose > .dosesingle,
#content > #center > .dose > .dosesingle
{display:none !important;}</style></head>

  <body class="text-center">

    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
      <header class="masthead mb-auto cal">
        <div class="inner">
            <h3 class="masthead-brand">Legend of Ikaros</h3>
            <nav class="nav nav-masthead justify-content-center">
