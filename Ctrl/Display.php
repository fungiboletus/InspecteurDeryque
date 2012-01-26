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
		if (!DisplayMod::loadDisplayType($type)||!isset($_REQUEST['nom']))
		{
			CTools::hackError();
			return;
		}

		$classe = "D$type";
		//$g = "D$type"::nom;
		$g = new $classe();

		/*$salut = 42;
		$coucou = 'salut';
		echo $$coucou;*/
		CNavigation::setTitle($g::nom);

		$g->data = self::TriPoint(self::tableauRandom(15));
		$g->show();

		DisplayView::showBackButtons(CNavigation::generateUrlToApp('Data','view',
			array('nom'=>$_REQUEST['nom'])));
	}
}
?>
