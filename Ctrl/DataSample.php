<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Manages statements.
 */
class DataSample {
    public function index() {
        CNavigation::setTitle('Gestion des extraits de relevés');

        $statements = DataMod::getStatementComp($_SESSION['bd_id']);
        $statement = DataMod::getStatementCompMulti($_SESSION['bd_id']);
	echo <<<END
		    <b>Extraits de sources simples</b>
END;
	$int = 0;
        DataSampleView::showStatementsList($statements, $int);
	echo <<<END
		    <b>Extraits de sources multiples</b>
END;
	$int=1;
        DataSampleView::showStatementsLists($statement);

        DataSampleView::showAddButton();
    }

    public function choose() {
        CNavigation::setTitle('Nouvel extrait de relevé');
        CNavigation::setDescription('Sélectionnez le relevé que vous souhaitez utiliser');

        DataSampleView::showAddForm();
        DataSampleView::showBackButtons(
            CNavigation::generateUrlToApp('DataSample'));
    }

    public function add() {

        if (CNavigation::isValidSubmit(['nom','desc'], $_REQUEST)) {
            if (R::findOne('multi_releve', 'name = ? and user_id = ?', [$_REQUEST['nom'], $_SESSION['bd_id']])) {
                new CMessage('Un relevé existe déjà avec le même nom', 'error');
		CNavigation::redirectToApp('DataSample', 'choose');
            } 
	    else if(!(isset($POST['releve'])) or count($_POST['releve']) < 1){
		new CMessage('Vous devez selectionner au moins un relevé', 'error');
		CNavigation::redirectToApp('DataSample', 'choose');

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

                CNavigation::redirectToApp('DataSample');

                return;
            }

        }

        DataSampleView::showStatementsList();
    }

    public function addMulti() {

        if (CNavigation::isValidSubmit(['nom','desc'], $_REQUEST)) {
            if (R::findOne('multi_extrait', 'name = ? and user_id = ?', [$_REQUEST['nom'], $_SESSION['bd_id']])) {
                new CMessage('Un multi extrait existe déjà avec le même nom', 'error');
		CNavigation::redirectToApp('DataSample', 'choosemulti');
            } 
	    else if(!(isset($POST['releve'])) and count($_POST['releve']) < 1){
		new CMessage('Vous devez selectionner au moins un extrait', 'error');
		CNavigation::redirectToApp('DataSample', 'choosemulti');

		} else {

                $user = $_SESSION['user'];

                $statement = R::dispense('multi_extrait');
                $statement->user = $user;
                $statement->name = $_REQUEST['nom'];
                $statement->description = $_REQUEST['desc'];

                R::store($statement);
		
		$tab_releve = $_POST['releve'];
                foreach($tab_releve as $rel){
                	$stat = R::dispense('multi_releve_extrait');
                	$stat->multi_releve_id = $statement['id'];
                	$stat->composition_id = $rel;
			R::store($stat);
		}

                new CMessage('Relevé correctement ajouté');

                CNavigation::redirectToApp('DataSample');

                return;
            }

        }

        DataSampleView::showStatementsList();
    }

	public function view() {
	
	$statements = isset($_REQUEST['nom']) ? DataMod::getCompo($_REQUEST['nom']) : false;

  	if (!$statements) {
            CTools::hackError();
        }
	CNavigation::setTitle('Extrait «'.$_REQUEST['nom'].'»');
        DataSampleView::showViewButtons(
            CNavigation::generateUrlToApp('DataSample'),
	    CNavigation::generateUrlToApp('DataSample', 'remove', ['nom' => $_REQUEST['nom']]));
	
	}

	public function viewmu() {
	
	$statements = isset($_REQUEST['nom']) ? DataMod::getMultiCompo($_REQUEST['nom'], $_SESSION['bd_id']) : false;

  	if (!$statements) {
            CTools::hackError();
        }
	CNavigation::setTitle('Extrait «'.$_REQUEST['nom'].'»');
        DataSampleView::showViewButtons(
            CNavigation::generateUrlToApp('DataSample'),
	    CNavigation::generateUrlToApp('DataSample', 'removeMulti', ['nom' => $_REQUEST['nom']]));
	}

	public function choosemulti() {
        CNavigation::setTitle('Nouveau multi relevé extrait');
        CNavigation::setDescription('Sélectionnez les extraits que vous souhaitez composer');

        DataSampleView::showMultiForm([
                                       'nom' => '',
					'desc' => '']);
        DataSampleView::showBackButtons(
            CNavigation::generateUrlToApp('DataSample'));
	}


