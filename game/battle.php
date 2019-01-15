<?php
$auth = false;
if (filter_has_var(INPUT_COOKIE,"token")) {
  require "/var/www/no-access/loi/config.php";
  $n = $conn->escape_string(base64_decode(filter_input(INPUT_COOKIE,"token")));
  $c = $conn->query("SELECT bal, token, username from users where token = '".$n."'");
  if (!$c->num_rows) {
    $na = array("index.php", "login.php", "index", "login", "login-");
} else {
  $auth = true;
}
}
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Team LOI">
  <link href="/assets/bootstrap-material-design.min.css" rel="stylesheet">
  <link href="/assets/battle.min.css" rel="stylesheet">
  <script src="/assets/jquery.slim.min.js"></script>
  <script src="/assets/jdetects.min.js"></script>
  <script src="/assets/JSON5.min.js"></script>
  <script src="/assets/pop.min.js"></script>
  <script src="/assets/bootstrap-material-design.min.js"></script>
  <script src="/assets/typeit.min.js"></script>
  <title>Legend of Ikaros</title>
</head>
<body>
  <main role="main" class="inner cover">
    <h3 class="brand">Legend of Ikaros</h3>
    <div class="battlebox">
      <div class="info">
        <div class="mc healthbox">
          <span id="mobname"></span><br>
          <span id="mobhealth"></span>/<span id="mobttlhealth"></span> HP<br>
          <span id="mobeffects"></span>
        </div>
        <div class="uc healthbox">
          <span id="playername"></span><br>
          <span id="playerhealth"></span>/<span id="playerttlhealth"></span> HP<br>
          <span id="playereffects"></span>
        </div>
      </div>
      <div class="brdr">
      </div>
      <div class="story">
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
        <span onclick="record('inv')" id="inv" class="inv btn btn-raised btn-success">
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
  enemy.name = "Xavier";
  enemy.health = "120";
  enemy.total = "120";
  enemy.effects = JSON.parse("[]");

var back = "451 Char MAX.";
  var story = new TypeIt('.story')


  $('#mobeffects').html("<b><u>No Effects</u></b>");
  story.type(back);
  $('.trip').hide();
  $('.proc').hide();

  function toggletrip() {
    $('.trip').toggle();
    $('.rab').toggle();
  }
  mobdata = null
  function record(item) {

    if (item == "trip") {
      //send trip paramaters here
      toggletrip();
    } else if (item == "inv") {
      //open the inventory.
    } else if (item == "atk") {

    } else if (item == "def") {

    } else if (item == "flee") {

    } else if (item == "winfail") {

    }
  }
  x = 0
  p = 0
  var r = new WebSocket(websocket+"/main");
  r.onmessage = function(s){console.log(s);
  if (!p) {
    if(!JSON.parse(s).code) r.send(JSON.stringify({atoken: "<?php echo htmlspecialchars(filter_input(INPUT_COOKIE,"token")); ?>" }));
    else {JSON.stringify({cmd: "ping"})}
      p = 1
}
  var s = new WebSocket(websocket+"/btl");
  s.onmessage = function (data) {
    console.log(data);
    var dt = JSON.parse(data);
    if(dt.msg != undefined && dt.msg == "YOU_ARE_STILL_IN_A_FIGHT") {
      s.send("{ok:true}");
    }
    story.type(dt.story);
    if (x == 0) {
      mobdata = dt.battleid;
      $('#mobname').text(dt.mobdata.name);
      $('#mobttlhealth').text(dt.mobdata.ttlhealth);
      $('#playername').text(dt.playerdata.name);

      x = 1
    } else {
      if (mobdata !== dt.battleid) {s.close(1008, "I'VE BEEN TAMPERED WITH");alert("The clientside, or serverside code has been tampered with. Please reload, or contact support.")}
        $('#mobhealth').text(enemy.health);
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
}
</script>
