<?php
class DisplayView extends AbstractView
{
	public static function showGraphChoiceMenu($data, $well = true, $prefs = array(), $action = 'view'){
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
			$url = CNavigation::generateMergedUrl('Display', $action, array('type' => $dossier));
			$class = in_array($dossier, $prefs, true) ? ' class="display_prefs"' : '';
			echo <<<END
				<li$class>
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
				<div id="message_visualisations_vide">
					<h1>OWIIIII <small>C'est trop bien !'</small></h1>
					<p>Pour commencer, sélectionnez votre relevé.</p>
				</div>
				<div id="espace_visualisations"></div>
				</div>
			</div>
		</div>
END;
		/*$data = DisplayMod::getDisplayTypes();
		DisplayView::showGraphChoiceMenu($data, false);*/
		//DashboardView::showGraph();
	}

	public static function showRelevesChoiceMenu(){
		echo <<<END
		<h4 id="titre_releves">Liste des relevés</h4>
		<div id="releves">
			<table class="zebra-striped">
END;
		$releves = DataMod::getReleves($_SESSION['bd_id']);
		foreach($releves as $releve){
			$hname = htmlspecialchars($releve['name']);
			$hurl = CNavigation::generateUrlToApp('Display', 'iframe_view', array('nom'=>$releve['name']));
			$hid = sha1($releve['name']);
			echo <<<END
			<tr>
				<td><input type="checkbox" value="$hurl" name="i$hid"/></td>
				<td>$hname</td>
			</tr>
END;
		}
		echo "</table></div>";
	}
}

?>
