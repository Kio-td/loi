<?php
    echo "<style>input{color: white !important;}</style>";
    require('../base/head.php');
?>
</nav>
</div>
</header>
<main role="main" class="inner cover">

  <h1 class="cover-heading">Login</h1>
  <p class="lead">Welcome back. Please login to link your account.</p>
  <p>The person requesting your LOI Account be linked is responsible for any misuse.</p>
  <p>If you need to register, then click <u><a href="https://loi.nayami.party/login?reg">here</a></u>, and login here when you're finished.</p>
    <input class="form-control" id="un" placeholder="Username" required type="text"><br>
    <input class="form-control" id="pw" placeholder="Password" required type="password"><br>
    <button onclick="auth()" class="btn btn-secondary">Link</button>

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
            document.cookie = "token=" + data.data;
            window.location = "index";
          }
        }
        s.send(json.stringify({cmd:'auth', data:{un:document.getElementById('un').value, pw:document.getElementById('pw').value}}))
      }
    </script>
    <?php
    require('../base/feet.php');
?>
