<?php
class Data
{
	public function index() {
		CNavigation::setTitle('Gestion des données');
		CNavigation::setDescription('Pour plus de plaisir !');

		DataView::showAddButton();
	}

	public function choose() {
		CNavigation::setTitle('Ajouter un nouveau type de données');
		CNavigation::setDescription('Sélectionnez le type à utiliser');

		$data = DataMod::getDataTypes();
		DataView::showDataTypeList($data);
	}

	public function add() {
		global $ROOT_PATH;
		if (!isset($_REQUEST['type'])) CNavigation::redirectToApp();

		$data_type = DataMod::loadDataType($_REQUEST['type']);
		
		CNavigation::setTitle('Ajout de données de type '.$data_type->nom);

		$hdir = htmlspecialchars($data_type->dossier);
		echo <<<END
			<img class="thumbnail" src="$ROOT_PATH/Data/$hdir/thumbnail.png" alt=""/>
END;
	}
}
?>
