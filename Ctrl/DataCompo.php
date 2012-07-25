<?php
/**
 * Manages statements.
 */
class DataCompo {
    public function index() {
        CNavigation::setTitle('Gestion des relevés multiples');

        $statements = DataMod::getStatementsMulti($_SESSION['bd_id']);

        DataCompoView::showStatementsList($statements);

        DataCompoView::showAddButton();
    }

    public function choose() {
        CNavigation::setTitle('Nouveau relevé');
        CNavigation::setDescription('Sélectionnez les relevés que vous souhaitez composer');

        DataCompoView::showAddForm(array(
                                       'nom' => '',
                                       'desc' => ''));
    }

    public function add() {

        if (CNavigation::isValidSubmit(array('nom','desc'), $_REQUEST)) {
            if (R::findOne('multi_releve', 'name = ? and user_id = ?', array($_REQUEST['nom'], $_SESSION['bd_id']))) {
                new CMessage('Un relevé existe déjà avec le même nom', 'error');
		CNavigation::redirectToApp('DataCompo', 'choose');
            } 
	    else if(count($_POST['releve']) < 2){
		new CMessage('Vous devez selectionner au moins 2 relevés', 'error');
		CNavigation::redirectToApp('DataCompo', 'choose');

		} else {

                $user = $_SESSION['user'];

                $statement = R::dispense('multi_releve');
                $statement->user = $user;
                $statement->name = $_REQUEST['nom'];
                $statement->description = $_REQUEST['desc'];

                R::store($statement);
		
		$tab_releve = $_POST['releve'];
                foreach($tab_releve as $rel){
                	$stat = R::dispense('multi_releve_releve');
                	$stat->multi_releve_id = $statement['id'];
                	$stat->releve_id = $rel;
			R::store($stat);
		}

                new CMessage('Relevé correctement ajouté');

                CNavigation::redirectToApp('DataCompo');

                return;
            }

        }

        DataCompoView::showStatementsList();
    }

	public function change() {

	    if (CNavigation::isValidSubmit(array('nom','desc'), $_REQUEST)) {
            
	    if(!isset($_POST['releve']) || count($_POST['releve']) < 2){
		new CMessage('Vous devez selectionner au moins 2 relevés', 'error');
		CNavigation::redirectToApp('DataCompo', 'choosechange', array('nom' => $_REQUEST['nom']));

	     } else {
		$state = DataMod::getStatementMulti($_REQUEST['nom'], $_SESSION['bd_id']);
		$state = R::load('multi_releve', $state['id']);
		$state->description = $_REQUEST['desc'];

		R::store($state);
		
		$multi = DataMod::getMultiRelRel($_SESSION['bd_id'], $state['id']);
		foreach($multi as $mult){
			$mul = R::load('multi_releve_releve', $mult['id']);
			R::exec('delete from multi_releve_releve where id = ?', array($mul['id']));
			R::trash(R::load('multi_releve_releve', $mul['id']));
		}
		$tab_releve = $_POST['releve'];
		foreach($tab_releve as $rel) {
		    $stat = R::dispense('multi_releve_releve');
		    $stat->multi_releve_id = $state['id'];
		    $stat->releve_id = $rel;
		    R::store($stat);
		}

		new CMessage('Relevé correctement modifié');

		CNavigation::redirectToApp('DataCompo');

		return;
	    }
	  }

	    DataCompoView::showStatementsList();
	}

    public function view() {
        $statements = isset($_REQUEST['nom']) ? DataMod::getMultiStatement($_REQUEST['nom'], $_SESSION['bd_id']) : false;
  	if (!$statements) {
            CTools::hackError();
        }
	CNavigation::setTitle('Relevé «'.$_REQUEST['nom'].'»');
        //CNavigation::setDescription($statements['description']);

	foreach($statements as $statement){
	$stat = DataMod::getStatement($statement['name'], $_SESSION['bd_id']);
      
        $n_datamod = DataMod::loadDataType($stat['modname']);

        $sql = '';
        foreach ($n_datamod->getVariables() as $k => $v) {
            $sql .= "min($k), max($k), avg($k), ";
        }
        $stats = R::getRow('select '.$sql.'count(*) from d_'.$n_datamod->folder.' where user_id = ? and releve_id = ?', array($_SESSION['bd_id'], $stat['id']));
        DataCompoView::showInformations($stats, $n_datamod, $stat['name']);
	}

        $data = DisplayMod::getDisplayTypes();
        DataCompoView::showDisplayViewChoiceTitle();
        DisplayView::showGraphicChoiceMenu($data, true, $n_datamod->display_prefs);

        DataCompoView::showAPIInformations();

        DataCompoView::showViewButtons(
            CNavigation::generateMergedUrl('DataCompo', 'remove'),
            CNavigation::generateUrlToApp('DataCompo'),
            CNavigation::generateMergedUrl('DataCompo', 'choosechange'));
    }

    public function remove() {
        $statement = DataMod::getStatementMulti($_REQUEST['nom'], $_SESSION['bd_id']);
        if (!$statement) {
            CTools::hackError();
        }

        if (isset($_REQUEST['confirm'])) {
            $statement = R::load('multi_releve', $statement['id']);
            R::exec('delete from multi_releve where id = ?', array($statement['id']));
            R::trash(R::load('multi_releve', $statement['id']));
            CNavigation::redirectToApp('DataCompo');
        } else {
            CNavigation::setTitle('Suppression du relevé «'.$statement['name'].'»');
            CNavigation::setDescription('Consequences will never be the same!');

            DataCompoView::showRemoveForm(
                $statement['description'],
                CNavigation::generateMergedUrl('DataCompo', 'remove', array('confirm' => 'yes')),
                CNavigation::generateMergedUrl('DataCompo', 'view'));
        }
    }

    public function choosechange() {
        CNavigation::setTitle('Modifier le relevé');
        CNavigation::setDescription('Sélectionnez les relevés que vous souhaitez ajouter');
	$desc = DataMod::getDescMulti($_REQUEST['nom'], $_SESSION['bd_id']);
	DataCompoView::showChangeForm(array(
                                       'nom' => $_REQUEST['nom'],
                                       'desc' => $desc["description"]));
        
    }


}
?>
