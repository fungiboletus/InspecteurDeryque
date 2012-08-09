<?php
/**
 * Manages statements.
 */
class Data
{
	public function index() {
		CNavigation::setTitle(_('Simple statements'));
		CNavigation::setDescription(_('All your data are belong to us'));

		$statements = DataMod::getStatements($_SESSION['bd_id']);

		DataView::showStatementsList($statements);

		DataView::showAddButton();
	}

	public function add() {

		if (CNavigation::isValidSubmit(array('name','desc', 'mode'), $_REQUEST))
		{
			$_REQUEST['type'] = $_REQUEST['mode'];
			if (R::findOne('releve', 'name = ? and user_id = ?', array($_REQUEST['name'], $_SESSION['bd_id'])))
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
						$mode->storage = InternalDataMod::storageConstant;
						R::store($mode);
					}
					else
					{
						CTools::hackError();
					}
				}

				$user = $_SESSION['user'];

				$statement = R::dispense('releve');
				$statement->mod = $mode;
				$statement->user = $user;
				$statement->name = $_REQUEST['name'];
				$statement->description = $_REQUEST['desc'];

				R::store($statement);

				new CMessage('Relevé correctement ajouté');
				CNavigation::redirectToApp('Data');

				return;
			}

		}

		// $data_type = DataMod::loadDataType($_REQUEST['type']);

		CNavigation::setTitle(_('New statement'));


		DataView::showAddForm(array_merge(array(
						'name' => '',
						'desc' => '',
						'mode' => ''/*$data_type->folder*/),$_REQUEST),
			DataMod::getDataTypes());
	}

	public function view()
	{
		$statement = isset($_REQUEST['name']) ? DataMod::getStatement($_REQUEST['name'], $_SESSION['bd_id']) : false;

		if (!$statement)
			CTools::hackError();

		CNavigation::setTitle('Statement : '.$statement['name']);
		CNavigation::setDescription($statement['description']);

		$n_datamod = DataMod::loadDataType($statement['modname']);
		$sql = '';
		foreach ($n_datamod->getVariables() as $k => $v) {
			$sql .= "min($k), max($k), avg($k), ";
		}
		$stats = R::getRow('select '.$sql.'count(*) from d_'.$n_datamod->folder.' where user_id = ? and releve_id = ?', array($_SESSION['bd_id'], $statement['id']));
		DataView::showInformations($stats, $n_datamod);

		/*ata = DisplayMod::getDisplayTypes();
		DataView::showDisplayViewChoiceTitle();
		DisplayView::showGraphicChoiceMenu($data, true, $n_datamod->display_prefs);

		DataView::showAPIInformations();*/

		DataView::showViewButtons();
	}

	public function remove()
	{
		$statement = DataMod::getStatement($_REQUEST['name'], $_SESSION['bd_id']);
		if (!$statement) {
			CTools::hackError();
		}

		if (isset($_REQUEST['confirm'])) {
			//TODO check uselessness of :
			//$name = $statement['name'];
			$statement = R::load('releve', $statement['id']);
			$modname = R::load('datamod', $statement->mod_id)->modname;
			R::exec('delete from d_'.$modname.' where releve_id = ?', array($statement['id']));
			R::trash(R::load('releve', $statement['id']));
			CNavigation::redirectToApp('Data');
		}
		else
		{
			CNavigation::setTitle('Deleting statement : '.$statement['name']);
			CNavigation::setDescription('Consequences will never be the same!');

			DataView::showRemoveForm(
					$statement['description'],
					CNavigation::generateMergedUrl('Data', 'remove', array('confirm' => 'yes')),
					CNavigation::generateMergedUrl('Data', 'view'));
		}
	}

	public function random()
	{
		$statement = isset($_REQUEST['name']) ? DataMod::getStatement($_REQUEST['name'], $_SESSION['bd_id']) : false;
		$b_statement = R::load('releve', $statement['id']);

		if (!$statement) {
			CTools::hackError();
		}

		$n_datamod = DataMod::loadDataType($statement['modname']);
		$variables = $n_datamod->getVariables();

		R::begin();
		for ($i = 0; $i < 10; ++$i) {
			$datamod = $n_datamod->initialize();

			foreach ($variables as $k => $var) {
				if ($k === 'timestamp') $datamod->timestamp = microtime(true);
				else
					$datamod->$k = rand(0,6000)*0.02345;
			}

			$n_datamod->save($_SESSION['user'], $b_statement, $datamod);
		}
		R::commit();

		new CMessage('10 valeurs aléatoires ont étés générées');
		CNavigation::redirectToApp('Data', 'view', array('name' => $_REQUEST['name']));
	}

	public function composition()
	{
		if (!isset($_REQUEST['cname']) || !isset($_REQUEST['creleves']))
			CTools::hackError();

		$name = $_REQUEST['cname'];
		$releves = $_REQUEST['creleves'];

		$compostion = new StatementComposition($name, $_SESSION['user']);

		foreach ($releves as $releve)
			$compostion->addStatement($releve);

		CNavigation::redirectToApp('Dashboard');
	}

}
?>
