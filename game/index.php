<?php
require('../base/head.php');
if($auth) {
  ?>
  <a class="nav-link" href="index.php">home</a>
  </nav>
  </div>
  </header>

  <main role="main" class="inner cover">
    <h1 class="cover-heading">Welcome home, <?php echo $username; ?></h1>
    <p class="lead">Please choose an option.</p>
<?php
} else {

?>
<a class="nav-link" href="login.php">Login</a>
<a class="nav-link" href="login.php?reg">Register</a>
</nav>
</div>
</header>

<main role="main" class="inner cover">
<h1 class="cover-heading">You're not logged in.</h1>
<p class="lead">Use the links up top if you'd like to continue your quest.</p>

<?php } require('../base/feet.php');?>
