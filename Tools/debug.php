<?php
// GROAW (raving rabbit cry) print the information with print_r in a special html part.

function groaw($info)
{
    if (DEBUG)
    {
		if (!isset($GLOBALS['groaw_array'])) {
			$GLOBALS['groaw_array'] = array();
		}

		$GLOBALS['groaw_array'][] = $info;
    }
}

function showGroaw() {
	global $groaw_array;
	
	if (isset($groaw_array)) {
		echo "\n<pre class=\"groaw\">";

		$c_groaw_array = count($groaw_array);

		for($i = 0; $i < $c_groaw_array; ++$i) {
			$groaw = $groaw_array[$i];
			if ($groaw) {
				echo htmlspecialchars(print_r($groaw, true));
			} else {
				var_dump($groaw);
			}
			echo (($i < $c_groaw_array - 1) ? "<hr/>" : '' );
		}
		echo "</pre>\n";
	}
}
?>
