<?php
class DTableau extends DAbstract
{
	const nom = 'Tableau';

	public function show() 
	{
		CHead::addJS('jquery.tablesorter.min');
		echo <<<END
		<table class="zebra-striped bordered-table display_list">
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
			echo "<td>", htmlspecialchars($data[$k]), "</td>";

			}
			echo "</tr>\n";
		}
		echo "</tbody></table>";
	}
}
?>
