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
  if(isset($_GET["c"])) {
    if ($bal >= $mg) {
      ?>
        <main role="main" class="inner cover">
          <h1 class="cover-heading">Create a Guild</h1>
          <p class="lead">This will cost <?php echo $mg; ?>.</p>
          <form>
            <input type="text" name="gid" class="form-control" placeholder="Guild Name"><br>
            <input type="text" id="tag1" name="sg" class="form-control" oninput="tr()" placeholder="Guild tag (Max 4 Characters)">
            <p class="vg">Your Guild tag will look like this:<br>
            <span id="tag"></span><?php echo $username; ?></p>

          </form>
          <script>
            function tr() {
              if (document.getElementById('tag1').value == "") {
                document.getElementById('tag').innerText = "";
              }
              document.getElementById('tag').innerText = "[" + document.getElementById('tag1').value + "]";
            }
          </script>
      <?php
    require('../base/feet.php');
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
