<?php
require('../base/head.php');
 ?>
</nav>
</div>
</header>
  <link href="/base/battle.css" rel="stylesheet">

  <main role="main" class="inner cover">
  <div class="battlebox">
  <div class="info">
    <span class="mc">
    </span>
    <span class="uc">
      <span id="playername"><?php echo $username; ?></span><br>
      <span id="health"></span>/<span id="ttlhealth"></span> HP<br>
      <span id="effects"></span>
    </span>
  </div>
  <div class="">
  </div>
  </div>

  <div class="inv">
  </div>
  <div class="rab">
    <span onclick="record('atk')" class="atk btn btn-raised btn-danger">
      Attack
    </span>
    <span onclick="record('def')" class="def btn btn-raised btn-info">
      Defend
    </span>
    <span onclick="record('inv')" class="inv btn btn-raised btn-success">
      Inventory
    </span>
    <span onclick="record('flee')" class="fle btn btn-raised btn-warning" style="background-color: #ffde00">
      Flee
    </span>
  </div>
  <span onclick="record('trip')" class="trip btn btn-raised btn-light">
    Trip
  </span>
  <span onclick="record('winfail')" class="proc btn btn-secondary">
    Proceed
  </span>
  </main>
  <script>

    $('.trip').hide();
    $('.proc').hide();
    
    function toggletrip() {
      $('.trip').toggle();
      $('.rab').toggle();
    }

    function record(item) {
      if (item == "trip") {
        //send trip paramaters here
        toggletrip();
      }
    }


  </script>
<?php
require('../base/feet.php');
 ?>
