<?php
/* This file is released under the CeCILL-B V1 licence.*/

// Autoloading of class (PHP5)
function __autoload($class)
{
	$possible_paths = [
		"Tools/$class.php",
		"Mod/$class.php",
		"View/$class.php",
		"Ctrl/$class.php",
		"Data/$class.php",
		"Mod/FileTypes/$class.php",
		"Mod/Storages/$class.php"
	];

	foreach($possible_paths as $chemin)
	{
		if (file_exists($chemin))
		{
			require_once($chemin);
			return;
		}
	}

	$data_path = 'Data/'.substr($class, 1)."/$class.php";
	if (file_exists($data_path))
		require_once($data_path);
}// __autoload()
?>
