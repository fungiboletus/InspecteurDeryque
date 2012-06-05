var compositions = [];
var dataCharts = [];

var graphs = [];

var addComposition = function(cname) {

	compo = new Object();

	compo.name = cname;

	compo.selections = [];

	compositions.push(compo);
}
/**
 * Ajoute une sélection à la composition donnée.
 * @param {Object} cname Le nom de la composition
 * @param {Object} graphName Le nom du graphe duquel est issu la sélection
 * @param {Object} debut
 * @param {Object} fin
 */
var addSelectionToComposition = function(cname, graphName, debut, fin) {
	var selection = new Object();
	var section = new Object();
	section.min = debut;
	section.max = fin;
	selection.graph = graphName;
	selection.section = section;

	for(var i = 0; i < compositions.length; i++) {
		if(compositions[i].name == cname) {
			compositions[i].selections.push(selection);
		}
	}
}
/**
 * Affiche un miniChart
 * @param {Object} where Dans quel tableau ajouter le graphe
 * @param {Object} divName Dans quel container
 * @param {Object} i Ligne dans la grille de divs
 * @param {Object} tab
 */
var showBigChart = function(where, divName, i, selections, title) {
	$('#' + divName).append('<div id="chartBloc"><a class="close" href="#" onClick="rmChart(\'' + divName + '\', ' + i + ')">x</a><div id="' + divName + '' + i + '" style="margin:20px;"></div></div>');
	where.push(new Highcharts.StockChart({
		chart : {
			renderTo : divName + '' + i,
			height : 300,
			width : 600
		},
		title : {
			text : title
		},
		rangeSelector : {
			buttons : [{
				count : 1,
				type : 'second',
				text : '1s'
			}, {
				count : 10,
				type : 'second',
				text : '10s'
			}, {
				count : 30,
				type : 'second',
				text : '30s'
			}, {
				count : 1,
				type : 'minute',
				text : '1min'
			}, {
				type : 'all',
				text : 'All'
			}],
			inputEnabled : false,
			selected : 0
		},
		series : [{
			name : title,
			data : computeData(i, selections)
		}],
		exporting : {
			enabled : false
		}
	}));

};

var computeData = function(i, selections) {
	var tmp = [];
	var indice = 0;
	for (var i= 0; i < selections.length; i++) {
		for (var y = 0; y < dataCharts.length; y++) {
			if (dataCharts[y].name == selections[i].graph) {
				console.log("found ya");
				indice = y;
				break;
			}
		}
		for(var x = 0; x < dataCharts[indice].length; x++) {
			var leX = dataCharts[indice][x].x*1000;
			if (leX >= selections[i].section.max)
				break;
			if (leX >= selections[i].section.min) {
				// Que des y, les x ne forment pas une suite
				tmp.push(dataCharts[indice][x].y);
			}
		}
	}
	console.log('Bouh : '+ tmp);
	return tmp;	
};

var initCompositions = function() {
	//TODO utiliser compositions ( modifie le remplissage du tableau composition comme tu veux)
	console.log(compositions);
	$.each(compositions, function(i, compo) {
		console.log("Une compo");
		showBigChart(graphs, 'composition', i, compo.selections, compo.name);
	});
}
