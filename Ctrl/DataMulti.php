<?php
/**
 * Manages statements.
 */
class DataMulti {
	public function index() {
		CNavigation::setTitle('Multiple statements');

		$statements = DataMod::getStatementsMulti($_SESSION['bd_id']);

		DataMultiView::showStatementsList($statements);

		DataMultiView::showAddButton();
	}

	public function form() {

		$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'add';

		if (CNavigation::isValidSubmit(['name','desc', 'releve', 'old_id'], $_REQUEST))
		{
			$old_statement = R::findOne('multi_releve', 'name = ? and user_id = ?',
						[$_REQUEST['name'], $_SESSION['bd_id']]);
			if ($old_statement && $old_statement['id'] != $_REQUEST['old_id'])
			{
				new CMessage(_('A multiple statement already exist with the same name'), 'error');
			}
			else if(count($_REQUEST['releve']) < 1)
			{
				new CMessage(_('You have to select one or more statements'), 'error');
			}
			else
			{
				$statement = null;
				$user = $_SESSION['user'];

				if ($mode === 'add')
					$statement = R::dispense('multi_releve');
				else if ($mode === 'edit')
				{
					$statement = R::load('multi_releve', intval($_REQUEST['old_id']));

					if (!$statement)
					{
						new CMessage(_('You had asked to edit an unknown multiple statement. Creating a new multiple statement.'),
							'warning');
						$mode = 'add';
						$statement = R::dispense('multi_releve');
					}
					else if ($statement->user != $_SESSION['user'])
						CTools::hackError();
					else
						$statement->sharedReleve = [];
				}

				$statement->user = $user;
				$statement->name = $_REQUEST['name'];
				$statement->description = $_REQUEST['desc'];

				foreach($_REQUEST['releve'] as $id)
				{
					$rel = R::load('releve', $id);

					// a few times ago, a horrible security hole was here
					if ($rel->user != $user)
						CTools::hackError();

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
		else if (CNavigation::isPost())
			new CMessage(_('You have to select one or more statements'), 'error');

		CNavigation::setTitle('New multiple statement');
		CNavigation::setDescription('Select the statements which you want to compose');

		DataMultiView::showAddForm(array_merge([
			'old_id' => isset($_REQUEST['old_id']) ? intval($_REQUEST['old_id']) : -1,
			'name' => '',
			'releve' => [],
			'desc' => ''], $_REQUEST), $mode);
	}

	public function view() {
		$statement = isset($_REQUEST['name']) ? DataMod::getMultiStatement($_REQUEST['name'], $_SESSION['bd_id']) : false;
		if (!$statement)
			CTools::hackError();

		CNavigation::setTitle(_('Multiple statement : ').$_REQUEST['name']);

		$releves = [];

		foreach ($statement->sharedReleve as $r)
			$releves[] = $r->getId();

		DataMultiView::showAddForm(array_merge([
			'old_id' => $statement->getId(),
			'name' => $statement->name,
			'releve' => $releves,
			'desc' => $statement->description], $_REQUEST), 'edit');
	}


	public function remove() {
		$statement = isset($_REQUEST['name']) ? DataMod::getMultiStatement($_REQUEST['name'], $_SESSION['bd_id']) : false;
		if (!$statement)
			CTools::hackError();

		if (isset($_REQUEST['confirm'])) {
			$statement = R::load('multi_releve', $statement['id']);
			R::exec('delete from multi_releve where id = ?', [$statement['id']]);
			R::trash(R::load('multi_releve', $statement['id']));
			CNavigation::redirectToApp('DataMulti');
		} else {
			CNavigation::setTitle('Deleteing multiple statement : '.$statement['name']);
			CNavigation::setDescription('Consequences will never be the same!');

			DataMultiView::showRemoveForm(
				$statement['description'],
				CNavigation::generateMergedUrl('DataMulti', 'remove', ['confirm' => 'yes']),
				CNavigation::generateMergedUrl('DataMulti', 'view'));
		}
	}

}
?>
