<?php
class DisplayView{
	public static function showGraphChoiceMenu($data){
		CHead::addCSS('Display');
		echo <<<END
		<div id="selection_graph">
			<ul class="media-grid">	
END;
		
		foreach ($data as $display)
		{
			echo <<<END
				<li>
					<a href="#" class="liengraph">
						<img alt="" src="/InspecteurDeryque/Data/Calories/thumbnail.png" class="thumbnail"/>
					</a>
				</li>
END;
		}

		echo <<<END
			</ul>
		</div>
END;
	}
}

?>
