
<?php if (!$x) {
?>
</main>
<footer class="mastfoot mt-auto">
  <div class="inner">
  </div>
</footer>
</div>
<div id="ap">
  &nbsp;
</div>
<?php
}
?>
<script>
function err(info) {
document.getElementById('ap').innerHTML += '<div class="alert alert-warning alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}
function inf(info) {
document.getElementById('ap').innerHTML += '<div class="alert alert-info alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}
function suc(info) {
document.getElementById('ap').innerHTML += '<div class="alert alert-success alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}

//$(document).ready(function() { $('body').bootstrapMaterialDesign(); });
//I'm removing this for right now as it just spurrs up meaningless errors for no reason.
var t = false
jdetects.create({
once: true,
onchange: function(status) {
//console.clear();
console.error("!!!GAME MAYBE TAMPERED WITH NOW!!!\nI have closed all connections. Please close DevConsole and restart the game.");
}
});
</script>

</body></html>
