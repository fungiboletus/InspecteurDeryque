<?php
/**
 * Displays a box chart.
 */
class DBoites extends DAbstract
{
	const name = 'Boite';
	
	/**
	 * Permet d'accéder à un tableau par un index numérique 
	 * alors qu'il ne l'est pas.
	 * Par exemple avec a=array('a'=>"bon",'b'=>"jour"), a[0] ne marche pas,
	 * tandis que goAssocArrayNumeric($a,0) renverra "bon".
	 */
	private static function goAssocArrayNumeric($arrAssoc, $key=-1){
		$i = -1;
		foreach ($arrAssoc as $k => $v){
			$i++;
			if ($i == $key){
				return $v;
			}
		}
		return FALSE;
	}
	
	private static function quartiles($entry){
		$subtab=array();
		foreach($entry as $c=>$key){
			array_push($subtab,self::goAssocArrayNumeric($key,2));
		}
		
		sort($subtab,SORT_NUMERIC);
		$tab=array($subtab[0],0,0,0,end($subtab));
		$i_max=count($subtab);
		for($i=0;$i<$i_max;$i++){
			// 1er quartile
			if ($tab[1] == 0 && $subtab[$i] > round($tab[4]/4,0, PHP_ROUND_HALF_UP) ){
				$tab[1] = $subtab[$i];
			}
			// 3ème quartile
			if ($tab[3] == 0 && $subtab[$i] > round($tab[4]*3/4,0, PHP_ROUND_HALF_UP) ){
				$tab[3] = $subtab[$i];
			}
		}
		// Valeur médiane
		if ($i_max%2!=0){
			$tab[2]=$subtab[ ($i_max+1)/2 ];
		} else {
			$tab[2]=($subtab[round( ($i_max+1)/2,0, PHP_ROUND_HALF_DOWN)]
					+$subtab[round( ($i_max+1)/2,0, PHP_ROUND_HALF_UP)  ])/ 2 ;
		}
		return $tab;
	}
	
	public function show()
	{
		if ($this->gererVide()) return;
		
		CHead::addJs('raphael-min');
		CHead::addJs('Plot_js/raphael_boxplot');

		$tab = self::quartiles($this->data);
		
		$type=self::goAssocArrayNumeric($this->structure,1);
		
		$tds="<tr class=''>";
		$i_max=count($tab);
		for($i=0; $i<$i_max; $i++){
			$tds.="<td>".$tab[$i]."</td>";
		}
		$tds.="</tr>";
		echo "<div id='first' style='width:640px;height:480px;'></div>"
					."<div id='data' class='".$type."'><table>"
					."<tr>
						<th>Min</th>
						<th>Quartile 1</th>
						<th>Moyenne</th>
						<th>Quartile 3</th>
						<th>Max</th>
					</tr>"
					.$tds."</table></div>"
					."<script>showBoxPlot();</script>";
	}
}
?>
