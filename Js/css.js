var vertical = function() {

$('#accordion').css("position","fixed");
$('#accordion').css("margin-left", "650px");
$('#accordion').css("width","400px");

$('#tablePics').html('<tr><td> Seuil maximum : <span id="picMax"></span></td><td><a class="btn" onClick="setAction(\'pics\');minOrMax=\'max\'"><img src="/InspecteurDeryque/Img/icons/Cible.png" alt="Placer" title="Placer une limite maximum sur le graphe" /></a><a class="btn" onClick="rmLigne(\'max\')"><img src="/InspecteurDeryque/Img/icons/supprimer.png" alt="Supprimer" title="Supprimer la limite maximum existante"/></a></td></tr><tr><td> Seuil minimum : <span id="picMin"></span></td><td><a class="btn" onClick="setAction(\'pics\');minOrMax=\'min\'"><img src="/InspecteurDeryque/Img/icons/Cible.png" alt="Placer" title="Placer une limite minimum sur le graphe" /></a><a class="btn" onClick="rmLigne(\'min\')"><img src="/InspecteurDeryque/Img/icons/supprimer.png" alt="Supprimer" title="Supprimer une limite minimum existante"/></a></td></tr>');
		
// Anti Horizontal
$('#holder').css("margin-top", "");
$('#accordion > div').css("height", "");
$('#controles').css("float", "");
$('#informations').css("float", "");
$('#informations').css("width", "");
$('#controles > ul li').css("display", "");
$('#controles').css("width", "");
$('#divSelect > ul li').css("display", "");

for (var x = 0; x < charts.length; x++) {
	$('#holder'+x).css('float', '');
	$('#holder'+x).css('display', '');
	$('.close').css('float', 'right');
	charts[x].setSize(600, 300);
}

};

var horizontal = function() {
	//Anti vertical
	$('#accordion').css("width", "");
	
	$('#accordion').css("position", "absolute");
	$('#accordion').css("margin-left", "10px");
	$('#accordion > div').css("height", "180");
	$('#controles').css("float", "left");
	$('#controles').css("width", "480px");
	$('#informations').css("float", "left");
	$('#informations').css("width", "180px");
	$('#controles > ul li').css("display", "block");
	$('#tablePics').html('<tr><td> Seuil maximum : <span id="picMax"></span></td><td><a class="btn" onClick="setAction(\'pics\');minOrMax=\'max\'"><img src="/InspecteurDeryque/Img/icons/Cible.png" alt="Placer" title="Placer une limite maximum sur le graphe" /></a><a class="btn" onClick="rmLigne(\'max\')"><img src="/InspecteurDeryque/Img/icons/supprimer.png" alt="Supprimer" title="Supprimer la limite maximum existante"/></a></td><td> Seuil minimum : <span id="picMin"></span></td><td><a class="btn" onClick="setAction(\'pics\');minOrMax=\'min\'"><img src="/InspecteurDeryque/Img/icons/Cible.png" alt="Placer" title="Placer une limite minimum sur le graphe" /></a><a class="btn" onClick="rmLigne(\'min\')"><img src="/InspecteurDeryque/Img/icons/supprimer.png" alt="Supprimer" title="Supprimer une limite minimum existante"/></a></td></tr>')
	$('#holder').css("margin-top", "220px");
	$('#divSelect > ul li').css("display", "inline");
	for (var x = 0; x < charts.length; x++) {
		$('#holder'+x).css('float', 'left');
		$('#holder'+x).css('display', 'inline');
		$('.close').css('float', 'left');
		charts[x].setSize(300, 200);
	}
};
