<?php
class DisplayView{
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
			echo <<<END
				<li>
					<a href="#" class="liengraph">
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

	//public static function show
}

?>
