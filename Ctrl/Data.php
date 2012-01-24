<?php
class Data
{
	public function index() {
		CNavigation::setTitle('Gestion des données');
		CNavigation::setDescription('All your data are belong to us');

		echo <<<END
		<table class="zebra-striped">
			<tr>
				<th>Nom</th>
				<th>Description</th>
				<th>Type</th>
			</tr>
END;
		foreach (R::getAll('select name, description, modname from releve r, datamod d where r.user_id = ? and r.mod_id = d.id', array($_SESSION['bd_id'])) as $releve) {
			echo "\t<tr><td>", htmlspecialchars($releve['name']),
				 "</td><td>", htmlspecialchars($releve['desc']),
				 "</td><td>", htmlspecialchars($releve['modname']), "</td></tr>\n";
		}

		echo "</table>";

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
			$mode = R::findOne('datamod', 'modname = ?', array($_REQUEST['mode']));

			if (!$mode) {
				$mode = R::dispense('datamod');
				$mode->modname = $_REQUEST['mode'];
				R::store($mode);
			}

			$user = R::load('user', $_SESSION['bd_id']);

			$releve = R::dispense('releve');
			$releve->mod = $mode;
			$releve->user = $user;
			$releve->name = $_REQUEST['nom'];
			$releve->description = $_REQUEST['desc'];

			R::store($releve);

			new CMessage('Relevé correctement ajouté');
			CNavigation::redirectToApp('Data');

		}
		else
		{
			global $ROOT_PATH;
			if (!isset($_REQUEST['type'])) CNavigation::redirectToApp();

			$data_type = DataMod::loadDataType($_REQUEST['type']);
			
			CNavigation::setTitle('Nouveau relevé de type «'.$data_type->nom.'»');

			/*$hdir = htmlspecialchars($data_type->dossier);
			echo <<<END
				<img class="thumbnail" src="$ROOT_PATH/Data/$hdir/thumbnail.png" alt=""/>
	END;*/

			DataView::showAddForm($data_type->dossier);
		}
	}
}
?>
