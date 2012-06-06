<?php
/** Class managing Display types */
class DisplayMod extends AbstractMod
{
	public static function getDisplayTypes() {
		$data = array();

		foreach (scandir('Display') as $folder) {
			$folder = self::secureDossier($folder);
			if (strlen($folder) && $folder[0] !== '.' && is_dir('Display/'.$folder) && file_exists("Display/$folder/D$folder.php")) {
				require_once("Display/$folder/D$folder.php");
				$class = "D$folder";
				$name = $class::name;
				$data[] = new DisplayMod($name, $folder);
			}
		}

		return $data;
	}
	
	public static function loadDisplayType($folder) {
		$folder = self::secureDossier($folder);
		if (!file_exists("Display/$folder/D$folder.php")) return null;
		require_once("Display/$folder/D$folder.php");
		$class = "D$folder";
		$name = $class::name;
		return new DisplayMod($name, $folder);
	}
}

?>
