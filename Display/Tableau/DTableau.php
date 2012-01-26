<?php
class DTableau extends DAbstract
{
	const nom = 'Tableau';

	public function show() 
	{
		$tab = $this->data;
		CHead::addJS('jquery.tablesorter.min');
		echo <<<END
		<table class="zebra-striped bordered-table display_list">
			<thead><tr>
				<th class="header yellow">Abscisse</th>
				<th class="header blue">Ordonnée</th>
			</tr></thead>
			<tbody>
END;
		$i_max = count($tab['abscisse']);
		for($i=0; $i<$i_max; $i++){
			echo "<tr>";
			echo "<td>", $tab["abscisse"][$i], "</td>";
			echo "<td>", $tab["ordonnee"][$i], "</td>";
			echo "</tr>\n";
		}
		echo "</tbody></table>";
	}
}
?>
