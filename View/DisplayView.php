<?php
class DisplayView extends AbstractView
{
	public static function showGraphChoiceMenu($data){
		CHead::addCSS('Display');
		echo <<<END
		<div class="well">
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
		</div>
END;
	}

	public static function showBackButtons($url_back) {
		echo '<div class="well">';
		self::showButton($url_back, 'info', 'Retour au relevé', 'back');
		echo '</div>';
	}
}

?>
