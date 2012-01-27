<?php
class Display
{
	private static function tableauRandom($nb = 10, $max = 100){
		for($i = 0 ; $i < $nb ; $i++){
			$tab["abscisse"][$i] = rand(1, $max);
			$tab["ordonnee"][$i] = rand(1, $max);
		}
		return $tab;
	}
	private static function TriPoint($tab){
		array_multisort($tab["abscisse"], SORT_ASC, $tab["ordonnee"]);
		return $tab;
	}

	public function index() {
		CNavigation::setTitle('Super page');
		CNavigation::setDescription('Tout reste à faire');
	}

	public function view() {

		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'default';

		$releve = isset($_REQUEST['nom']) ? DataMod::getReleve($_REQUEST['nom'], $_SESSION['bd_id']) : false;

		$d = DisplayMod::loadDisplayType($type);
		if (!$d||!$releve){
			CTools::hackError();
			return;
		}

		$n_datamod = DataMod::loadDataType($releve['modname']);
		$g = $d->instancier();

		/*$salut = 42;
		$coucou = 'salut';
		echo $$coucou;*/
		CNavigation::setTitle($g::nom);


		$g->structure = $n_datamod->getVariables();
		$g->data = self::TriPoint(self::tableauRandom(15));
		$g->data = R::getAll('select * from d_'.$n_datamod->dossier.' where user_id = ? and releve_id = ?', array($_SESSION['bd_id'], $releve['id']));
		$g->show();

		DisplayView::showBackButtons(CNavigation::generateUrlToApp('Data','view',
			array('nom'=>$_REQUEST['nom'])));
	}
}
?>
