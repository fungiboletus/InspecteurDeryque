<?php
/**
 * Manages statements.
 */
class DataMulti {
    public function index() {
        CNavigation::setTitle('Gestion des relevés multiples');

        $statements = DataMod::getStatementsMulti($_SESSION['bd_id']);

        DataMultiView::showStatementsList($statements);

        DataMultiView::showAddButton();
    }

    public function choose() {
        CNavigation::setTitle('Nouveau relevé');
        CNavigation::setDescription('Sélectionnez les relevés que vous souhaitez composer');

        DataMultiView::showAddForm(array(
                                       'nom' => '',
                                       'desc' => ''));
    }

    public function add() {

        if (CNavigation::isValidSubmit(array('nom','desc'), $_REQUEST)) {
            if (R::findOne('multi_releve', 'name = ? and user_id = ?', array($_REQUEST['nom'], $_SESSION['bd_id']))) {
                new CMessage('Un relevé existe déjà avec le même nom', 'error');
		CNavigation::redirectToApp('DataMulti', 'choose');
            } 
	    else if(!(isset($POST['releve'])) and count($_POST['releve']) < 1){
		new CMessage('Vous devez selectionner au moins un relevé', 'error');
		CNavigation::redirectToApp('DataMulti', 'choose');

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

                CNavigation::redirectToApp('DataMulti');

                return;
            }

        }

        //DataMultiView::showStatementsList();
    }

	public function change() {

	    if (CNavigation::isValidSubmit(array('nom','desc'), $_REQUEST)) {
            
	    if(!isset($_POST['releve']) || count($_POST['releve']) < 1){
		new CMessage('Vous devez selectionner au moins un relevés', 'error');
		CNavigation::redirectToApp('DataMulti', 'choosechange', array('nom' => $_REQUEST['nom']));

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

		CNavigation::redirectToApp('DataMulti');

		return;
	    }
	  }

	    DataMultiView::showStatementsList();
	}


    public function view() {
        $statements = isset($_REQUEST['nom']) ? DataMod::getMultiStatement($_REQUEST['nom'], $_SESSION['bd_id']) : false;
  	if (!$statements) {
            CTools::hackError();
        }
	CNavigation::setTitle('Relevé «'.$_REQUEST['nom'].'»');
        //CNavigation::setDescription($statements['description']);

	DataMultiView::showStatement($_REQUEST['nom']);
        $data = DisplayMod::getDisplayTypes();
	foreach($statements as $statement){
	$stat = DataMod::getStatement($statement['name'], $_SESSION['bd_id']);
      
        $n_datamod = DataMod::loadDataType($stat['modname']);

        $sql = '';
        foreach ($n_datamod->getVariables() as $k => $v) {
            $sql .= "min($k), max($k), avg($k), ";
        }
        $stats = R::getRow('select '.$sql.'count(*) from d_'.$n_datamod->folder.' where user_id = ? and releve_id = ?', array($_SESSION['bd_id'], $stat['id']));
	}
        DataMultiView::showDisplayViewChoiceTitle();
        DisplayView::showGraphicChoiceMenu($data, true, $n_datamod->display_prefs);

        DataMultiView::showViewButtons(
            CNavigation::generateMergedUrl('DataMulti', 'remove'),
            CNavigation::generateUrlToApp('DataMulti'),
            CNavigation::generateMergedUrl('DataMulti', 'choosechange'));
    }

    public function viewInfo() {
        $statements = isset($_REQUEST['nom']) ? DataMod::getMultiStatement($_REQUEST['nom'], $_SESSION['bd_id']) : false;
  	if (!$statements) {
            CTools::hackError();
        }
	CNavigation::setTitle('Relevé «'.$_REQUEST['nom'].'»');
        //CNavigation::setDescription($statements['description']);

	DataMultiView::showStatement($_REQUEST['nom']);
	foreach($statements as $statement){
	$stat = DataMod::getStatement($statement['name'], $_SESSION['bd_id']);
      
        $n_datamod = DataMod::loadDataType($stat['modname']);

        $sql = '';
        foreach ($n_datamod->getVariables() as $k => $v) {
            $sql .= "min($k), max($k), avg($k), ";
        }
        $stats = R::getRow('select '.$sql.'count(*) from d_'.$n_datamod->folder.' where user_id = ? and releve_id = ?', array($_SESSION['bd_id'], $stat['id']));
        DataMultiView::showInformations($stats, $n_datamod, $stat['name']);
	}


        DataMultiView::showViewButtons(
            CNavigation::generateMergedUrl('DataMulti', 'remove'),
            CNavigation::generateUrlToApp('DataMulti'),
            CNavigation::generateMergedUrl('DataMulti', 'choosechange'));
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
            CNavigation::redirectToApp('DataMulti');
        } else {
            CNavigation::setTitle('Suppression du relevé «'.$statement['name'].'»');
            CNavigation::setDescription('Consequences will never be the same!');

            DataMultiView::showRemoveForm(
                $statement['description'],
                CNavigation::generateMergedUrl('DataMulti', 'remove', array('confirm' => 'yes')),
                CNavigation::generateMergedUrl('DataMulti', 'view'));
        }
    }

    public function choosechange() {
        CNavigation::setTitle('Modifier le relevé');
        CNavigation::setDescription('Sélectionnez les relevés que vous souhaitez ajouter');
	$desc = DataMod::getDescMulti($_REQUEST['nom'], $_SESSION['bd_id']);
	DataMultiView::showChangeForm(array(
                                       'nom' => $_REQUEST['nom'],
                                       'desc' => $desc["description"]));
        
    }


}
?>
