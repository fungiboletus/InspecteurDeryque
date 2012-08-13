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

		if (isset($_REQUEST['return']))
			$_SESSION['form_return'] = $_REQUEST['return'];

		if (CNavigation::isValidSubmit(['name','desc', 'type', 'location', 'old_id'], $_REQUEST))
		{

			$old_statement = R::findOne('releve', 'name = ? and user_id = ?',
						[$_REQUEST['name'], $_SESSION['bd_id']]);
			if ($old_statement && $old_statement['id'] != $_REQUEST['old_id'])
			{
				new CMessage(_('A statement already exist with the same name'), 'error');
			}
			else
			{
				$statement = null;
				$user = $_SESSION['user'];

				if ($mode === 'add')
					$statement = R::dispense('releve');
				else if ($mode === 'edit')
				{
					$statement = R::load('releve', intval($_REQUEST['old_id']));

					if (!$statement)
					{
						new CMessage(_('You had asked to edit an unknown statement. Creating a new statement.'),
							'warning');
						$mode = 'add';
						$statement = R::dispense('releve');
					}
					else if ($statement->user != $user)
						CTools::hackError();
				}

				$datamode = R::findOne('datamod', 'modname = ?', [$_REQUEST['type']]);

				if (!$datamode) {
					if (DataMod::modExist($_REQUEST['type'])) {
						$datamode = R::dispense('datamod');
						$datamode->modname = $_REQUEST['type'];
						R::store($datamode);
					}
					else
						CTools::hackError();
				}

				$statement->mod = $datamode;
				$statement->user = $user;
				$statement->name = $_REQUEST['name'];
				$statement->description = $_REQUEST['desc'];

				$locations = [
					'youtube' => 'YoutubeDataMod',
					'sensapp' => 'SensAppDataMod'];

				$location = in_array($_REQUEST['location'], array_keys($locations)) ?
					$locations[$_REQUEST['location']] : 'InternalDataMod';

				// PHP in her limits
				$statement->storage = constant($location.'::storageConstant');
				$statement->additional_data = call_user_func($location.'::generateAdditionalData');

				R::store($statement);

				if ($mode === 'add')
				{
					new CMessage(_('Statement : ').$statement->name._(' correctly added'));

					if (isset($_SESSION['form_return']))
					{
						$r = $_SESSION['form_return'];
						unset($_SESSION['form_return']);
						if ($r === 'list')
							CNavigation::redirectToApp('Data');
					}

					CNavigation::redirectToApp('Data', 'form');
				}
				else
				{
					new CMessage(_('Successfull update'));
					CNavigation::redirectToApp('Data', 'view', ['name' => $statement->name]);
				}
			}
		}
		else if (CNavigation::isPost())
			new CMessage(_('Please select a statement type'), 'error');

		CHead::addJS('Data_add');

		CNavigation::setTitle($mode === 'edit' ? _('Editing : '.$_REQUEST['name']) : _('New statement'));

		DataView::showAddForm(array_merge([
						'old_id' => isset($_REQUEST['old_id']) ? intval($_REQUEST['old_id']) : -1,
						'name' => '',
						'desc' => '',
						'type' => '',
						'location' => '',
						'sensapp' => [],
						'youtube_location' => ''],$_REQUEST),
			DataMod::getDataTypes(), $mode);
	}

	public function view()
	{
		$statement = isset($_REQUEST['name']) ? DataMod::getStatement($_REQUEST['name'], $_SESSION['bd_id']) : false;

		if (!$statement)
			CTools::hackError();

		CNavigation::setTitle(_('Statement : ').$statement['name']);
		CNavigation::setDescription($statement['description']);

		$n_datamod = DataMod::loadDataType($statement['modname']);

		$storages = [
			InternalDataMod::storageConstant => 'internal',
			YoutubeDataMod::storageConstant => 'youtube',
			SensAppDataMod::storageConstant => 'sensapp'];

		$storage = in_array($statement['storage'], array_keys($storages)) ?
			$storages[$statement['storage']] : $storages[InternalDataMod::storageConstant];

		if ($statement['storage'] == SensAppDataMod::storageConstant)
			$sensapp = SensAppDataMod::decodeAdditionalData($statement['additional_data']);
		else
			$sensapp = [];

		CHead::addJS('Data_add');
		DataView::showAddForm(array_merge([
						'old_id' => $statement['id'],
						'name' => $statement['name'],
						'desc' => $statement['description'],
						'type' => $n_datamod->folder,
						'location' => $storage,
						'sensapp' => $sensapp,
						'youtube_location' => ''],$_REQUEST),
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
			R::exec('delete from d_'.$modname.' where releve_id = ?', [$statement['id']]);
			R::trash(R::load('releve', $statement['id']));
			CNavigation::redirectToApp('Data');
		}
		else
		{
			CNavigation::setTitle('Deleting statement : '.$statement['name']);
			CNavigation::setDescription('Consequences will never be the same!');

			DataView::showRemoveForm(
					$statement['description'],
					CNavigation::generateMergedUrl('Data', 'remove', ['confirm' => 'yes']),
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
