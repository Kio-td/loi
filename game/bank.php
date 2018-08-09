<?php
require("../base/head.php");
 ?>
 <a class="nav-link" href="index">Home</a>
 <a class="nav-link" href="login?lo">Logout</a>
 </nav>
 </div>
 </header>


<?php
if (isset($_GET["transfer"])) {
  if(isset($_POST["amnt"]) && isset($_POST["un"])) {
    $no = (int) $_POST["amnt"];
    $x = $conn->escape_string(strtolower($_POST["un"]));
    $n = $conn->query("Select username from users where username = '".$x."'");
    if($username === $x) {
      ?>
      <main role="main" class="inner cover">
        <div class="alert alert-danger" role="alert">Please don't send money to yourself.</div>
        <h1 class="cover-heading">Transaction info-</h1>
        <p class="lead">Give some money to another user on our system.</p>
        <form method="post">
          <input type="text" required class="form-control" name="un" placeholder="Username"><br>
          <input type="number" required class="form-control" name="amnt" min="1" max="<?php echo $bal; ?>" placeholder="Amount"><br>
          <input type="submit" class="btn btn-secondary" value="Send">
        </form>
      <?php
    }
    if($no > $bal || $no < 0) {
      ?>
      <main role="main" class="inner cover">
        <div class="alert alert-danger" role="alert">We have limits for a reason.</div>
        <h1 class="cover-heading">Transaction info-</h1>
        <p class="lead">Give some money to another user on our system.</p>
        <form method="post">
          <input type="text" required class="form-control" name="un" placeholder="Username"><br>
          <input type="number" required class="form-control" name="amnt" min="1" max="<?php echo $bal; ?>" placeholder="Amount"><br>
          <input type="submit" class="btn btn-secondary" value="Send">
        </form>

      <?php
    }
    if(!$n->num_rows) {
      ?>
      <main role="main" class="inner cover">
        <div class="alert alert-danger" role="alert">That user doesn't exist.</div>
        <h1 class="cover-heading">Transaction info-</h1>
        <p class="lead">Give some money to another user on our system.</p>
        <form method="post">
          <input type="text" required class="form-control" name="un" placeholder="Username"><br>
          <input type="number" required class="form-control" name="amnt" min="1" max="<?php echo $bal; ?>" placeholder="Amount"><br>
          <input type="submit" class="btn btn-secondary" value="Send">
        </form>

      <?php
    }

  } else {
  ?>
 <main role="main" class="inner cover">
   <h1 class="cover-heading">Transaction info-</h1>
   <p class="lead">Give some money to another user on our system.</p>
   <form method="post">
     <input type="text" required class="form-control" name="un" placeholder="Username"><br>
     <input type="number" required class="form-control" name="amnt" min="1" max="<?php echo $bal; ?>" placeholder="Amount"><br>
     <input type="submit" class="btn btn-secondary" value="Send">
   </form>
<?php
}
} else {


?>
 <main role="main" class="inner cover">
   <h1 class="cover-heading">Good day, <?php echo ucfirst($username); ?>.</h1>
   <p class="lead">Welcome to the bank. Your current account balance: <?php echo $bal; ?>Tn.</p>
   <p class="lead">Options: <a href="?transfer">Transfer</a></p>

<?php
}
require("../base/feet.php");?>
