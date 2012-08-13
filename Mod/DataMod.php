<?php

/** Class managing Data types and statements.
 * @see AbstractMod
 */
class DataMod extends AbstractMod {
	public $display_prefs = null;/**< FIXME */

	/**
	 * Look at and get all the data types.
	 * @returns array containing all data types
	 */
	public static function getDataTypes() {
		// Add internal and abstract data types
		$data = [
			new DataMod('EmptyData', null),
			new DataMod('DefaultData', null),
			new DataMod('DPositiveNumerical', null)
		];

		foreach (scandir('Data') as $folder) {
			$folder = self::secureFolder($folder);
			if (strlen($folder) && $folder[0] !== '.' && is_dir('Data/' . $folder) && file_exists("Data/$folder/D$folder.php")) {
				require_once ("Data/$folder/D$folder.php");
				$class = "D$folder";
				$data[] = new DataMod($class, $folder);
			}
		}

		return $data;
	}

	/**
	 * Load a data type class using its folder name (= type name).
	 * @param $folder String containing the name of the data
	 * @return $datamod A DataMod object with the name and folder of a data type.
	 */
	public static function loadDataType($folder) {
		$folder = self::secureFolder($folder);
		if (!file_exists("Data/$folder/D$folder.php"))
			return null;
		require_once ("Data/$folder/D$folder.php");
		$class = "D$folder";
		$mod = new DataMod($class, $folder);
		$mod->display_prefs = explode(' ', $class::display_prefs);
		return $mod;
	}

	/** Check availability of a given Data. */
	public static function modExist($folder) {
		$folder = self::secureFolder($folder);
		return file_exists("Data/$folder/D$folder.php");
	}

	/** Get a statement given the name and the user of that statement.
	 * @param $name Name of the statement.
	 * @param $_SESSION['bd_id'] id of the user who created the asked statement.
	 * @return A query request.
	 */
	public static function getStatement($name) {
		return R::getRow(
<<<SQL
	select r.id, name, description, modname, storage, additional_data
	from releve r, datamod d
	where r.user_id = ? and r.mod_id = d.id and r.name = ?
SQL
		, [$_SESSION['bd_id'], $name]);
		/*return R::getRow('select r.id, concat_ws("/", r.name, m.name) as name, description, modname, PicMinLine, PicMaxLine from multi_releve m, releve r, multi_releve_releve mr, datamod d where r.user_id = ? and r.mod_id = d.id and m.id = mr.multi_releve_id and mr.releve_id=r.id and r.name = ?', array($_SESSION['bd_id'], $name));*/
	}

	/**
	 * Save a statement.
	 * @param $user the user of the statement
	 * @param $statement The statement's' data
	 * @param $data The data type
	 * @return The result of the data saving query.
	 */
	public function save($user, $statement, $data) {
		$vars = $this->getVariables();

		$tuple = R::dispense('d_' . $this->folder);
		$tuple->user = $user;
		$tuple->releve = $statement;

		foreach ($vars as $key => $var) {
			$tuple->$key = $data->$key;
		}

		return R::store($tuple);
	}

	/**
	 * Get all statements created by a given user.
	 * @return array of statements.
	 */
	public static function getStatements()
	{
		return R::getAll(
<<<SQL
	select name, description, modname
	from releve r, datamod d
	where r.user_id = ? and r.mod_id = d.id order by name
SQL
		, [$_SESSION['bd_id']]);
	}

	/**
	 * Get statement with its id created by a given user.
	 * @return array of statements.
	 */
	public static function getStatementsWithId()
	{
		return R::getAll('select name, r.id as id, description, modname from releve r, datamod d where r.user_id = ? and r.mod_id = d.id order by name ', [$_SESSION['bd_id']]);
	}

	/**
	 * Get all multi statements created by a given user with a concatanation of the types of the statements they contain.
	 * @return array of statements.
	 */
	public static function getStatementsMulti()
	{
		return R::getAll(
<<<SQL
	select m.name, m.description, GROUP_CONCAT(modname) as modname
	from multi_releve m, releve r, multi_releve_releve mr, datamod d
	where m.user_id = ? and m.id = mr.multi_releve_id
	and mr.releve_id=r.id and r.mod_id = d.id group by m.name
SQL
		, [$_SESSION['bd_id']]);
	}

