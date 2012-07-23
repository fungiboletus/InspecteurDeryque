<?php
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
