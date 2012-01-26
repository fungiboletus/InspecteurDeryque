<?php

class DisplayMod extends AbstractMod
{
	public static function getDisplayTypes() {
		$data = array();

		foreach (scandir('Display') as $dossier) {
			$dossier = self::secureDossier($dossier);
			if (strlen($dossier) && $dossier[0] !== '.' && is_dir('Display/'.$dossier) && file_exists("Display/$dossier/D$dossier.php")) {
				require_once("Display/$dossier/D$dossier.php");
				$classe = "D$dossier";
				$nom = $classe::nom;
				$data[] = new DisplayMod($nom, $dossier);
			}
		}

		return $data;
	}
}

?>
