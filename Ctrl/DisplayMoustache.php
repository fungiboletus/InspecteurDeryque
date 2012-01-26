<?php
class DisplayMoustache
{
	private static function tableauRandom($nb = 10, $max = 100){
		for($i = 0 ; $i < $nb ; $i++){
			$entry[$i] = rand(1, $max);
		}
		return $entry;
	}
	
	private static function quartiles($entry){
		sort($entry,SORT_NUMERIC);
		$tab=array($entry[0],0,0,0,end($entry));
		$i_max=count($entry);
		for($i=0;$i<$i_max;$i++){
			// 1er quartile
			if ($tab[1] == 0 && $entry[$i] > round($tab[4]/4,0, PHP_ROUND_HALF_UP) ){
				$tab[1] = $entry[$i];
			}
			// 3ème quartile
			if ($tab[3] == 0 && $entry[$i] > round($tab[4]*3/4,0, PHP_ROUND_HALF_UP) ){
				$tab[3] = $entry[$i];
			}
		}
		// Valeur médiane
		if ($i_max%2!=0){
			$tab[2]=$entry[ ($i_max+1)/2 ];
		} else {
			$tab[2]=($entry[round( ($i_max+1)/2,0, PHP_ROUND_HALF_DOWN)]
					+$entry[round( ($i_max+1)/2,0, PHP_ROUND_HALF_UP)  ])/ 2 ;
		}
		return $tab;
	}
	
	

	public function index() {
		CNavigation::setTitle('Affichage d\'un graphe');

		require_once('Display/Boites/DBoites.php');

		$g = new DBoites();
		$g->data = self::quartiles(self::tableauRandom(15));

		$g->show();
	}
}
?>
