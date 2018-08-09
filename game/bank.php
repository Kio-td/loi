<?php
require("../base/head.php");
 ?>
 <a class="nav-link active" href="index">Home</a>
 <a class="nav-link" href="login?lo">Logout</a>
 </nav>
 </div>
 </header>

 <main role="main" class="inner cover">
   <h1 class="cover-heading">Good day, <?php echo ucfirst($username); ?></h1>
   <p class="lead">Current account balance: <?php ?></p>
