<?php

class DataMod
{
	public $nom;
	public $dossier;

	public function DataMod($nom, $dossier) {
		$this->nom = $nom;
		$this->dossier = $dossier;
	}

	public static function secureDossier($dossier) {
		return str_replace('..', '', $dossier);
	}

	public static function getDataTypes() {
		$data = array();

		foreach (scandir('Data') as $dossier) {
			$dossier = self::secureDossier($dossier);
			if (strlen($dossier) && $dossier[0] !== '.' && is_dir('Data/'.$dossier) && file_exists("Data/$dossier/D$dossier.php")) {
				require_once("Data/$dossier/D$dossier.php");
				$classe = "D$dossier";
				$nom = $classe::nom;
				$data[] = new DataMod($nom, $dossier);
			}
		}

		return $data;
	}

	public static function loadDataType($dossier) {
		$dossier = self::secureDossier($dossier);
		if (!file_exists("Data/$dossier/D$dossier.php")) return null;
		require_once("Data/$dossier/D$dossier.php");
		$classe = "D$dossier";
		$nom = $classe::nom;
		return new DataMod($nom, $dossier);
	}

	public static function modExist($dossier) {
		$dossier = self::secureDossier($dossier);
		return is_dir("Data/$dossier/D$dossier.php");
	}

	public static function getReleve($nom, $user_id) {
		return R::getRow('select r.id, name, description, modname from releve r, datamod d where r.user_id = ? and r.mod_id = d.id and r.name = ?', array($user_id, $nom)); 
	}
}
?>
