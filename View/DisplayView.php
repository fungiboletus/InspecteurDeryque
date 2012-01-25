<?php
class DisplayView{
	public static function showGraphChoiceMenu(){
		echo <<<END
		<div class="content">
			<div class="hero-unit">
				<div id=selection_graph>
					<ul class="media-grid">	
						<li>
							<a href="#" class="liengraph">
								<img alt="" src="/InspecteurDeryque/Data/Calories/thumbnail.png" class="thumbnail"/>
							</a>
						</li>
					</ul>
				</div>
			<h1>Page Principale</h1>
			<p>Texte</p>
			</div>
		</div>
END;
	}
}

?>
