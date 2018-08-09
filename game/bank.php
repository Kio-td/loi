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
  ?>
 <main role="main" class="inner cover">
   <h1 class="cover-heading">Transaction info-</h1>
   <form method="post">
     <input type="text" class="form-control" name="un" placeholder="Username"><br>
     <input type="number" min="1" max="<?php echo $bal; ?>" placeholder="Amount"><br>
     <input type="submit" class="btn btn-secondary" value="Send">
   </form>
<?php
} else {


?>
 <main role="main" class="inner cover">
   <h1 class="cover-heading">Good day, <?php echo ucfirst($username); ?>.</h1>
   <p class="lead">Welcome to the bank. Your current account balance: <?php echo $bal; ?>Tn.</p>
   <p class="lead">Options: <a href="?transfer">Transfer</a></p>

<?php
}
require("../base/feet.php");?>
