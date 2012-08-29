<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * A composition is a group of selections associated to a statement.
 * This class is used to display multiple statements into the same graphic,
 *          even though doing it server-side and storing this into the database
 *          is stupid as fuck.
 * @see Selection
 */
class Composition {

    private $_compositionBean; /**< storing the current composition */
    private $_statementBean; /**< storing the targeted statement */

    /**
     * Add to a statement a composition.
     * @param $sName Name of the statement.
     * @param $cName Name of the composition.
     */
    public function __construct($sName, $cName) {
        $beans = R::find('releve', "name = ?", [$sName]);

        foreach ($beans as $bean) {
            $this->_statementBean = $bean;
            break;
        }

        if ($this->_statementBean === NULL) {
            $beans = R::find('multi_releve', "name = ?", [$sName]);

            foreach ($beans as $bean) {
                $this->_statementBean = $bean;
                break;
            }
        }

        $this->_compositionBean = R::dispense('composition');
        $this->_compositionBean->name = $cName;
        $this->_compositionBean->releve_id = $this->_statementBean->getID();
        $this->_compositionBean->releve_type = $this->_statementBean->getMeta('type');
    }

    /**
     * Add a selection to a composition using a selection bean
     * @param $selectionBean The selection to associate to the current composition.
     */
    public function addSelection($selectionBean) {
        $selectionBean->composition = $this->_compositionBean;
        R::store($selectionBean);
    }

    /**
     * Add a selection (obtained from data) to a composition.
     * @param $graphName Name of the graphic.
     * @param $begin The timestamp of the first data of the selection
     * @param $end The timestamp of the last data of the selection
     */
    public function addNewSelection($graphName, $begin, $end) {
        $selection = new Selection($this->_statementBean->name, $graphName, $begin, $end);
        $selection->save();
        $selectionBean = $selection->getBean();
        $this->addSelection($selectionBean);
    }

    /**
     * Store selection into the database.
     * FIXME seems like a copypasta from Selection
     * @see Selection
     */
    public function save() {
        R::store($this->_statementBean);
        R::store($this->_selectionBean);
    }

    /**
     * Get all compositions associated to a statement.
     * @param $sName The name of the statement.
     * @return $compositions an array of Composition.
     * FIXME This method could access to another user's statements.
     */
    public static function getCompositions($sName) {
        $statements = R::find('releve', "name = ?", [$sName]);
        if ($statements == NULL) {
            $statements = R::find('multi_releve', "name = ?", [$sName]);
        }

        foreach ($statements as $statement) {
            $values['id'] = $statement->getID();
            $values['type'] = $statement->getMeta('type');
            $compositions = R::find('composition', 'releve_id = :id AND releve_type = :type', $values);
            return $compositions;
        }
    }

    /**
     * Get all selections of this composition.
     * @param $cName The name of the composition. 
     * @return $selections An array of Selection.
     */
    public static function getSelections($cName) {
        $composition = R::findOne('composition', "name = ?", [$cName]);
        $selections = $composition->ownSelection;
        return $selections;
    }
    
    /**
     * Remove the actual composition to the database.
     */
    public function delete() {
        R::trash($this->_compositionBean);
    }

}
?>
