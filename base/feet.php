
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

<script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js" integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous"></script>
<script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous"></script>
<script src="https://unpkg.com/jdetects@0.1.0/jdetects.js">
<script>$(document).ready(function() { $('body').bootstrapMaterialDesign(); });
var t = false
jdetects.create({
	once: true,
	onchange: function(status) {
    console.clear();
    console.error("!!!GAME MAYBE TAMPERED WITH NOW!!!\nI have closed all connections. Please close DevConsole and restart the game.")
	}
});

</script>


</body></html>
