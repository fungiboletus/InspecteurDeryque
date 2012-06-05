var minLine = "", maxLine = "";

var placerLigne = function(choix, y, min, max) {
	console.log("Min : "+min+" et max : "+ max);
	var start = 0; 
	var end = 0;
	var color = (choix == 'min') ? 'green' : 'red';
	$.each(charts, function(i, chart) {
		chart.yAxis[0].removePlotLine(choix);
		chart.yAxis[0].addPlotLine({ 
			value: y,
			color: color,
			width: 2,
			id: choix
		});	
	});
	for (var i= 0; i < charts.length; i++) {
		if (charts[i] != null && charts[i].series != null) {
			for (var j = 0; j < charts[i].series[0].xData.length; j++) {
				if ((charts[i].series[0].xData[j] == Math.floor(min)) || (charts[i].series[0].xData[j] == Math.floor(min +1 ))) {
					start = j;
				} else
				if ((charts[i].series[0].xData[j] == Math.floor(max)) || (charts[i].series[0].xData[j] == Math.floor(max +1))){
					end = j;
					break;
				}
			}
			break;
		}
	}
	if (choix == "min") {
		minLine = y;
		//recupererPicsMin();
		$.get(document.URL, { 'minLine' : minLine, 'beginTime' : start,'endTime' : end } );
	} else if (choix == "max") {
		maxLine = y;
		//recupererPicsMax();
		$.get(document.URL, { 'maxLine' : maxLine, 'beginTime' : start,'endTime' : end } );
	}
	printLignes();
};

var rmLigne = function(choix) {
	if (choix == "min") {
		$.each(charts, function(i, chart) {
			chart.yAxis[0].removePlotLine(choix);
		});
		minLine = "";
	} else if (choix == "max") {
		$.each(charts, function(i, chart) {
			chart.yAxis[0].removePlotLine(choix);
		});
		maxLine = "";
	}
	printLignes();
};

function setMinLine(i) {
	minLine = i;
}

function setMaxLine(i) {
	maxLine = i;
}

var printLignes = function() {
	if (minLine != "" || minLine == "0") {
		$('#picMin').html(minLine);
	} else {
		$('#picMin').html('Non d&eacutefini');	
	}
	if (maxLine != "" || maxLine == "0") {
		$('#picMax').html(maxLine);
	} else {
		$('#picMax').html('Non d&eacutefini');	
	}
};
