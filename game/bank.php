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
    elseif($no > $bal || $no < 0) {
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
    elseif(!$n->num_rows) {
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
    } else {
      if(isset($_POST["confirm"])) {
        $ticket = uniqid("tfr_");
        $conn->query("UPDATE `users` SET `bal`=".$bal - ($no + ceil($no * 0.15))." WHERE username = '".$username."'");
        $conn->query("UPDATE `users` SET `bal`=".$bal + $no." WHERE username = '".$x."'");
        die($conn->error);
        $conn->query("INSERT INTO `ticket`(`tid`, `ufrom`, `uto`, `amnt`) VALUES ('$ticket', '$username', '$x', '$no')");
        ?>
          <h1 class="cover-heading">Sent</h1>
          <p class="lead">A charge of <?php echo $no + ceil($no * 0.15); ?>Tn. was deducted from your account, and <?php echo $no; ?>Tn. has been sent to <?php echo $x; ?>.</p>
          <p class="lead">Transaction ID: <?php echo $ticket; ?></p>
          <a class="btn btn-info" href="info">Home</a>
        <?php
      } else {
        ?>
          <h1 class="cover-heading">Confirm</h1>
          <p class="lead">Are you sre you would like to send <?php echo $no . "Tn. to " . $x . "?";?></p>
          <p class="lead">Service charge: <?php echo ceil($no * 0.15); ?>Tn.</p>
          <form method="post">
            <input type="hidden" name="un" value="<?php echo $x; ?>"><input type="hidden" name="amnt" value="<?php echo $no; ?>"><input class="btn btn-info" type="submit" name="confirm" value="Charge"><a href="bank" class="btn btn-success">Cancel</a>
          </form>
        <?php
      }
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
