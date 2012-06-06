<?php
class DGraphique extends DAbstract
{
	const nom = 'Graphique';

	public function show()
	{
		if ($this->gererVide()) return;

		CHead::addJs('raphael-min');
		CHead::addJs('g.raphael-min');
		CHead::addJs('g.line-min');
		CHead::addJs('Plot_js/graphael_linechart');

		$first = null;
		$ths="<tr>";
		$tds="<tr>";
		foreach ($this->data as $data)
		{
			if ($first === null) {
				$first = $data['timestamp'];
			}

			$diff = ($data['timestamp'] - $first)*1000000;

			$ths.="<th>".$diff."</th>\n";

			// TODO Une seule ligne normalement…
			foreach ($this->structure as $k => $v) {
				if ($k !== 'timestamp')
					$tds.="<td>".$data[$k]."</td>\n";
			}
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
