require('jquery');
require('json5');
require('popper.js');
require('tooltip.js');
require('bootstrap-material-design');
require('jdetects');

function err(info) {
  document.getElementById('ap').innerHTML += '<div class="alert alert-warning alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}
function inf(info) {
  document.getElementById('ap').innerHTML += '<div class="alert alert-info alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}
function suc(info) {
  document.getElementById('ap').innerHTML += '<div class="alert alert-success alert-dismissible fade show" role="alert">'+info+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
}

$(document).ready(function() { $('body').bootstrapMaterialDesign(); });
var t = false
jdetects.create({
  once: true,
	onchange: function(status) {
      console.clear();
      console.error("!!!GAME MAYBE TAMPERED WITH NOW!!!\nI have closed all connections. Please close DevConsole and restart the game.");
	}
});
