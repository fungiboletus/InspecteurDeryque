<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Displays a classic table.
 */
class DCanard extends DAbstract
{
	const name = 'Canard';

	public function show() 
	{
		if ($this->gererVide()) return;

		?>
<script type="text/javascript">
window.top.addEventListener('canard', function(e) {console.log("Mais c'est un beau canard: "+e.detail);});

var i = 0;
window.setInterval(function() {
	var e = new CustomEvent('canard2', {detail: {i: ++i}});
	window.top.dispatchEvent(e);
}, 1000);
</script>
		<?php
		// var canard = new CustomEvent('canard', {"detail": true,"lapin": {}});
		// window.top.dispatchEvent(canard)
		// window.top.addEventListener('canard', function(e) {console.log(e.detail);});
	}
}
?>
