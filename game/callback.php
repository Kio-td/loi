<?php
if(isset($_GET["data"])) {
  require '/var/www/no-access/loi/config.php');
  $x = $conn->query("SELECT username, email, bal from users where uid in (SELECT uid from oauthtokens where authid = '".$_GET["data"]."')");
  if(!$x->num_rows == 1) die ('{"error":"data incorrect"}');
  $r = $x->fetch_assoc();
  echo json_encode(array(
	"success" => 0,
	"data" => array(
		"username" => $r["username"],
		"email" => $r["email"],
		"balance" => $r["bal"]
	)
	));

} else {
    echo "<style>input{color: white !important;}.small{font-size:.5rem}</style>";
    require '../base/head.php';
//    if(0) {
   if(!isset($_GET["return"])) {
      ?>
    </nav></div></header><main role="main" class="inner cover">
      <h1 class="cover-heading">Something went wrong.</h1>
      <p class="lead">Please go back and try again. If this issue persists, please contact the webmaster of the site who brought you here.</p>
    </main>
      <?php
      require '../base/feet.php';
      die();
    }
?>
</nav>
</div>
</header>
<main role="main" class="inner cover">

  <h1 class="cover-heading">Link your account</h1>
  <p class="lead">Welcome back. Please login to link your account.</p>
    <input class="form-control" id="un" placeholder="Username" required type="text"><br>
    <input class="form-control" id="pw" placeholder="Password" required type="password"><br>
    <button onclick="auth()" class="btn btn-secondary">Link</button><br>
<div class="small">
    <p>The person requesting your LOI Account be linked is responsible for any misuse.<br>
      If you need to register, then click <u><a href="/login?reg">here</a></u>, and login here when you're finished.</p>
</div>
</main>
    <script>
      s = new WebSocket(websocket + "/anon");
      function auth() {
        s.onmessage = function(evt) {
          data = json.parse(evt.data);
          if(data.ok == false) {
            switch (data.msg) {
              case "CONF_EMAIL":
                err("Please look in your inbox for the confirmation email.")
              break;
              case "INC_PASS":
                err("Your username or password is incorrect.")
              break;
              case "NOBODY_FOUND":
                err("Your username or password is incorrect.")
              break;
              default:
                window.location = "<?php echo htmlspecialchars($_GET["return"]); ?>" + "?token="+data.data.token+"&username="+data.data.username;
              break;
            }
        }
        s.send(json.stringify({cmd:'authcallback', data:{un:document.getElementById('un').value, pw:document.getElementById('pw').value}}))
      }
    </script>
    <?php
    require '../base/feet.php';
  }
?>
