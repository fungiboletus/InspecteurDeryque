<?php
/**
 * Displays a classic table.
 */
class DConsole extends DAbstract
{
	const name = 'Console';

	public function show()
	{
		CHead::addJs($GLOBALS['ROOT_PATH'].'/Display/Console/Rainbow.min.js');
		DAbstract::show();

		?>
<script type="text/javascript">
$(document).ready(function(){
	new DConsole();
});
</script>
<ul id="console">
</ul>
		<?php
	}
}
?>
