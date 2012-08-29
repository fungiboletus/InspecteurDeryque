<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Displays a map.
 */
class DInfos extends DAbstract
{
	const name = 'Infos';

	public function show()
	{
		DAbstract::show();
?>
<script type="text/javascript">
$(document).ready(function(){
	infos = new DInfos(byId('infosBox'));
});
</script>
<div id="infosBox">
</ul>
<?php
	}
}
?>
