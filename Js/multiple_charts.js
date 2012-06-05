var run = true, action = 'marqueur', lastCall = 0, minOrMax = '';
var charts = [];
var dataCharts = [];


//Fonction pour cr�er un graphe facilement
var getChartConfig = function(renderId, title, width, height) {
	var config = {};
	config.chart = {
			renderTo: renderId,
			height:height,
			width:width,
			zoomType : 'x',

			events: {
				selection: function(event) {
					switch (action) {
					case 'zoom' :
						if (event.xAxis) {
							for (var x = 0; x < charts.length; x++) {
								charts[x].xAxis[0].setExtremes(event.xAxis[0].min,event.xAxis[0].max);
							}
						}
						break;
					case 'selectionSelect' :
						selectSelection(this, event);
						break;
					}
				}
			}
	};
	config.rangeSelector = {
			buttons: [{
				count: 1,
				type: 'second',
				text: '1s'
			},
			{
				count: 10,
				type: 'second',
				text: '10s'
			},
			{
				count: 30,
				type: 'second',
				text: '30s'
			}, {
				count: 1,
				type: 'minute',
				text: '1min'
			}, {
				type: 'all',
				text: 'All'
			}],
			inputEnabled: false,
			selected: 0
	};
	config.title = {
			text : title
	};
	config.legend = {
			enabled: false
	};
	config.scrollbar = {
			height: 7
	};
	config.xAxis = {
			dateTimeLabelFormats : '%S',
			title : {
				text : "Temps (ms)"
			}
	};
	config.yAxis = {
			title : {
				text : "Voltage (mV)"
			}
	};
	config.navigator = {
			height : 15
	};
	config.plotOptions = {
			series : {
				cursor: 'pointer',
				events: {
					click: function(event) {
						//Multiple flags
						switch (action) {
						case 'marqueurs' :
							placerMarqueur($('#listeMarqueurs').val(), event.point.x);
							break;
						case 'pics' :
							//Multiple x plot lines
							console.log(this.chart.xAxis[0].getExtremes());
							placerLigne(minOrMax, event.point.y, this.chart.xAxis[0].getExtremes().min, this.chart.xAxis[0].getExtremes().max);
							break;
						}
					}
				}
			}
	};

	config.series =  [{ name: title, data :[[0, null]]}, 
	                  {
		id: "flags",
		name: "flagflag",
		type: "flags",
		showInLegend: false,
		data: []
	                  }
	];

	return config;
};

//Ajouter un graphe avec un tableau de y, et de x
function addChart(name, datas, timestamps) {

	var data = new Array();
	var i;
	for(i = 0; i < timestamps.length; i++) {
		data.push([
		           timestamps[i]*1000,
		           datas[i]
		           ]);
	}

	var idHolder = "holder"+(charts.length);
	//$('#holder').height($('#holder').height()+400);
	$('#holder').append('<a class="close" href="#" onClick="rmChart('+charts.length+');">x</a><div id="'+idHolder+'" style="margin:20px;"></div>');
	charts.push(new Highcharts.StockChart(
			getChartConfig(idHolder, name, 600, 300)
	));
	dataCharts[charts.length - 1] = data;
	inf = "infos"+(charts.length-1);
	$('#infos').append("<li>"+ name +" : <span id="+ inf +"></span></li>");
	requestData(lastCall, charts.length - 1, data);
	console.log("Length : "+data.length);
};

function rmChart(i) {
	charts[i].destroy();
	//charts.splice(i,1);
	$('#holder'+i).prev().hide();
	$('#holder'+i).hide();
};

//Temps r�el
function requestData(i, j, data) {
	// add the point
	if (charts[j].series == null)
		return;
	if (!run) {
		lastCall = i;
		return;
	}
	if (i >= data.length - 1) {
		run = false;
		return;
	}
	var tmp = i;
	while (i < data.length && i < (tmp + 4)) {
		charts[j].series[0].addPoint(data[i], false, false);
		i++;
	}
	//Rafraichissement qu'un sur 5, faster faster !
	if (i < data.length) {
		charts[j].series[0].addPoint(data[i], true, false);
		i++;
	}
	afficheInfos(j, charts[j].series[0].yData[i]);

	setTimeout(function() { if (charts[j]) requestData(i, j, data); }, 10);    
};

//Info en temps r�el � droite
function afficheInfos(i, data) {
	$('#infos'+i).html(data+ "unite");
}
//Action � effectuer, placer des marqueurs ou des pics
function setAction(item) {
	action = item;
	var info = "";
	switch (action) {
	case 'zoom' : 
		info = "S�lectionnez un intervalle sur le graphe � zoomer.";
		break;
	case 'selectionCompo' :
		info="S�lectionnez un intervalle sur le graphe afin de pouvoir le composer avec d'autres.";
		break;
	case 'pics' :
		info="Cliquez sur la courbe l� afin de d�finir un seuil pour d�tecter un ou plusieurs pic(s).";
		break;
	case 'marqueurs' :
		info="Cliquez sur la courbe l� o� vous souhaitez placer un marqueur.";
		break;
	}
	$('#infosAction').html("<p class='alert-message info' style='margin-left: 25px'>"+info+"</p>");
}

function Dezoom() {
	$.each(charts, function (i, chart) {
		chart.xAxis[0].setExtremes(chart.xAxis.min, chart.xAxis.max);
	});
	console.log("Dezoom");
}