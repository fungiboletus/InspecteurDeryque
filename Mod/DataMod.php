<?php

class DataMod
{
	public $nom;
	public $dossier;

	public function DataMod($nom, $dossier) {
		$this->nom = $nom;
		$this->dossier = $dossier;
	}

	public static function getDataTypes() {
		$data = array();

		foreach (scandir('Data') as $dossier) {
			if ($dossier[0] !== '.' && is_dir('Data/'.$dossier) && file_exists("Data/$dossier/D$dossier.php")) {
				require_once("Data/$dossier/D$dossier.php");
				$classe = "D$dossier";
				$nom = $classe::nom;
				$data[] = new DataMod($nom, $dossier);
			}
		}

		return $data;
	}

	public static function loadDataType($dossier) {
		if (!file_exists("Data/$dossier/D$dossier.php")) return null;
		require_once("Data/$dossier/D$dossier.php");
		$classe = "D$dossier";
		$nom = $classe::nom;
		return new DataMod($nom, $dossier);
	}
}
?>
