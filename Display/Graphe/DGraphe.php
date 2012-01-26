<?php
class DGraphe
{
	const nom = 'Graphe';
	public $data = null;

	public function show()
	{
		CHead::addJs('raphael-min');
		CHead::addJs('g.raphael-min');
		CHead::addJs('g.line-min');
		CHead::addJs('Plot_js/graphael_linechart');

		$tab = $this->data;

		$ths="<tr>";
		$tds="<tr>";
		for($i=0; $i<count($tab["abscisse"]); $i++){
			$ths.="<th>".$tab["abscisse"][$i]."</th>";
			$tds.="<td>".$tab["ordonnee"][$i]."</td>";
		}
		$ths.="</tr>";
		$tds.="</tr>";
		echo "<div id='holder' style='margin:10px;'></div>"
					."<div id='data' style='display:none;'><table>"
					.$ths.$tds."</table></div>"
					."<script>showLineChart();</script>";
	}
}
?>
