<?php
/**
 * Manages statements.
 */
class Data
{
	public function index() {
		CNavigation::setTitle(_('Simple statements'));
		CNavigation::setDescription(_('All your data are belong to us'));

		$statements = DataMod::getStatements();

		DataView::showStatementsList($statements);

		DataView::showAddButton();
	}

	/**
	 *	Store and modify statements
	 */
	public function form() {

		// The form mode (add a new statement by default)
		$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'add';

		// Store in the session the return argument
		// because if the user have to submit the form many times
		// maybe, he want to still have this arguments effect
		if (isset($_REQUEST['return']))
			$_SESSION['form_return'] = $_REQUEST['return'];

		// If the form is submited, and all the necessary variables are present
		if (CNavigation::isValidSubmit(['name','desc', 'type', 'storage', 'old_id'], $_REQUEST))
		{

			// Find the old statement with the same name
			$old_statement = R::findOne('releve', 'name = ? and user_id = ?',
						[$_REQUEST['name'], $_SESSION['bd_id']]);

			// If it's not an update
			if ($old_statement && $old_statement['id'] != $_REQUEST['old_id'])
			{
				new CMessage(_('A statement already exist with the same name'), 'error');
			}
			else
			{
				$statement = null;
				$user = $_SESSION['user'];

				// Create a new statement in add mode
				if ($mode === 'add')
					$statement = R::dispense('releve');

				// Or edit the old statement
				else if ($mode === 'edit')
				{
					// load the Redbean object for the old statement
					$statement = R::load('releve', intval($_REQUEST['old_id']));

					// if the old statement is wrong
					if (!$statement)
					{
						new CMessage(_('You had asked to edit an unknown statement. Creating a new statement.'),
							'warning');
						// Create a new statement
						$mode = 'add';
						$statement = R::dispense('releve');
					}
					// If it's a hack ?
					else if ($statement->user->getID() != $user->getID())
					{
						CTools::hackError();
					}
				}

				// Find the mod with the given name
				// modname are stored in a new table, because they are no many
				// instead of the statements (size optimization)
				$datamode = R::findOne('datamod', 'modname = ?', [$_REQUEST['type']]);

				// If the datamod doesn't exist yet
				if (!$datamode) {
					// If the datamod exist in the application
					if (DataMod::modExist($_REQUEST['type'])) {

						// Create the new datamod
						$datamode = R::dispense('datamod');
						$datamode->modname = $_REQUEST['type'];
						R::store($datamode);
					}
					else
						CTools::hackError();
				}

				// Set all the data
				$statement->mod = $datamode;
				$statement->user = $user;
				$statement->name = $_REQUEST['name'];
				$statement->description = $_REQUEST['desc'];

				// Load associated storage type
				$storage = DataMod::loadStorageType(intval($_REQUEST['storage']));

				// PHP in her limits, in my opinion, php is ugly
				$statement->storage = constant($storage.'::storageConstant');
				$statement->additional_data = call_user_func($storage.'::generateAdditionalData');

				// Storing the statement !! (yes, this comment is useless)
				R::store($statement);

				if ($mode === 'add')
				{
					new CMessage(_('Statement : ').$statement->name._(' correctly added'));

					// If the user ask to return to list, return to the list
					if (isset($_SESSION['form_return']))
					{
						$r = $_SESSION['form_return'];
						unset($_SESSION['form_return']);
						if ($r === 'list')
							CNavigation::redirectToApp('Data');
					}

					// Else, return to the form
					CNavigation::redirectToApp('Data', 'form');
				}
				else
				{
					// If it's an update, show the updated statement
					new CMessage(_('Successfull update'));
					CNavigation::redirectToApp('Data', 'view', ['name' => $statement->name]);
				}
			}
		}
		// If the form is submited, but a value is absent
		else if (CNavigation::isPost())
			// In the majority of case, it's an absent statement type (or a bug)
			new CMessage(_('Please select a statement type'), 'error');

		// Javascript management
		CHead::addJS('Data_add');

		// The title depends on the mode
		CNavigation::setTitle($mode === 'edit' ? _('Editing : '.$_REQUEST['name']) : _('New statement'));

		// Show the form, with default values management
		DataView::showAddForm(array_merge([
						'old_id' => isset($_REQUEST['old_id']) ? intval($_REQUEST['old_id']) : -1,
						'name' => '',
						'desc' => '',
						'type' => '',
						'storage' => '',
						'sensapp' => [],
						'video_location' => ''],$_REQUEST),
			DataMod::getDataTypes(), $mode);
	}

	/**
	 *	View a statement.
	 */
	public function view()
	{
		// Load the statement
		$statement = isset($_REQUEST['name']) ? DataMod::getStatement($_REQUEST['name']) : false;

		// If the statement is not present, show a 404 error
		if (!$statement)
			CTools::hackError('page_not_found');

		CNavigation::setTitle(_('Statement : ').$statement['name']);
		CNavigation::setDescription($statement['description']);

		$n_datamod = DataMod::loadDataType($statement['modname']);

		// Wonderful and very well concepted storage load
		$video_location = '';
		if ($statement['storage'] == SensAppStorage::storageConstant)
			$sensapp = SensAppStorage::decodeAdditionalData($statement['additional_data']);
		else
		{
			$sensapp = [];
			if ($statement['storage'] == VideoStorage::storageConstant)
				$video_location = VideoStorage::decodeAdditionalData($statement['additional_data']);

		}

		// Show the form with the statement's values
		CHead::addJS('Data_add');
		DataView::showAddForm([
						'old_id' => $statement['id'],
						'name' => $statement['name'],
						'desc' => $statement['description'],
						'type' => $n_datamod->folder,
						'storage' => intval($statement['storage']),
						'sensapp' => $sensapp,
						'video_location' => $video_location], DataMod::getDataTypes(), 'edit'); // edit mode
	}

	/**
	 *	Remove a statement.
	 */
	public function remove()
	{
		// Load the statement
		$statement = isset($_REQUEST['name']) ? DataMod::getStatement($_REQUEST['name']) : false;

		if (!$statement)
			CTools::hackError();

		// If the user has confirmed
		if (isset($_REQUEST['confirm'])) {
			// load the statement in a redbean object
			$statement = R::load('releve', $statement['id']);

			// load the datamod
			$modname = R::load('datamod', $statement->mod_id)->modname;

			// delete data in the internal storage (can be empty)
			R::exec('delete from d_'.$modname.' where releve_id = ?', [$statement['id']]);

			// Remove the  statement
			R::trash($statement);

			new CMessage(_('The statement has been correctly deleted.'),'warning');
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
}
?>