	/**
	 * Get all statements created by a given user.
	 * @return array of statements.
	 */
	public static function getStatementComp() {
		return R::getAll('select c.name, description, modname from composition c, datamod d, releve r where r.user_id = ? and r.id = c.releve_id and r.mod_id = d.id order by c.name ', [$_SESSION['bd_id']]);
	}
	public static function getStatementCompo($name) {
		return R::getAll('select c.name, description, modname from composition c, datamod d, releve r where r.user_id = ? and r.id = c.releve_id and r.mod_id = d.id and c.name=? ', [$_SESSION['bd_id'], $name]);
	}

	public static function getStatementCompWithId() {
		return R::getAll('select c.name, c.id as id, modname from composition c, datamod d, releve r where r.user_id = ? and r.id = c.releve_id and r.mod_id = d.id order by c.name ', [$_SESSION['bd_id']]);
	}

	public static function getStatementCompMulti() {
		return R::getAll('select m.name, m.description, GROUP_CONCAT(modname) as modname from multi_extrait m, composition r, multi_releve_extrait mr, datamod d, releve v where m.user_id = ? and v.id = r.releve_id and m.id = mr.multi_releve_id and mr.composition_id=r.id and v.mod_id = d.id group by m.name', [$_SESSION['bd_id']]);
	}
	public static function getMultiCompo($name) {
		return R::getAll('select r.name, r.id from multi_extrait m, composition r, multi_releve_extrait mr where m.user_id=? and m.id=mr.multi_releve_id and  mr.composition_id=r.id and m.name=?', [$_SESSION['bd_id'], $name]);
	}
	public static function getCompo($name) {
		return R::getAll('select name, id from composition where name=?', [$name]);
	}
	public static function getCompoMulti($name) {
		return R::getRow('select m.id, m.name, m.description ,modname from multi_extrait m, composition r, releve v, multi_releve_extrait mr, datamod d where m.user_id = ? and v.id = r.releve_id and m.id = mr.multi_releve_id and mr.composition_id=r.id and v.mod_id = d.id and m.name=?', [$_SESSION['bd_id'], $name]);
	}

	/** Get a statement given the name and the user of that statement.
	  * @param $name Name of the statement.
	  * @return A query request.
	  */
	public static function getStatementMulti($name) {
		return R::getRow('select m.id, m.name, m.description, modname  from multi_releve m, releve r, multi_releve_releve mr, datamod d where m.user_id = ? and m.id = mr.multi_releve_id and mr.releve_id=r.id and r.mod_id = d.id and m.name=?', [$_SESSION['bd_id'], $name]);
	}

	/**
	 * Get all statement names created by a given user.
	 * @param $_SESSION['bd_id'] The id of the user
	 * @return array of statements names.
	 */
	public static function getStatementsNames() {
		return R::getAll('select name from multi_releve r where r.user_id = ? order by name', [$_SESSION['bd_id']]);
	}

	/** Get a multi statement given the name and the user of that statement.
	  * @param $name Name of the statement.
	  * @param $_SESSION['bd_id'] id of the user who created the asked statement.
	  * @return A query request.
	  */
	public static function getMultiStatement($name) {
		return R::findOne('multi_releve', 'name = ? and user_id = ?', [$name, $_SESSION['bd_id']]);
	}

	public static function getMultiStatements() {
		return R::find('multi_releve', 'user_id = ?', [$_SESSION['bd_id']]);
	}

	/** Get the name and id of a statement given the name and the user of that statement.
	  * @param $name Name of the statement.
	  * @param $_SESSION['bd_id'] id of the user who created the asked statement.
	  * @return A query request.
	  */
	public static function getName($name) {
		return R::getAll('select name, id from releve r where user_id=? and name=?', [$_SESSION['bd_id'], $name]);
	}
	/** Get the description of a multi statement given the name and the user of that statement.
	  * @param $name Name of the statement.
	  * @param $_SESSION['bd_id'] id of the user who created the asked statement.
	  * @return A query request.
	  */
public static function getDescMulti($name) {
		return R::getRow('select description from multi_releve m where user_id = ? and name=?', [$_SESSION['bd_id'], $name]);
	}

	/** Get the id of all the multi_releve_releve of a multi statement given the name and the user of that statement.
	  * @param $name Name of the statement.
	  * @param $_SESSION['bd_id'] id of the user who created the asked statement.
	  * @return array of statements id.
	  */
	public static function getMultiRelRel($id) {
		return R::getAll('select m.id from multi_releve_releve m, multi_releve r where user_id=? and multi_releve_id=?', [$_SESSION['bd_id'], $id]);
	}

}
?>
