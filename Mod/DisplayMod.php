<?php
/** Class managing Display types */
class DisplayMod extends AbstractMod
{
    /**
     * Get a list of the different kind of display.
     * @return $data Array of String.
     */
	public static function getDisplayTypes() {
		$data = array();

		foreach (scandir('Display') as $folder) {
			$folder = self::secureFolder($folder);
			if (strlen($folder) && $folder[0] !== '.' && is_dir('Display/'.$folder) && file_exists("Display/$folder/D$folder.php")) {
				require_once("Display/$folder/D$folder.php");
				$class = "D$folder";
				$name = $class::name;
				$data[] = new DisplayMod($name, $folder);
			}
		}

		return $data;
	}
	
	/**
	 * Get a DisplayMod pointing on a given kind of display.
	 * @param $folder the name of the folder containing the files
	 *                relative to the given Display.
	 * @return $displaymod A DisplayMod object pointing on the asked Display.
	 */
	public static function loadDisplayType($folder) {
		$folder = self::secureFolder($folder);
		if (!file_exists("Display/$folder/D$folder.php")) return null;
		require_once("Display/$folder/D$folder.php");
		$class = "D$folder";
		$name = $class::name;
		return new DisplayMod($name, $folder);
	}
}

?>
