// SOUS "CLASSE" DE MINICHART
//Need charts, maxLine, minLine
var dataCharts = [];	// Tableaux de donn�es pour les graphes, utilis� dans computeData principalement
var maxLine = "";		// obvious
var minLine = "";		// �
var picsMaxCharts = []; //Graphes des pics max
var picsMinCharts = []; // Graphes des pics min
var dPicsMin = new Array();	//Tableau d'intervalles pour les min
var dPicsMax = new Array();	// pour les max
var starto = 0;
var endo = 0; // La taille du tableau lors de la définition du seuil. 

function initPics() {
	if(maxLine == "" && minLine == "") {
		$("#pics").before('<div id="description" class="alert-message info">Pour pouvoir afficher des pics, il faut utiliser le module de d&eacutetection de pics disponible dans la visualisation Graphe RT.</div>');
	} else {
		if (minLine != "")
			recupererPicsMin(starto, endo, minLine);
		if (maxLine != "")
			recupererPicsMax(starto, endo, maxLine);
	}
};


var recupererPicsMin = function (startView, endView, minL) {
	dPicsMin = new Array();
	console.log("Min Line :"+ minL);
	// Pour chaque graphe
	$.each(dataCharts, function (i, data) {
		var dChart = new Array();
		// Pour chaque section < min
		for (var j = startView; j < endView; j++) {
			if (data[j].y <= minL && data[j].y != null) {
				var min = j, debut = j, fin = j, start = 0, end = 0;
				var dChart1 = new Array();
				// On cherche le min, et on prendra un intervalle dont ce min est le milieu
				console.log('j ='+ j +'data.length = '+ data.length +' data[j].y = '+ data[j].y);
				while(j < endView && data[j].y <= minL) {
					console.log('< y : '+ data[j].y);
					if (data[j].y < data[min].y) {
						min = j;
					}
					j++;
				}
				fin = j;
				console.log("min = "+ min +" debut = "+ debut +" fin = "+ fin);
				// Si l'intervalle est trop grand.. on fixe
				if ((fin - debut) > 10) {
					start = min - 7;
					end = min + 7;
				} else {
					start = debut - 5;
					end = fin + 5;
				}
				// On prend l'intervalle
				dChart1.min = data[Math.max(1, start)].x;
				dChart1.max = data[Math.min(end, data.length - 1)].x;
				//Structure .min/.max � enregistrer pour chaque pic
				console.log(dChart1.min +", "+ dChart1.max);

				dChart.push(dChart1);
			}
		}
		dPicsMin.push(dChart);
	});
	showPicsCharts(picsMinCharts, "picsMin", "Pics minimum", dPicsMin);
};

var recupererPicsMax = function (startView, endView, maxL) {
	dPicsMax = new Array();
	$.each(dataCharts, function (i, data) {
		var dChart = new Array();
		for (var j = startView; j < endView; j++) {
			if (data[j].y >= maxL && data[j].y != null) {
				var max = j, debut = j, fin = j;
				var dChart1 = new Array();
				// On cherche le min, et on prendra un intervalle dont ce min est le milieu
				while(j < endView && data[j].y >= maxL) {
					console.log('> y : '+ data[j].y);
					if (data[j].y > data[max].y) {
						max = j;
					}
					j++;
				}
				fin = j;
				console.log("max = "+ max +" debut = "+ debut +" fin = "+ fin);
				// Si l'intervalle est trop grand.. on fixe
				if ((fin - debut) > 10) {
					start = max - 7;
					end = max + 7;
				} else {
					start = debut - 5;
					end = fin + 5;
				}
				// On prend l'intervalle
				dChart1.min = data[Math.max(1, start)].x;
				dChart1.max = data[Math.min(end, data.length - 1)].x;
				//Structure .min/.max � enregistrer pour chaque pic
				console.log(dChart1.min +", "+ dChart1.max);

				dChart.push(dChart1);
			}
		}
		dPicsMax.push(dChart);
	});
	showPicsCharts(picsMaxCharts, "picsMax", "Pics Maximum", dPicsMax);
};

var showPicsCharts = function (where, divName, titre, what) {
	$('#'+ divName).empty();
	$('#'+ divName).append("<h2 style='text-align:center'> "+ titre +" </h3");
	$.each(what, function (i, dChart) {
		$('#'+ divName).append("<h3> Graphe "+ dataCharts[i].name +"</h3");
		$.each(dChart, function (j, section) {
			showChart(where, divName, i, j, section, dataCharts[i].name+'-'+j);
			
		});
		$('#'+ divName).append('<br />');
	});
};


var computeData = function(i, section, title) {
	var tmp = [];
	for(var x = 1; x < dataCharts[i].length; x++) {
		if (dataCharts[i][x].x >= section.max)
			return tmp;
		if (dataCharts[i][x].x >= section.min) {
			tmp.push([dataCharts[i][x].x*1000, dataCharts[i][x].y]);
		}
	}
	return tmp;
}
//chargement des données dans js.

function setMinLine(i) {
	minLine = i;
}

function setMaxLine(i) {
	maxLine = i;
}

function setViewLength(min, max) {
	starto = min;
	endo = max;
}
