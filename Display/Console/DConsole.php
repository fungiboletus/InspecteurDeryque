<?php
/* This file is released under the CeCILL-B V1 licence.*/

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
	}
}
?>
