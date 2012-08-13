<?php

/**
 * A selection is a subpart of a statement. The goal is to load less data
 * than a complete statement when the whole statement is not needed
 * (e.g. the user wants to see yesterday's data and not other days).
 * This class allows to associate one or more selections to a statement.
 * @see Composition
 */
class Selection {

    private $_selectionBean; /**< storing the current selection */
    private $_statementBean; /**< storing the targeted statement */

    /**
     * Adds to a statement a selection.
     * @param $sName Name of the statement.
     * @param $graphName Name of the graphic.
     * @param $begin The timestamp of the first data of the selection
     * @param $end The timestamp of the last data of the selection
     */
    public function __construct($sName, $graphName, $begin, $end) {
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

        $this->_selectionBean = R::dispense('selection');

        $this->_selectionBean->releve_id = $this->_statementBean->getID();
        $this->_selectionBean->releve_type = $this->_statementBean->getMeta('type');
        $this->_selectionBean->name = $graphName;
        $this->_selectionBean->begin = $begin;
        $this->_selectionBean->end = $end;
        $this->_selectionBean->composition_id = NULL;

    }

    /**
     * Store the current selection.
     */
    public function save() {
        R::store($this->_statementBean);
        R::store($this->_selectionBean);
    }

    /**
     * Get all selections associated to a statement
     * that are not associated to a composition.
     * @param $sName the name of the asked statement.
     * @return $selections an array of Selection.
     * FIXME This method could access to another user's statements.
     */
    public static function getSelections($sName) {
        $statements = R::find('releve', "name = ?", [$sName]);
        if ($statements == NULL) {
            $statements = R::find('multi_releve', "name = ?", [$sName]);
        }
        
        foreach ($statements as $statement) {
            $values['id'] = $statement->getID();
            $values['type'] = $statement->getMeta('type');
            $selections = R::find ( 'selection', 'releve_id = :id AND releve_type = :type AND composition_id IS NULL',$values );
            return $selections;
        }
    }

    /**
     * Get the selection bean.
     * @return $bean A selection bean.
     */
    public function getBean() {
        return $this->_selectionBean;
    }
    
    /**
     * Delete the current selection bean in the database.
     */
    public function delete() {
        R::trash($this->_selectionBean);
    }

}
?>
