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

	public function form() {

		if (CNavigation::isValidSubmit(array('name','desc', 'type', 'location'), $_REQUEST))
		{
			if (R::findOne('releve', 'name = ? and user_id = ?', array($_REQUEST['name'], $_SESSION['bd_id'])))
			{
				new CMessage(_('A statement already exist with the same name'), 'error');
			}
			else
			{
				$mode = R::findOne('datamod', 'modname = ?', array($_REQUEST['type']));

				if (!$mode) {
					if (DataMod::modExist($_REQUEST['type'])) {
						$mode = R::dispense('datamod');
						$mode->modname = $_REQUEST['type'];
						R::store($mode);
					}
					else
						CTools::hackError();
				}

				$user = $_SESSION['user'];

				$statement = R::dispense('releve');
				$statement->mod = $mode;
				$statement->user = $user;
				$statement->name = $_REQUEST['name'];
				$statement->description = $_REQUEST['desc'];

				$datamods = array(
					'youtube' => 'YoutubeDataMod',
					'sensapp' => 'SensAppDataMod');

				$datamod = in_array($_REQUEST['location'], array_keys($datamods)) ?
					$datamods[$_REQUEST['location']] : 'InternalDataMod';

				// PHP in her limits
				$statement->storage = constant($datamod.'::storageConstant');
				$statement->additional_data = call_user_func($datamod.'::generateAdditionalData');

				R::store($statement);

				new CMessage('Relevé correctement ajouté');
				CNavigation::redirectToApp('Data');
			}
		}
		else
			new CMessage(_('Please select a statement type'), 'error');

		CHead::addJS('Data_add');

		CNavigation::setTitle(_('New statement'));

		DataView::showAddForm(array_merge(array(
						'name' => '',
						'desc' => '',
						'type' => '',
						'location' => '',
						'sensapp' => array(),
						'youtube_location' => ''),$_REQUEST),
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

		$storages = array(
			InternalDataMod::storageConstant => 'internal',
			YoutubeDataMod::storageConstant => 'youtube',
			SensAppDataMod::storageConstant => 'sensapp');

		$storage = in_array($statement['storage'], array_keys($storages)) ?
			$storages[$statement['storage']] : $storages[InternalDataMod::storageConstant];

		if ($statement['storage'] == SensAppDataMod::storageConstant)
			$sensapp = SensAppDataMod::decodeAdditionalData($statement['additional_data']);
		else
			$sensapp = array();

		CHead::addJS('Data_add');
		DataView::showAddForm(array_merge(array(
						'name' => $statement['name'],
						'desc' => $statement['description'],
						'type' => $n_datamod->folder,
						'location' => $storage,
						'sensapp' => $sensapp,
						'youtube_location' => ''),$_REQUEST),
			DataMod::getDataTypes(), 'edit');
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
