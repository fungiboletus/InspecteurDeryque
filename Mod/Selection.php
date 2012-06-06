<?php

/**
 * Associe une ou plusieurs selections à un relevé.
 */
class Selection {

    private $_selectionBean;
    private $_statementBean;

    /**
     * Ajoute une selection au relevé.
     */
    public function __construct($rname, $graphName, $begin, $end) {
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

        $this->_selectionBean = R::dispense('selection');

        $this->_selectionBean->releve_id = $this->_statementBean->getID();
        $this->_selectionBean->releve_type = $this->_statementBean->getMeta('type');
        $this->_selectionBean->name = $graphName;
        $this->_selectionBean->begin = $begin;
        $this->_selectionBean->end = $end;
        $this->_selectionBean->composition_id = NULL;

    }

    /**
     * Envoie la selection dans la BD.
     */
    public function save() {

        R::store($this->_statementBean);

        R::store($this->_selectionBean);

    }

    /**
     * Récupère toutes les selections associées à un relevé (et non attaché à une composition).
     */
    public static function getSelections($rname) {
        $statements = R::find('releve', "name = ?", array($rname));
        if ($statements == NULL) {
            $statements = R::find('multi_releve', "name = ?", array($rname));
        }
        
        
        

        foreach ($statements as $statement) {
            
            $values['id'] = $statement->getID();
            $values['type'] = $statement->getMeta('type');
            
            $selections = R::find ( 'selection', 'releve_id = :id AND releve_type = :type AND composition_id IS NULL',$values );
            
            return $selections;
        }

    }

    /**
     * Récupérer le bean de la sélection.
     */
    public function getBean() {
        return $this->_selectionBean;
    }
    
    public function delete() {
        R::trash($this->_selectionBean);
    }

}
?>
