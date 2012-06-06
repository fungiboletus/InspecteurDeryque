<?php

/**
 * Permet de combiner plusieurs relevés en un seul.
 */
class CompositionReleve {

    private $_statementBean;

    /**
     * Trouve ou ajoute un nouveau relevé multiple.
     */
    public function __construct($name, $user) {
        $beans = R::findOrDispense('multi_releve', "name = ?", array($name));

        foreach ($beans as $bean) {
            $this->_statementBean = $bean;
            break;
        }
        
        $this->_statementBean->name = $name;
        $this->_statementBean->user = $user;
        
        R::store($this->_statementBean);

    }

    /**
     * Ajoute un relevé à la liste.
     */
    public function addReleve($rname) {
        

        $statements = R::find('releve', 'name = ?', array($rname));

        foreach ($statements as $statement) {
            
            R::associate($this->_statementBean, $statement);
            
        }

    }

    
    /**
     * Retourne le relevé qui porte ce nom (NULL si il n'existe pas).
     */
    public static function getCReleve($name) {
        $resList = R::find('multi_releve', "name = ?", array($name));
        if(count($resList) === 0) {
            return NULL;
        } else {
            foreach ($resList as $res) {
                return $res;
            }
            
        }
    }

}
?>
