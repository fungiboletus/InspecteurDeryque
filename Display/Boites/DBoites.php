<?php
class DBoites extends DAbstract
{
	const nom = 'Boites';
	
	public $data = null;

	public function show()
	{
		CHead::addJs('raphael-min');
		CHead::addJs('Plot_js/raphael_boxplot');

		$tab = $this->data;

		$tds="<tr class='données 1'>";
		for($i=0; $i<count($tab); $i++){
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
