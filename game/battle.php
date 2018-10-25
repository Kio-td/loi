<?php
require('../base/head.php');
 ?>
</nav>
</div>
</header>
  <link href="/battle.css" rel="stylesheet">

  <main role="main" class="inner cover">
  <div class="battlebox">

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
    <span class="fle btn btn-raised btn-warning">
      Flee
    </span>
  </div>
  <span class="trip btn btn-raised btn-light">
    Trip
  </span>
  </main>

  <script>

    $('.trip').hide();
    function toggletrip() {
      $('.trip').toggle();
      $('.rab').toggle();
    }
  </script>
<?php
require('../base/feet.php');
 ?>
