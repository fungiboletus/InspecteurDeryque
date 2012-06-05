<?php

/**
 * Associe une ou plusieurs compositions à un relevé.
 */
class Composition {

    private $_compositionBean;
    private $_releveBean;

    /**
     * Ajoute une composition au relevé.
     */
    public function __construct($rname, $name) {
        $beans = R::find('releve', "name = ?", array($rname));

        foreach ($beans as $bean) {
            $this -> _releveBean = $bean;
            break;
        }

        if ($this -> _releveBean === NULL) {
            $beans = R::find('multi_releve', "name = ?", array($rname));

            foreach ($beans as $bean) {
                $this -> _releveBean = $bean;
                break;
            }
        }

        $this -> _compositionBean = R::dispense('composition');

        $this -> _compositionBean -> name = $name;

        $this -> _compositionBean -> releve_id = $this -> _releveBean -> getID();
        $this -> _compositionBean -> releve_type = $this -> _releveBean -> getMeta('type');

    }

    /**
     * Ajoute une sélection (bean) à la composition.
     */
    public function addSelection($selectionBean) {
        $selectionBean -> composition = $this -> _compositionBean;

        R::store($selectionBean);
    }

    /**
     * Ajoute une sélection (crée à partir des données) à la composition.
     */
    public function addNewSelection($graphName, $debut, $fin) {
        $selection = new Selection($this -> _releveBean -> name, $graphName, $debut, $fin);

        $selection -> save();

        $selectionBean = $selection -> getBean();

        $this -> addSelection($selectionBean);

    }

    /**
     * Envoie la selection dans la BD.
     */
    public function save() {

        R::store($this -> _releveBean);

        R::store($this -> _selectionBean);

    }

    /**
     * Récupère toutes les compositions associées à un relevé.
     */
    public static function getCompositions($rname) {
        $releves = R::find('releve', "name = ?", array($rname));
        if ($releves == NULL) {
            $releves = R::find('multi_releve', "name = ?", array($rname));
        }

        foreach ($releves as $releve) {

            $values['id'] = $releve -> getID();
            $values['type'] = $releve -> getMeta('type');

            $compositions = R::find('composition', 'releve_id = :id AND releve_type = :type', $values);

            return $compositions;
        }

    }

    /**
     * Récupère toutes les selections associées à une composition.
     */
    public static function getSelections($cname) {
        $composition = R::findOne('composition', "name = ?", array($cname));

        $selections = $composition -> ownSelection;

        return $selections;

    }
    
    public function delete() {
        R::trash($this -> _compositionBean);
    }

}
?>