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
		CNavigation::setTitle('Affichage d\'un graphe');

		require_once('Display/Graphe/DGraphe.php');

		$g = new DGraphe();
		$g->data = self::TriPoint(self::tableauRandom(15));

		$g->show();
	}
}
?>
