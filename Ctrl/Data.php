<?php
class Data
{
	public function index() {
		CNavigation::setTitle('Gestion des données');
		CNavigation::setDescription('All your data are belong to us');

		$releves = DataMod::getReleves($_SESSION['bd_id']);

		DataView::showRelevesList($releves);

		DataView::showAddButton();
	}

	public function choose() {
		CNavigation::setTitle('Nouveau relevé');
		CNavigation::setDescription('Sélectionnez le type de relevé');

		$data = DataMod::getDataTypes();
		DataView::showDataTypeList($data);
	}

	public function add() {

		if (CNavigation::isValidSubmit(array('nom','desc', 'mode'), $_REQUEST))
		{
			$_REQUEST['type'] = $_REQUEST['mode'];
			if (R::findOne('releve', 'name = ? and user_id = ?', array($_REQUEST['nom'], $_SESSION['bd_id'])))
			{
				new CMessage('Un relevé existe déjà avec le même nom', 'error');
			}
			else
			{
				$mode = R::findOne('datamod', 'modname = ?', array($_REQUEST['mode']));

				if (!$mode) {
					if (DataMod::modExist($_REQUEST['mode'])) {
						$mode = R::dispense('datamod');
						$mode->modname = $_REQUEST['mode'];
						R::store($mode);
					}
					else
					{
						CTools::hackError();
					}
				}

				$user = $_SESSION['user'];

				$releve = R::dispense('releve');
				$releve->mod = $mode;
				$releve->user = $user;
				$releve->name = $_REQUEST['nom'];
				$releve->description = $_REQUEST['desc'];

				R::store($releve);

				new CMessage('Relevé correctement ajouté');
				CNavigation::redirectToApp('Data');

				return;
			}

		}
		
		global $ROOT_PATH;
		if (!isset($_REQUEST['type']))
		{
			CTools::hackError();
		}

		$data_type = DataMod::loadDataType($_REQUEST['type']);
		
		CNavigation::setTitle('Nouveau relevé de type «'.$data_type->nom.'»');

		/*$hdir = htmlspecialchars($data_type->dossier);
		echo <<<END
			<img class="thumbnail" src="$ROOT_PATH/Data/$hdir/thumbnail.png" alt=""/>
END;*/

		DataView::showAddForm(array_merge(array(
						'nom' => '',
						'desc' => '',
						'mode' => $data_type->dossier),$_REQUEST));
	}

	public function view()
	{
		$releve = isset($_REQUEST['nom']) ? DataMod::getReleve($_REQUEST['nom'], $_SESSION['bd_id']) : false;
		
		if (!$releve) {
			CTools::hackError();
		}

		CNavigation::setTitle('Relevé «'.$releve['name'].'»');
		CNavigation::setDescription($releve['description']);
		
		$n_datamod = DataMod::loadDataType($releve['modname']);
		$sql = '';
		foreach ($n_datamod->getVariables() as $k => $v) {
			$sql .= "min($k), max($k), avg($k), ";	
		}
		$stats = R::getRow('select '.$sql.'count(*) from d_'.$n_datamod->dossier.' where user_id = ? and releve_id = ?', array($_SESSION['bd_id'], $releve['id']));
		DataView::showInformations($stats, $n_datamod);
	
		$data = DisplayMod::getDisplayTypes();
		DataView::showDisplayViewChoiceTitle();
		DisplayView::showGraphChoiceMenu($data, true, $n_datamod->display_prefs);

		DataView::showAPIInformations();

		DataView::showViewButtons(
				CNavigation::generateMergedUrl('Data', 'remove'),
				CNavigation::generateUrlToApp('Data'),
				CNavigation::generateMergedUrl('Data', 'random'));
	}

	public function remove()
	{
		$releve = DataMod::getReleve($_REQUEST['nom'], $_SESSION['bd_id']);
		if (!$releve) {
			CTools::hackError();
		}

		if (isset($_REQUEST['confirm'])) {
			$nom = $releve['name'];
			$releve = R::load('releve', $releve['id']);
			$modname = R::load('datamod', $releve->mod_id)->modname;
			R::exec('delete from d_'.$modname.' where releve_id = ?', array($releve['id']));
			R::trash(R::load('releve', $releve['id']));
			CNavigation::redirectToApp('Data');
		}
		else
		{
			CNavigation::setTitle('Suppression du relevé «'.$releve['name'].'»');
			CNavigation::setDescription('Consequences will never be the same!');

			DataView::showRemoveForm(
					$releve['description'],
					CNavigation::generateMergedUrl('Data', 'remove', array('confirm' => 'yes')),
					CNavigation::generateMergedUrl('Data', 'view'));
		}
	}

	public function random()
	{
		$releve = isset($_REQUEST['nom']) ? DataMod::getReleve($_REQUEST['nom'], $_SESSION['bd_id']) : false;
		$b_releve = R::load('releve', $releve['id']);

		if (!$releve) {
			CTools::hackError();
		}
		
		$n_datamod = DataMod::loadDataType($releve['modname']);
		$variables = $n_datamod->getVariables();
		
		R::begin();
		for ($i = 0; $i < 10; ++$i) {
			$datamod = $n_datamod->instancier();

			foreach ($variables as $k => $var) {
				if ($k === 'timestamp') $datamod->timestamp = microtime(true);
				else
					$datamod->$k = rand(0,6000)*0.02345;
			}

			$n_datamod->save($_SESSION['user'], $b_releve, $datamod);
		}
		R::commit();

		new CMessage('10 valeurs aléatoires ont étés générées');
		CNavigation::redirectToApp('Data', 'view', array('nom' => $_REQUEST['nom']));
	}

}
?>