	public function change() {

	    if (CNavigation::isValidSubmit(['nom','desc'], $_REQUEST)) {
            
	    if(!isset($_POST['releve']) || count($_POST['releve']) < 1){
		new CMessage('Vous devez selectionner au moins un relevés', 'error');
		CNavigation::redirectToApp('DataSample', 'choosechange', ['nom' => $_REQUEST['nom']]);

	     } else {
		$state = DataMod::getStatementMulti($_REQUEST['nom'], $_SESSION['bd_id']);
		$state = R::load('multi_releve', $state['id']);
		$state->description = $_REQUEST['desc'];

		R::store($state);
		
		$multi = DataMod::getMultiRelRel($_SESSION['bd_id'], $state['id']);
		foreach($multi as $mult){
			$mul = R::load('multi_releve_releve', $mult['id']);
			R::exec('delete from multi_releve_releve where id = ?', [$mul['id']]);
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

		CNavigation::redirectToApp('DataSample');

		return;
	    }
	  }

	    DataSampleView::showStatementsList();
	}

    public function viewMulti() {
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
        $stats = R::getRow('select '.$sql.'count(*) from d_'.$n_datamod->folder.' where user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $stat['id']]);
        DataSampleView::showInformations($stats, $n_datamod, $stat['name']);
	}

        $data = DisplayMod::getDisplayTypes();
        DataSampleView::showDisplayViewChoiceTitle();
        DisplayView::showGraphicChoiceMenu($data, true, $n_datamod->display_prefs);
        DataSampleView::showBackButtons(
            CNavigation::generateUrlToApp('DataSample', 'choose'));
    }

    public function viewSimple() {
		$statement = isset($_REQUEST['nom']) ? DataMod::getStatement($_REQUEST['nom'], $_SESSION['bd_id']) : false;
		
		if (!$statement) {
			CTools::hackError();
		}

		CNavigation::setTitle('Relevé «'.$statement['name'].'»');
		CNavigation::setDescription($statement['description']);
		
		$n_datamod = DataMod::loadDataType($statement['modname']);
		$sql = '';
		foreach ($n_datamod->getVariables() as $k => $v) {
			$sql .= "min($k), max($k), avg($k), ";	
		}
		$stats = R::getRow('select '.$sql.'count(*) from d_'.$n_datamod->folder.' where user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $statement['id']]);
		DataView::showInformations($stats, $n_datamod);
	
		$data = DisplayMod::getDisplayTypes();
		DataSampleView::showDisplayViewChoiceTitle();
		DisplayView::showGraphicChoiceMenu($data, true, $n_datamod->display_prefs);

		DataSampleView::showBackButtons(
				CNavigation::generateUrlToApp('DataSample', 'choose'));
    }

    public function removeMulti() {
        $statement = DataMod::getCompoMulti($_REQUEST['nom'], $_SESSION['bd_id']);
        if (!$statement) {
            CTools::hackError();
        }
        if (isset($_REQUEST['confirm'])) {
            $stat = R::load('multi_extrait', $statement['id']);
            R::exec('delete from multi_extrait where id = ?', [$stat['id']]);
            R::trash(R::load('multi_extrait', $stat['id']));
            CNavigation::redirectToApp('DataSample');
        } else {
            CNavigation::setTitle('Suppression du relevé «'.$statement['name'].'»');
            CNavigation::setDescription('Consequences will never be the same!');

           DataSampleView::showRemoveForm(
                $statement['description'],
                CNavigation::generateMergedUrl('DataSample', 'removeMulti', ['confirm' => 'yes']),
                CNavigation::generateMergedUrl('DataSample', 'viewmu'));
        }

    }

    public function remove() {
		$statement = DataMod::getStatementCompo($_REQUEST['nom'], $_SESSION['bd_id']);
		if (!$statement) {
			CTools::hackError();
		}

		if (isset($_REQUEST['confirm'])) {
			$stat = R::load('composition', $statement['id']);
			R::exec('delete from composition where id = ?', [$stat['id']]);
			R::trash(R::load('composition', $statement['id']));
			CNavigation::redirectToApp('DataSample');
		}
		else
		{
			CNavigation::setTitle('Suppression du relevé «'.$_REQUEST['nom'].'»');
			CNavigation::setDescription('Consequences will never be the same!');

           DataSampleView::showRemoveForm(
                '',
                CNavigation::generateMergedUrl('DataSample', 'remove', ['confirm' => 'yes']),
                CNavigation::generateMergedUrl('DataSample', 'view'));
        }

    }

    public function choosechange() {
        CNavigation::setTitle('Modifier le relevé');
        CNavigation::setDescription('Sélectionnez les relevés que vous souhaitez ajouter');
	$desc = DataMod::getDescMulti($_REQUEST['nom'], $_SESSION['bd_id']);
	DataSampleView::showChangeForm([
                                       'nom' => $_REQUEST['nom'],
                                       'desc' => $desc["description"]]);
        
    }


}
?>
