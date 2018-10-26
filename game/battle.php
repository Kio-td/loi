<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Team LOI">
  <link href="/base/battle.css" rel="stylesheet">
  <script src="/assets/jquery.slim.min.js"></script>
  <script src="/assets/jdetects.min.js"></script>
  <script src="/assets/json5.min.js"></script>
  <script src="/assets/popper.min.js"></script>
  <script src="/assets/tooltip.min.js"></script>
  <script src="/assets/bootstrap-material-design.min.js"></script>
  <link src="/assets/bootstrap-material-design.min.css">
  <script>json = JSON5;</script>
  <title>Legend of Ikaros</title>
</head>
<body>
  <main role="main" class="inner cover">
    <div class="battlebox">
      <div class="info">
        <div class="mc healthbox">
          <span id="mobname"></span><br>
          <span id="mobhealth"></span>/<span id="mobttlhealth"></span> HP<br>
          <span id="mobeffects"></span>
        </div>
        <div class="uc healthbox">
          <span id="playername"><?php echo $username; ?></span><br>
          <span id="playerhealth">15</span>/<span id="playerttlhealth">15</span> HP<br>
          <span id="playereffects"><b><u>No Effects</u></b></span>
        </div>
      </div>
      <div class="brdr">
      </div>
      <div class="story">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vitae nisl gravida, luctus orci eget, scelerisque ante. Duis non urna eget diam bibendum scelerisque. Phasellus pellentesque tellus at cursus molestie. Integer tempus orci ut nibh venenatis, vel dapibus velit accumsan. Nulla at libero rutrum, vehicula quam sit amet, sollicitudin nisi. Mauris pretium, orci eu volutpat viverra, dolor felis tincidunt velit, vitae laoreet dolor urna eget.
      </div>
    </div>

    <div class="inv">
    </div>
    <div class="buttons">
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
    </div>
  </main>
  <script>

  enemy = new Object();
  enemy.name = "Example Enemy";
  enemy.health = "120";
  enemy.total = "120";
  enemy.effects = json.parse("[]");


  $('#mobname').text(enemy.name);
  $('#mobhealth').text(enemy.health);
  $('#mobttlhealth').text(enemy.total);
  $('#mobeffects').html("<b><u>No Effects</u></b>");

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

  $(document).ready(function() { $('body').bootstrapMaterialDesign(); });
  var t = false
  jdetects.create({
    once: true,
    onchange: function(status) {
      //console.clear();
      console.error("!!!GAME MAYBE TAMPERED WITH NOW!!!\nI have closed all connections. Please close DevConsole and restart the game.");
    }
  });

</script>
