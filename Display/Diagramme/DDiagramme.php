<?php
class DDiagramme extends DAbstract
{
	const nom = 'Diagramme';

	public function show()
	{
		CHead::addJs('raphael-min');
		CHead::addJs('g.raphael-min');
		CHead::addJs('g.pie-min');
		CHead::addJs('Plot_js/graphael_piechart');

		$tab = $this->data;

		$ths="<tr>";
		$tds="<tr>";
		$i_max = count($tab['abscisse']);
		for($i=0; $i<$i_max; $i++){
			$ths.="<th>".$tab["abscisse"][$i]."</th>";
			$tds.="<td>".$tab["ordonnee"][$i]."</td>";
		}
		$ths.="</tr>";
		$tds.="</tr>";
		echo "<div id='pieChart' style='margin:10px;'></div>"
					."<div id='data' style='display:none;'><table>"
					.$ths.$tds."</table></div>"
					."<script>showPieChart();</script>";
	}
}
?>
