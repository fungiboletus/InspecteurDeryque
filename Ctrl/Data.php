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

		$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'add';

		if (CNavigation::isValidSubmit(array('name','desc', 'type', 'location', 'old_id'), $_REQUEST))
		{

			if ($mode === 'add' &&
					R::findOne('releve', 'name = ? and user_id = ?',
						array($_REQUEST['name'], $_SESSION['bd_id'])))
			{
				new CMessage(_('A statement already exist with the same name'), 'error');
			}
			else
			{
				$statement = null;

				if ($mode === 'add')
					$statement = R::dispense('releve');
				else if ($mode === 'edit') {
					$statement = R::load('releve', intval($_REQUEST['old_id']));

					if (!$statement)
					{
						new CMessage(_('You had asked to edit an unknown statement. Creating a new statement.'),
							'warning');
						$mode = 'add';
						$statement = R::dispense('releve');
					}
				}

				$datamode = R::findOne('datamod', 'modname = ?', array($_REQUEST['type']));

				if (!$datamode) {
					if (DataMod::modExist($_REQUEST['type'])) {
						$datamode = R::dispense('datamod');
						$datamode->modname = $_REQUEST['type'];
						R::store($datamode);
					}
					else
						CTools::hackError();
				}

				$user = $_SESSION['user'];

				$statement->mod = $datamode;
				$statement->user = $user;
				$statement->name = $_REQUEST['name'];
				$statement->description = $_REQUEST['desc'];

				$locations = array(
					'youtube' => 'YoutubeDataMod',
					'sensapp' => 'SensAppDataMod');

				$location = in_array($_REQUEST['location'], array_keys($locations)) ?
					$locations[$_REQUEST['location']] : 'InternalDataMod';

				// PHP in her limits
				$statement->storage = constant($location.'::storageConstant');
				$statement->additional_data = call_user_func($location.'::generateAdditionalData');

				R::store($statement);

				if ($mode === 'add')
				{
					new CMessage(_('Statement : ').$statement->name._(' correctly added'));
					CNavigation::redirectToApp('Data', 'form');
				}
				else
				{
					new CMessage(_('Successfull update'));
					CNavigation::redirectToApp('Data', 'view', array('name' => $statement->name));
				}
			}
		}
		else if (CNavigation::isPost())
			new CMessage(_('Please select a statement type'), 'error');

		CHead::addJS('Data_add');

		CNavigation::setTitle($mode === 'edit' ? _('Editing : '.$_REQUEST['name']) : _('New statement'));

		DataView::showAddForm(array_merge(array(
						'old_id' => isset($_REQUEST['old_id']) ? intval($_REQUEST['old_id']) : -1,
						'name' => '',
						'desc' => '',
						'type' => '',
						'location' => '',
						'sensapp' => array(),
						'youtube_location' => ''),$_REQUEST),
			DataMod::getDataTypes(), $mode);
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
						'old_id' => $statement['id'],
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
