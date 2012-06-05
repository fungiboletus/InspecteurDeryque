<?php

class DataMod extends AbstractMod {
    public $display_prefs = null;

    public static function getDataTypes() {
        $data = array();

        foreach (scandir('Data') as $dossier) {
            $dossier = self::secureDossier($dossier);
            if (strlen($dossier) && $dossier[0] !== '.' && is_dir('Data/' . $dossier) && file_exists("Data/$dossier/D$dossier.php")) {
                require_once ("Data/$dossier/D$dossier.php");
                $classe = "D$dossier";
                $name = $classe::nom;
                $data[] = new DataMod($name, $dossier);
            }
        }

        return $data;
    }

    public static function loadDataType($dossier) {
        $dossier = self::secureDossier($dossier);
        if (!file_exists("Data/$dossier/D$dossier.php"))
            return null;
        require_once ("Data/$dossier/D$dossier.php");
        $classe = "D$dossier";
        $name = $classe::nom;
        $mod = new DataMod($name, $dossier);
        $mod -> display_prefs = explode(' ', $classe::display_prefs);
        return $mod;
    }

    public static function modExist($dossier) {
        $dossier = self::secureDossier($dossier);
        return file_exists("Data/$dossier/D$dossier.php");
    }

    public static function getStatement($name, $user_id) {
        return R::getRow('select r.id, name, description, modname, PicMinLine, PicMaxLine from releve r, datamod d where r.user_id = ? and r.mod_id = d.id and r.name = ?', array($user_id, $name));
    }

    public function save($user, $statement, $data) {
        $vars = $this -> getVariables();

        $tuple = R::dispense('d_' . $this -> dossier);
        $tuple -> user = $user;
        $tuple -> releve = $statement;

        foreach ($vars as $key => $var) {
            $tuple -> $key = $data -> $key;
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
