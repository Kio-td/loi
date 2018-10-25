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
    <h1 class="cover-heading">Welcome home, <?php echo ucfirst($username); ?>.</h1>
    <p class="lead">Please choose an option.</p>
    <p class="lead"><a href="bank">Bank</a>&emsp;<a href="battle" id="tala">Battle</a>&emsp;Ipsum</p>
    <script>


new Tooltip(document.getElementById('tala'), {
    placement: 'top', // or bottom, left, right, and variations
    title: "Take a look at this before it's finished!"
});


    </script>
  </main>

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
