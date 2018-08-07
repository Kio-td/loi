<?php

if (isset($_GET["auth"])) {
    require('/var/www/no-access/loi/config.php');
  if (isset($_GET["register"])) {


  } else {

  }


}
echo "<style>input{color: white !important;}</style>";
require('../base/head.php');


if (isset($_GET["reg"])) {

  ?>
  <a class="nav-link" href="login.php">Login</a>
  <a class="nav-link active" href="login.php?reg">Register</a>
  </nav>
  </div>
  </header>

  <main role="main" class="inner cover">
  <h1 class="cover-heading">Registration</h1>
  <p class="lead">Register your existance with the department.</p>
  <form id="reg" action="?auth&register" method="post">
    <input class="form-control" name="un" placeholder="Username" required="" type="text"><br>
    <input class="form-control" id="pw" type="password" name="pw" required placeholder="Password"><br>
    <input class="form-control" type="email" name="em" required placeholder="Email"><br>
    <input type="hidden" name="salt">
    <input type="hidden" name="iv">
    <input class="btn btn-secondary" type="submit" value="Register">
    <button onclick="register()" class="btn btn-secondary">register</button>
  </form>

  <script>
  function register() {
    let possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
text = ""
for (let i = 0; i < 40; i++)
text += possible.charAt(Math.floor(Math.random() * possible.length));
iv = ""
for (let i = 0; i < 40; i++)
iv += possible.charAt(Math.floor(Math.random() * possible.length));
  antex =  CryptoJS.AES.encrypt(document.getElementById('pw').value, text, {iv: iv});
  document.getElementById('pw').value = antex.ciphertext.toString(lib_crypt.CryptoJS.enc.Base64);
  document.getElementById('salt').value = text;
  document.getElementById('iv').value = iv;
  }
</script>

  <?php

} else {
?>
<a class="nav-link" href="login.php">Login</a>
<a class="nav-link active" href="login.php?reg">Register</a>
</nav>
</div>
</header>
<main role="main" class="inner cover">
<h1 class="cover-heading">Login</h1>
<p class="lead">Welcome back. Login to continue your adventure.</p>
<form id="log" action="?auth" method="post">
  <input class="form-control" name="un" placeholder="Username" required type="text"><br>
  <input class="form-control" name="pw" placeholder="Password" required type="password"><br>
  <button onclick="login()" class="btn btn-secondary">Login</button>
<?php

}

require('../base/feet.php');

 ?>
