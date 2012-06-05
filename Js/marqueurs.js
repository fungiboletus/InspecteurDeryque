var marqueurs = [];			//Marqueurs enregistr�s
var marqueursCourants = [];	//Marqueurs plac�s
var compteurMarqueur = 0;
var compteur = 0;			

var getMarqueur = function(i) {
	for (var j = 0; j < marqueurs.length; j++) {
		if (i == marqueurs[j].i)
			return marqueurs[j];
	}
}
var createMarqueur = function (nom, desc, couleur) {
	var marqueur = {};
	marqueur.nom = nom;
	marqueur.desc = desc;
	marqueur.couleur = couleur;
	marqueur.i = compteurMarqueur++;
	
	return marqueur;
};

var addMarqueur = function (nom, desc, couleur) {
	marqueur = createMarqueur(nom, desc, couleur);
	marqueurs.push(marqueur);
	printMarqueurs();
	$('#listeMarqueurs option').eq(marqueurs.length-1).attr('selected', 'selected');
	description();
};

var rmMarqueur = function (i) {
	for (var j = 0; j < marqueurs.length; j++) {
		if (marqueurs[j].i == i) {
			marqueurs.splice(j,1);
			printMarqueurs();
			return;
		}
	}
};

var printMarqueurs = function() {
	var liste = "";
	$.each(marqueurs, function (i, marqueur) {
		liste +="<option value="+ marqueur.i +">"+ marqueur.nom +"</option>";
	});
	$('#listeMarqueurs').html(liste);
};

var placerMarqueur = function (i, x) {
	var marqueur = getMarqueur(i);
	var marqueurCourant = {};
	marqueurCourant.nom = marqueur.nom;
	marqueurCourant.x = x;
	for (var j = 0; j < charts[0].series[0].xData.length; j++) {
		if (charts[0].series[0].xData[j] == x) {
			marqueurCourant.index = j;
			break;
		}
	}
	marqueurCourant.i = compteur++;
	
	$.each(charts, function (i, chart) {
		var ser = chart.get("flags");
		ser.addPoint({
			x : x,
			title : marqueur.nom,
			id : marqueur.nom+marqueurCourant.i
		}); 
		chart.xAxis[0].addPlotLine({ 
			value: x,
			color: marqueur.couleur,
			width: 2,
			id: marqueur.nom+marqueurCourant.i
		});	
	});
	marqueursCourants.push(marqueurCourant);
	printMarqueursCourants();
};

var rmMarqueurCourant = function (id, i) {
	$.each(charts, function (i, chart) {
		chart.xAxis[0].removePlotLine(id);
		point = chart.get(id);
		point.remove(false);
	});
	marqueursCourants.splice(i,1);
	printMarqueursCourants();
};

var printMarqueursCourants = function() {
	var liste = "";
	$.each(marqueursCourants, function (i, marqueurCourant) {
		liste +="<li><a href='#' onClick='if ($(\"#marqueursCourants"+i+"\").is(\":hidden\")) {$(\"#marqueursCourants"+i+"\").fadeIn();}else {$(\"#marqueursCourants"+i+"\").fadeOut();}'>"+ marqueurCourant.nom +" [x = "+ marqueurCourant.x +" ]</a><a href='#' class='close' onClick='rmMarqueurCourant(\""+ marqueurCourant.nom + marqueurCourant.i +"\","+ marqueurCourant.i +")'>x</a></li>";
		liste +="<ol id='marqueursCourants"+i+"'>";
		$.each(charts, function (j, chart) {
			liste += "<li>"+ chart.title.textStr +" : "+ chart.series[0].yData[marqueurCourant.index] +"</li>";

		});		
		liste +="</ol>"
	});
	$('#listeMarqueursCourants').html(liste);
};

var description = function() {
	var description = getMarqueur($("#listeMarqueurs").val()).desc;
	console.log(description);
	if (description == "")
		description = "Il n'y a pas de description pour ce marqueur";
	$('#desc').html('<div id="description" class="alert-message info"><a class="close" href="#" onClick="$(\'#description\').remove()">x</a><p>'+ description +'</p></div>');
};
