<?php
class DisplayView extends AbstractView
{
	public static function showGraphChoiceMenu($data, $well = true, $prefs = array()){
		$cdata = count($data);
		$ii = 0;
		foreach ($prefs as $pref) {
			for ($i = 0; $i < $cdata; ++$i) {
				if ($data[$i]->dossier === $pref) {
					$tmp = $data[$i];
					$data[$i] = $data[$ii];
					$data[$ii] = $tmp;
					++$ii;
				}
			}
		}

		CHead::addCSS('Display');
		if ($well) echo '<div class="well">';
		echo <<<END
		<div id="selection_graph">
			<ul class="media-grid">	
END;
		
		foreach ($data as $display)
		{
			$dossier = $display->dossier;
			$url = CNavigation::generateMergedUrl('Display','view', array('type' => $dossier));
			echo <<<END
				<li>
					<a href="$url" class="liengraph">
						<img alt="" src="/InspecteurDeryque/Display/$dossier/thumbnail.png" class="thumbnail"/>
						<h4>{$display->nom}</h4>
					</a>
				</li>
END;
		}

		echo <<<END
			</ul>
		</div>
END;
		if ($well) echo '</div>';
	}

	public static function showBackButtons($url_back) {
		echo '<div class="well">';
		self::showButton($url_back, 'info', 'Retour au relevé', 'back');
		echo '</div>';
	}

	public static function showPageWithLayout(){
		echo <<<END
		<div class="container-fluid">
			<div class="sidebar">
				<div class="well">
END;
		DisplayView::showRelevesChoiceMenu();
		echo <<<END
				</div>
			</div>
			<div class="content">
				<div class="hero-unit">
END;
		$data = DisplayMod::getDisplayTypes();
		DisplayView::showGraphChoiceMenu($data, false);
		DashboardView::showGraph();
		echo <<<END
				</div>
			</div>
		</div>
END;
	}

	public static function showRelevesChoiceMenu(){
		echo <<<END
		<h4 id="titre_releves">Liste des relevés</h4>
		<div id="releves">
			<table class="zebra-striped">
END;
		$releves = DataMod::getReleves($_SESSION['bd_id']);
		foreach($releves as $releve){
			echo <<<END
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
				<td>
END;
			echo htmlspecialchars($releve["name"]), "</td>";
			echo "</tr>";
		}
		echo "</table></div>";
	}
}

?>
