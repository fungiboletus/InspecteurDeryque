<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Manages multiple statements.
 */
class DataMulti {
	public function index() {
		CNavigation::setTitle('Multiple statements');

		$statements = DataMod::getStatementsMulti();

		DataMultiView::showStatementsList($statements);

		DataMultiView::showAddButton();
	}

	/**
	 *	Store and modify multiple statement.
	 */
	public function form() {

		// The form mode (add a new statement by default)
		$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'add';

		// If the form is submited, and all the necessary variables are present
		if (CNavigation::isValidSubmit(['name','desc', 'releve', 'old_id'], $_REQUEST))
		{
			// Find the old multiple statement with the same name
			$old_statement = R::findOne('multi_releve', 'name = ? and user_id = ?',
						[$_REQUEST['name'], $_SESSION['bd_id']]);

			// If it's not an update
			if ($old_statement && $old_statement['id'] != $_REQUEST['old_id'])
			{
				new CMessage(_('A multiple statement already exist with the same name'), 'error');
			}
			// If the user have selected no one statement…
			else if(count($_REQUEST['releve']) < 1)
			{
				new CMessage(_('You have to select one or more statements'), 'error');
			}
			else
			{
				$statement = null;
				$user = $_SESSION['user'];

				// Create a new multiple statement in add mode
				if ($mode === 'add')
					$statement = R::dispense('multi_releve');

				// Or edit the old multiple statement
				else if ($mode === 'edit')
				{
					// load the Redbean object for the old multiple statement
					$statement = R::load('multi_releve', intval($_REQUEST['old_id']));

					// if the old multiple statement is wrong
					if (!$statement)
					{
						new CMessage(_('You had asked to edit an unknown multiple statement. Creating a new multiple statement.'),
							'warning');
						// Create a new multiple statement
						$mode = 'add';
						$statement = R::dispense('multi_releve');
					}
					// If it's a hack ?
					else if ($statement->user != $_SESSION['user'])
						CTools::hackError();
					// If everything is ok, reset the attached statements for the multiple statement
					else
						$statement->sharedReleve = [];
				}

				// Set all the data
				$statement->user = $user;
				$statement->name = $_REQUEST['name'];
				$statement->description = $_REQUEST['desc'];

				// For each selected statement
				foreach($_REQUEST['releve'] as $id)
				{
					// Load the statement
					$rel = R::load('releve', $id);

					// a few times ago, a horrible security hole was here
					if ($rel->user != $user)
						CTools::hackError();

					// Add the statement
					$statement->sharedReleve[] = $rel;
				}

				R::store($statement);

				if ($mode === 'add')
				{
					new CMessage(_('Multiple statement : ').$statement->name._(' correctly added'));
					CNavigation::redirectToApp('DataMulti', 'form');
				}
				else
				{
					new CMessage(_('Successfull update'));
					CNavigation::redirectToApp('DataMulti', 'view', ['name' => $statement->name]);
				}
			}
		}
		// If the form is submited, but a value is absent
		else if (CNavigation::isPost())
			// In the majority of case, it's an absence of selected statements
			new CMessage(_('You have to select one or more statements'), 'error');

		CNavigation::setTitle('New multiple statement');
		CNavigation::setDescription('Select the statements which you want to compose');

		// Show the form, with default values management
		DataMultiView::showAddForm(array_merge([
			'old_id' => isset($_REQUEST['old_id']) ? intval($_REQUEST['old_id']) : -1,
			'name' => '',
			'releve' => [],
			'desc' => ''], $_REQUEST), $mode);
	}

	/**
	 *	View a multiple statement.
	 */
	public function view() {
		// Load the multiple statement
		$statement = isset($_REQUEST['name']) ? DataMod::getMultiStatement($_REQUEST['name']) : false;

		// If the multiple statement is not present, show a 404 error
		if (!$statement)
			CTools::hackError('page_not_found');

		CNavigation::setTitle(_('Multiple statement : ').$_REQUEST['name']);

		// Give the selected ids
		$releves = [];
		foreach ($statement->sharedReleve as $r)
			$releves[] = $r->getId();

		// Show the form
		DataMultiView::showAddForm(array_merge([
			'old_id' => $statement->getId(),
			'name' => $statement->name,
			'releve' => $releves,
			'desc' => $statement->description], $_REQUEST), 'edit');
	}


	/**
	 *	Remove a statement.
	 */
	public function remove() {
		// Load the statement
		$statement = isset($_REQUEST['name']) ? DataMod::getMultiStatement($_REQUEST['name']) : false;

		if (!$statement)
			CTools::hackError();

		// If the user has confirmed
		if (isset($_REQUEST['confirm'])) {
			// load the statement in a redbean object
			$statement = R::load('multi_releve', $statement['id']);

			// Delete the statement
			R::trash($statement);

			new CMessage(_('The multiple statement has been correctly deleted.'),'warning');
			CNavigation::redirectToApp('DataMulti');

		} else {
			CNavigation::setTitle('Deleting multiple statement : '.$statement['name']);
			CNavigation::setDescription('Consequences will never be the same!');

			DataMultiView::showRemoveForm(
				$statement['description'],
				CNavigation::generateMergedUrl('DataMulti', 'remove', ['confirm' => 'yes']),
				CNavigation::generateMergedUrl('DataMulti', 'view'));
		}
	}

}
?>
