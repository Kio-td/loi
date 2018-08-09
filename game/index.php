<?php
require('../base/head.php');
if($auth) {
  ?>
  <a class="nav-link active" href="index">Home</a>
  <a class="nav-link" href="login?lo">Logout</a>
  </nav>
  </div>
  </header>

  <main role="main" class="inner cover">
    <h1 class="cover-heading">Welcome home, <?php echo ucfirst($username); ?></h1>
    <p class="lead">Please choose an option.</p>
    <p class="lead"><a class="pline" href="bank">Bank</a>&emsp;Lorem&emsp;Ipsum</p>
<?php
} else {

?>
<a class="nav-link" href="login">Login</a>
<a class="nav-link" href="login?reg">Register</a>
</nav>
</div>
</header>

<main role="main" class="inner cover">
<h1 class="cover-heading">You're not logged in.</h1>
<p class="lead">Use the links up top if you'd like to continue your quest.</p>

<?php } require('../base/feet.php');?>
