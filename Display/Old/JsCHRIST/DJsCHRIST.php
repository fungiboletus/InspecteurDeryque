<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Displays a wonderful graph.
 */
class DJsCHRIST extends DAbstract
{
	const name = 'JsCHRIST';

	public function show()
	{
		DAbstract::show();
?>
<script type="text/javascript">
$(document).ready(function(){
	graph = new DJsCHRIST(byId('graph'));
});
</script>
<div id="graph">
</ul>
<?php
	}
}
?>
