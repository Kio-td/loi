<?php
require("../base/head.php");
 ?>
 <a class="nav-link" href="index">Home</a>
 <a class="nav-link" href="login?lo">Logout</a>
 </nav>
 </div>
 </header>

 <main role="main" class="inner cover">
   <h1 class="cover-heading">Good day, <?php echo ucfirst($username); ?>.</h1>
   <p class="lead">Welcome to the bank. Your current account balance: <?php echo $bal; ?>Tn.</p>
   <p class="lead">Options: <a href="?transfer">Transfer</a></p>

<?php
require("../base/feet.php");?>
