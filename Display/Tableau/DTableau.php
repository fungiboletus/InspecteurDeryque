<?php
class DTableau extends DAbstract
{
	const nom = 'Tableau';

	public function show() 
	{
		if ($this->gererVide()) return;

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
				if ($k === 'timestamp') {
					echo "<td><span style=\"display:none\">",htmlspecialchars($data[$k]),"</span>", strftime('%d/%m/%Y %H:%M:%S', $data[$k]), "</td>";	
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
