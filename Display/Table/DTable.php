<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Displays a classic table.
 */
class DTable extends DAbstract
{
	const name = 'Table';

	/*public function show()
	{
		CHead::addJS('jquery.tablesorter.min');
		DAbstract::show();
		/*
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
	}*/
}
?>
