
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
<script src="https://unpkg.com/jdetects@0.1.0/jdetects.js"></script>
<script>
$(document).ready(function() { $('body').bootstrapMaterialDesign(); });
var t = false
jdetects.create({
  once: true,
	onchange: function(status) {
      console.error("!!!GAME MAYBE TAMPERED WITH NOW!!!\nI have closed all connections. Please close DevConsole and restart the game.");
	}
});

</script>


</body></html>
