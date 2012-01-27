<?php

abstract class AbstractMod
{
	public $nom;
	public $dossier;

	private $cache_variables = null;

	public function AbstractMod($nom, $dossier) {
		$this->nom = $nom;
		$this->dossier = $dossier;
	}

	public static function secureDossier($dossier) {
		return preg_replace('/(\.\.)|\\\'/', '', $dossier);
	}

	public function instancier() {
		$classe = 'D'.$this->dossier;
		return new $classe();
	}

	public function getVariables() {
		if ($this->cache_variables !== null)
		{
			return $this->cache_variables;
		}

		$classe = 'D'.$this->dossier;

		$vars = get_class_vars('D'.$this->dossier);
		$n_vars = $vars;

		foreach ($vars as $var => $value) {
			$var_name = 'n_'.$var;
			$n_vars[$var] = constant($classe.'::'.$var_name);
		}

		$this->cache_variables = $n_vars;
		return $n_vars; 
	}
}
?>
