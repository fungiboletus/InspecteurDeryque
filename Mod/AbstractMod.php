<?php

/** Abstract class governing types embedded into a specific directory tree. */
abstract class AbstractMod
{
	public $name; /**< Object, depending on the inherited ObjectMod class */
	public $folder; /**< folder of that Object. */

	private $cache_variables = null;/**< FIXME */

    /** The constructor.
     * @param $name The name of the Object
     * @param $folder The name of the folder containing all necessary
     *        informations about the Object
     */
	public function AbstractMod($name, $folder) {
		$this->name = $name;
		$this->folder = $folder;
	}
    /**
     * Clean the folder name.
     * @param $folder The folder path to clean.
     */
	public static function secureFolder($folder) {
		return preg_replace('/(\.\.)|\\\'/', '', $folder);
	}
    /**
     * Returns Object pointed by the current AbstractMod class.
     */
	public function initialize() {
		$class = 'D'.$this->folder;
		return new $class();
	}
    /**
     * FIXME je sais pas quoi dire.
     * TODO trouver un truc très intelligent à dire.
     */
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
