<?php
/**
 * Displays a classic table.
 */
class DTableau extends DAbstract
{
	const name = 'Tableau';

	public function show() 
	{
		if ($this->gererVide()) return;

		CHead::addJS('jquery.tablesorter.min');
		echo <<<END
		<table class="table table-striped table-bordered display_list">
			<thead><tr>
END;
		$couleurs = array('yellow', 'blue', 'green', 'purple','red', 'orange');
		$i = 0;
		foreach ($this->structure as $k => $v) {
			$hv = htmlspecialchars($v);
			$couleur = $couleurs[($i++)%6];
			echo "<th class=\"header $couleur\">$hv</th>\n";
		}
				
		echo <<<END
			</tr></thead>
			<tbody>
END;
		foreach ($this->data as $data)
		{
			echo "<tr>";
			foreach ($this->structure as $k => $v) {
				if ($k === 'timestamp') {
					echo "<td>", strftime('%d/%m/%Y %H:%M:%S', $data[$k]), "</td>";	
				} else {
					echo "<td>", htmlspecialchars($data[$k]), "</td>";
				}
			}
			echo "</tr>\n";
		}
		echo "</tbody></table>";
	}
}
?>
