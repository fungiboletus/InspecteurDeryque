var compoOrSelect = '';
var listeCompo = []; // Liste des intervalles pour la compo
/* ----------------------------------------------- onClick() ------------------------------------------------- */

/*
 *  select : Initie les popup de selection et composition
 */
var selectMarqueurs = function() {
	var listeGraphes = "";
	var listeMarqueursCourants = "";

	$('#graphe_select_marqueurs').empty();
	$('#de_select_marqueurs').empty();
	$('#a_select_marqueurs').empty();
	
	$.each(charts, function (j, chart) {
		if (chart != null && chart.title != null) {
			listeGraphes += "<option value='"+ chart.title.textStr +"'>"+chart.title.textStr+"</option>";
		}
	});
	$.each(marqueursCourants, function (j, marqueurCourant) {
		listeMarqueursCourants += "<option value='"+ marqueurCourant.x +"'>"+ marqueurCourant.nom +" &agrave; "+ marqueurCourant.x +"</option>";
	});
	$('#graphe_select_marqueurs').html(listeGraphes);
	$('#de_select_marqueurs').html(listeMarqueursCourants);
	$('#a_select_marqueurs').html(listeMarqueursCourants);
	
};

var selectDates = function() {
	var listeGraphes = "";
	var listeDates = "";

	$('#graphe_select_dates').empty();
	$('#de_select_dates').empty();
	$('#a_select_dates').empty();
	
	$.each(charts, function (j, chart) {
		if (chart != null && chart.title != null) {
			listeGraphes += "<option value='"+ chart.title.textStr +"'>"+chart.title.textStr+"</option>";
		}
	});
	for (var i = 0; i < charts.length; i++) {
		if (charts[i] != null && charts[i].series != null) {
			$.each(charts[i].series[0].xData, function (j, timestamp) {
				listeDates += "<option value='"+ timestamp +"'>"+timestamp +" ms : "+ charts[i].series[0].yData[j] +" mV</option>";
			});
			break;
		}
	}
	$('#graphe_select_dates').html(listeGraphes);
	$('#de_select_dates').html(listeDates);
	$('#a_select_dates').html(listeDates);
};

var selectSelection = function(graphe, event) {
	if (event.xAxis) {
		//alert('Rï¿½cupï¿½rer '+ event.xAxis[0].min +' et '+event.xAxis[0].max +' dans getChartConfig (multiple_charts.js) ainsi que l\'id du relevï¿½');
		// La selection sur graphe n'est possible que si zoomType est activï¿½, provoquant un zoom. Il faut alors dï¿½zoomer :/
		$('#graphe_select_selection').html(graphe.title.textStr);
		$('#de_select_selection').html(event.xAxis[0].min);
		$('#a_select_selection').html(event.xAxis[0].max);
		$('#popup_select_selection').modal('show');
		setTimeout("Dezoom()", 100);
	}
};

/*
 * ok : Actions à faire lorsque l'on valide les popup
 */
var okMarqueurs = function() {
	var graphe = $('#graphe_select_marqueurs').val();
	var de = $('#de_select_marqueurs').val();
	var a = $('#a_select_marqueurs').val();
	
	console.log('Marqueurs '+ graphe +' '+ de +' '+ a);
	
	if (compoOrSelect == 'select')
		saveSelection(graphe,de,a);
	else
		addToCompo(graphe, de, a);
		
}
var okDates = function() {
	var graphe = $('#graphe_select_dates').val();
	var de = $('#de_select_dates').val();
	var a = $('#a_select_dates').val();

	console.log('Dates '+ graphe +' '+ de +' '+ a);
	
	if (compoOrSelect == 'select')
		saveSelection(graphe,de,a);
	else
		addToCompo(graphe, de, a);
	
};
var okSelection = function() {
	var graphe = $('#graphe_select_selection').text();
	var de = $('#de_select_selection').text();
	var a = $('#a_select_selection').text();

	console.log('Selection '+ graphe +' '+ de +' '+ a);
	
	if (compoOrSelect == 'select')
		saveSelection(graphe,de,a);
	else
		addToCompo(graphe, de, a);
	
};

function printListCompo(where) {
	var liste = "<ul>";
	$.each(listeCompo, function (i, e) {
		liste +="<li>"+ e.graphe +" de "+ e.debut +" &agrave; "+ e.fin +"<a href='#' class='close' onClick='rmCompoIntervalle("+ i +")'>x</a></li>";
	});
	liste += "</ul>";
	where.html(liste);	
};

function rmCompoIntervalle(i) {
	listeCompo.slice(i, 1);
	printListCompo($('#listeCompo'));
};

function addToCompo(graphe, de, a) {
	var elem = [];
	elem.graphe = graphe;
	elem.debut = de;
	elem.fin = a;
	listeCompo.push(elem);
	printListCompo($('#listeCompo'));
};

/*
 * Sauvegarde
 */
function saveSelection(graphName,begin,end) {
	if (graphName != null && begin != null && end != null)
		$.get(document.URL, { 'graphName' : graphName , 'selectionBegin' : begin, 'selectionEnd' : end } );
};

function saveComposition(nom) {
	if (nom != null && listeCompo.length > 0) {
		var grapheNames = [];
		var debuts = [];
		var fins = [];
		
		for(var i = 0; i < listeCompo.length; i++) {
			grapheNames[i] = listeCompo[i].graphe;
			debuts[i] = listeCompo[i].debut;
			fins[i] = listeCompo[i].fin;
		}
				
		$.get(document.URL, { 'cname' : nom , 'snames' : grapheNames.join(',') , 'sdebuts' : debuts.join(',') , 'sfins' : fins.join(',') } );
		listeCompo = new Array();
		printListCompo($('#listeCompo'));
		$('#listeCompo').html("<p class='alert-message info' style='margin-left: 25px'> Composition enregistr&eacute;e </p>");
	} else {
		$('#infosAction').html("<p class='alert-message info' style='margin-left: 25px'> S&eacute;lectionnez des intervalles avant de composer </p>");
	}
};