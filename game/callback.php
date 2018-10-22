<?php
    echo "<style>input{color: white !important;}.small{font-size:.5rem}</style>";
    require('../base/head.php');
    if(!isset($_GET["return"])) {
      ?>
    </nav></div></header><main role="main" class="inner cover">
      <h1 class="cover-heading">Something went wrong.</h1>
      <p class="lead">Please go back and try again. If this issue persists, please contact the webmaster of the site who brought you here.</p>
    </main>
      <?php
      require('../base/feet.php');
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
      If you need to register, then click <u><a href="https://loi.nayami.party/login?reg">here</a></u>, and login here when you're finished.</p>
</div>
    <script>
      s = new WebSocket("wss://ws.nayami.party/anon");
      function auth() {
        s.onmessage = function(evt) {
          data = json.parse(evt.data);
          if(data.ok == false) {
            if(data.msg == "CONF_EMAIL") {err("Please look in your inbox for the confirmation email.")}
            else if (data.msg == "INC_PASS") {err("Your username or password is incorrect.")}
            else if (data.msg == "NOBODY_FOUND") {err("Your username or password is incorrect.")}
          } else {
            window.location = <?php echo urlencode($_GET["return"]); ?> + "?token="+data.token+"&username="+data.username;
          }
        }
        s.send(json.stringify({cmd:'authcallback', data:{un:document.getElementById('un').value, pw:document.getElementById('pw').value}}))
      }
    </script>
  </div>
    <?php
    require('../base/feet.php');
?>
