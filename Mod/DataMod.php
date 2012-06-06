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
        $data = array();

        foreach (scandir('Data') as $folder) {
            $folder = self::secureDossier($folder);
            if (strlen($folder) && $folder[0] !== '.' && is_dir('Data/' . $folder) && file_exists("Data/$folder/D$folder.php")) {
                require_once ("Data/$folder/D$folder.php");
                $class = "D$folder";
                $name = $class::name;
                $data[] = new DataMod($name, $folder);
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
        $folder = self::secureDossier($folder);
        if (!file_exists("Data/$folder/D$folder.php"))
            return null;
        require_once ("Data/$folder/D$folder.php");
        $class = "D$folder";
        $name = $class::name;
        $mod = new DataMod($name, $folder);
        $mod->display_prefs = explode(' ', $class::display_prefs);
        return $mod;
    }

    /** Check availability of a given Data. */
    public static function modExist($folder) {
        $folder = self::secureDossier($folder);
        return file_exists("Data/$folder/D$folder.php");
    }

    /** Get a statement given the name and the user of that statement.
     * @param $name Name of the statement.
     * @param $user_id id of the user who created the asked statement.
     * @return A query request.
     */
    public static function getStatement($name, $user_id) {
        return R::getRow('select r.id, name, description, modname, PicMinLine, PicMaxLine from releve r, datamod d where r.user_id = ? and r.mod_id = d.id and r.name = ?', array($user_id, $name));
    }
    
    /**
     *
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

    public static function getStatements($user_id) {
        return R::getAll('select name, description, modname from releve r, datamod d where r.user_id = ? and r.mod_id = d.id order by name', array($user_id));
    }

    public static function getMultiReleves($user_id) {
        return R::getAll('select name from multi_releve r where r.user_id = ? order by name', array($user_id));
    }

}
?>
