<?php

/** Abstract class governing types embedded into a specific directory tree. */
abstract class AbstractMod
{
	public $name; /**< Object name, depending on the implementing ObjectMod class */
	public $folder; /**< folder of that Object. */

	private $cache_variables = null;

	public function AbstractMod($name, $folder) {
		$this->name = $name;
		$this->folder = $folder;
	}

	public static function secureDossier($folder) {
		return preg_replace('/(\.\.)|\\\'/', '', $folder);
	}

	public function initialize() {
		$class = 'D'.$this->folder;
		return new $class();
	}

	public function getVariables() {
		if ($this->cache_variables !== null)
		{
			return $this->cache_variables;
		}

		$class = 'D'.$this->folder;

		$vars = get_class_vars('D'.$this->folder);
		$n_vars = $vars;

		foreach ($vars as $var => $value) {
			$var_name = 'n_'.$var;
			$n_vars[$var] = constant($class.'::'.$var_name);
		}

		$this->cache_variables = $n_vars;
		return $n_vars; 
	}
}
?>
