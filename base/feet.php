
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
$(document).ready(function() { $('body').bootstrapMaterialDesign(); });
var t = false
jdetects.create({
once: true,
onchange: function(status) {
//console.clear();
console.error("!!!GAME MAYBE TAMPERED WITH NOW!!!\nI have closed all connections. Please close DevConsole and restart the game.");
}
});
function err (info) { toastr.error(info); } function suc (info) { toastr.success(info) } function inf (info) { toastr.info(info) } toastr.options = {
  "newestOnTop": true,
  "progressBar": true,
  "positionClass": "toast-top-right",
  "preventDuplicates": true
}
</script>

</body></html>
