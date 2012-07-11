<?php
/** This class manages the graphics display. */
class Display {

    public function index() {
        CNavigation::setTitle('Super page');
        CNavigation::setDescription('Tout reste à faire');
    }
    
    /** 
     * Loads required data and informations to display one or more statements.
     * @return array:
     * g : object inherited of DAbstract, kind of graphic to use.
     * d : object of type DisplayMod  - contains informations about the display
     * n_datamod : object of type DataMod - contains informations about the data
     * statement(s) : Statements (array containing data about statements to display)
     */
    private function getStatementInfos() {
        // For multiple statements display
        if (isset($_GET['multireleve']) && $_GET['multireleve'] === "true") {
            // object inherited of DAbstract (= a graphic) expected.
            $g = NULL;
            // object of type DisplayMode expected.
            $d = NULL;
            // object of type DataMod expected FIXME renaming required?
            $n_datamod = NULL;
            // array of statements
            $statements = StatementComposition::getStatement($_REQUEST['nom']);
            
            // The fuck?? Moar statements?
            // FIXME A database analysis is required.
            $associatedStatements = R::related($statements, "releve");

            foreach ($associatedStatements as $oneStatement) {
                $statement = DataMod::getStatement($oneStatement->name, $_SESSION['bd_id']);
                // get Data informations
                $n_datamod = DataMod::loadDataType($statement['modname']);
                // for first loop iteration, define expected graphic
                if ($g === NULL) {                   
                    $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : (empty($n_datamod->display_prefs) ? 'default' : $n_datamod->display_prefs[0]);
                    // Get display informations
                    $d = DisplayMod::loadDisplayType($type);
                    if (!$d) CTools::hackError();
                    // Get kind of graphic
                    $g = $d->initialize();
                }
                
                // Initializes timestamps
                foreach ($n_datamod->getVariables() as $key => $value) {
                    // Deal with statements not relying on a timestamp
                    if($key != 'timestamp') {
                        $g->structure[ sha1($statement['name']).$key] = $statement['name'] . ' [ '. $value . ' ]';
                    } else {
                        $g->structure['timestamp'] = $value;
                    }
                }
                
                // Initializes other data values
                foreach (R::getAll('select * from d_' . $n_datamod->folder . ' where user_id = ? and releve_id = ?', array($_SESSION['bd_id'], $statement['id'])) as $index => $data) {
                    foreach ($data as $key => $value) {
                        // Deal with statements not relying on a timestamp
                        if($key != 'timestamp') {
                            $g->data[$index][sha1($statement['name']).$key] = $value;
                        } else {
                            $g->data[$index][$key] = $value;
                        }
                    }
                }
            } // end of foreach($associatedStatements);
            return array($g, $d, $n_datamod, $statements);
            
        } else {
            // For one-statement-at-a-time display.
            // So there is only one statement to get
            $statement = isset($_REQUEST['nom']) ? DataMod::getStatement($_REQUEST['nom'], $_SESSION['bd_id']) : false;

            if (!$statement) CTools::hackError();
            
            // Get data informations
            $n_datamod = DataMod::loadDataType($statement['modname']);
            $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : (empty($n_datamod->display_prefs) ? 'default' : $n_datamod->display_prefs[0]);
            // Get display informations
            $d = DisplayMod::loadDisplayType($type);

            if (!$d) CTools::hackError();
            
            // Get kind of graphic
            $g = $d->initialize();
            $g->structure = $n_datamod->getVariables();
            $g->data = R::getAll('select * from d_' . $n_datamod->folder . ' where user_id = ? and releve_id = ?', array($_SESSION['bd_id'], $statement['id']));
            
            return array($g, $d, $n_datamod, $statement);

        }
    }

    /** Default view when no statement neither any kind of graphic is selected. */
    public function view() {
        $infos = $this->getStatementInfos();
        $infos[0]->show();
        CNavigation::setTitle($infos[0]::name . ' du relevé «' . $_REQUEST['nom'] . '»');
        CNavigation::setDescription($infos[3]['description']);
        DisplayView::showBackButtons(CNavigation::generateUrlToApp('Data', 'view', array('nom' => $_REQUEST['nom'])));
    }
    
    /** Update view when either a statement or a kind of graphic is selected. */
    public function iframe_view() {
        define('NO_HEADER_BAR', true);
        CHead::addCss('iframe_view');

        $infos = $this->getStatementInfos();

        if (!isset($_REQUEST['no_choice_menu']))
        {
            $data = DisplayMod::getDisplayTypes();
            DisplayView::showGraphicChoiceMenu($data, false, $infos[2]->display_prefs, $infos[1]->folder, 'iframe_view');
        }

        echo '<h2>',    htmlspecialchars($infos[0]::name), ' du relevé «',
        htmlspecialchars($_REQUEST['nom']), '» <small>',
        htmlspecialchars($infos[3]['description']), '</small></h2>';
        $infos[0]->show();
    }

}
?>
