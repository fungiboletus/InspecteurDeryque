<?php
class DBoites extends DAbstract
{
	const nom = 'Boites';
	
	private static function quartiles($entry){
		// FIXME code en dur, affiche la température
		$type='temperature';
		
		$subtab=array();
		foreach($entry as $c=>$key){
			array_push($subtab,$key[$type]);
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
		CHead::addJs('raphael-min');
		CHead::addJs('Plot_js/raphael_boxplot');

		$tab = self::quartiles($this->data);
		
		$tds="<tr class=''>";
		$i_max=count($tab);
		for($i=0; $i<$i_max; $i++){
			$tds.="<td>".$tab[$i]."</td>";
		}
		$tds.="</tr>";
		echo "<div id='first' style='width:640px;height:480px;'></div>"
					."<div id='data' style='display:none;'><table>"
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
