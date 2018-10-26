<?php
require('../base/head.php');
 ?>
</nav>
</div>
</header>
  <link href="/battle.css" rel="stylesheet">

  <main role="main" class="inner cover">
  <div class="battlebox">
  <div class="info">
    <div class="uc">
    </div>
    <div class="mc">
      <span id="playername"></span><br>
      <span id="health"></span>HP
      <span id="effects"></span>
    </div>
  </div>
  <div class="">
  </div>
  </div>

  <div class="inv">
  </div>
  <div class="rab">
    <span class="atk btn btn-raised btn-danger">
      Attack
    </span>
    <span class="def btn btn-raised btn-info">
      Defend
    </span>
    <span class="inv btn btn-raised btn-success">
      Inventory
    </span>
    <span class="fle btn btn-raised btn-warning" style="background-color: #ffde00">
      Flee
    </span>
  </div>
  <span onclick="sendtrip()" class="trip btn btn-raised btn-light">
    Trip
  </span>
  <span onclick="record()" class="proc btn btn-secondary">
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
    function sendtrip() {
      //send trip paramaters here
      toggletrip();
    }

  </script>
<?php
require('../base/feet.php');
 ?>
