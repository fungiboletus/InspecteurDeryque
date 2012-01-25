<?php

abstract class AbstractMod
{
	public $nom;
	public $dossier;

	public function AbstractMod($nom, $dossier) {
		$this->nom = $nom;
		$this->dossier = $dossier;
	}

	public static function secureDossier($dossier) {
		return str_replace('..', '', $dossier);
	}

}
?>
