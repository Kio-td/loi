<?php
require('../base/head.php');
if($auth) {
  ?>
  <a class="nav-link active" href="index">Home</a>
  <a class="nav-link" href="login?lo">Logout</a>
  </nav>
  </div>
  </header>

<?php
} else {

?>
<a class="nav-link" href="login">Login</a>
<a class="nav-link" href="login?reg">Register</a>
</nav>
</div>
</header>

<main role="main" class="inner cover">
<h1 class="cover-heading">Reset Password</h1>
<p class="lead">Please put your email below and we will send you a link to reset your account.</p>
<form method="post">
  <input class="form-control" name="eml" type="email" required><br>
  <input type="submit" class="btn btn-secondary" value="Reset">
</form>
<?php } require('../base/feet.php');?>
