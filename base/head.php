<?php
header("X-Frame-Options: Deny");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
$auth = false;
$websocket = "wss://ws.legendofikaros.me"; //The websocket of the backend server. Must be prefixed with either ws:// or wss://.
if (filter_input(INPUT_COOKIE,"token") !== false) {
  require "/var/www/no-access/loi/config.php";
  $n = $conn->escape_string(base64_decode(filter_input(INPUT_COOKIE,"token")));
  $c = $conn->query("SELECT bal, token, username from users where token = '".$n."'");
  if (!$c->num_rows) {
    $na = array("index.php", "login.php", "index", "login");
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
    <script src="/assets/jquery.slim.min.js"></script>
    <script src="/assets/jdetects.min.js"></script>
    <script src="/assets/json5.min.js"></script>
    <script src="/assets/pop.min.js"></script>
    <script src="/assets/bootstrap-material-design.min.js"></script>
    <script>json = JSON5; JSON = JSON5; websocket = "<?php echo htmlspecialchars($websocket); ?>"</script>
    <title>Legend of Ikaros</title>
    <link rel="stylesheet" href="/assets/bootstrap-material-design.min.css">
    <link href="/assets/cover.min.css" rel="stylesheet">
  <style>#content > #right > .dose > .dosesingle,
#content > #center > .dose > .dosesingle
{display:none !important;}</style></head>

  <body class="text-center" onload="">

    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
      <header class="masthead mb-auto cal">
        <div class="inner">
            <h3 class="masthead-brand">Legend of Ikaros</h3>
            <nav class="nav nav-masthead justify-content-center">
