<?php

/**
 * Associates some compositions to a statement.
 */
class Composition {

    private $_compositionBean;
    private $_statementBean;

    /**
     * Add a composition to a statement.
     */
    public function __construct($rname, $name) {
        $beans = R::find('releve', "name = ?", array($rname));

        foreach ($beans as $bean) {
            $this->_statementBean = $bean;
            break;
        }

        if ($this->_statementBean === NULL) {
            $beans = R::find('multi_releve', "name = ?", array($rname));

            foreach ($beans as $bean) {
                $this->_statementBean = $bean;
                break;
            }
        }

        $this->_compositionBean = R::dispense('composition');

        $this->_compositionBean->name = $name;

        $this->_compositionBean->releve_id = $this->_statementBean->getID();
        $this->_compositionBean->releve_type = $this->_statementBean->getMeta('type');

    }

    /**
     * Add a selection (bean) to a composition.
     */
    public function addSelection($selectionBean) {
        $selectionBean->composition = $this->_compositionBean;

        R::store($selectionBean);
    }

    /**
     * Add a selection (obtained from data) to a composition.
     */
    public function addNewSelection($graphName, $debut, $fin) {
        $selection = new Selection($this->_statementBean->name, $graphName, $debut, $fin);

        $selection->save();

        $selectionBean = $selection->getBean();

        $this->addSelection($selectionBean);

    }

    /**
     * Store selection into the database.
     */
    public function save() {

        R::store($this->_statementBean);

        R::store($this->_selectionBean);

    }

    /**
     * Get all compositions associated to a statement.
     */
    public static function getCompositions($rname) {
        $statements = R::find('releve', "name = ?", array($rname));
        if ($statements == NULL) {
            $statements = R::find('multi_releve', "name = ?", array($rname));
        }

        foreach ($statements as $statement) {

            $values['id'] = $statement->getID();
            $values['type'] = $statement->getMeta('type');

            $compositions = R::find('composition', 'releve_id = :id AND releve_type = :type', $values);

            return $compositions;
        }

    }

    /**
     * Get all selections associated to a composition.
     */
    public static function getSelections($cname) {
        $composition = R::findOne('composition', "name = ?", array($cname));

        $selections = $composition->ownSelection;

        return $selections;

    }
    
    /**
     * Remove the actual composition.
     */
    public function delete() {
        R::trash($this->_compositionBean);
    }

}
?>
