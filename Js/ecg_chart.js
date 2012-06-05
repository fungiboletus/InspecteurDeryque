var chart;
var data0 = [], data1 = [], data2= [], data3= [], data4= [], data5= [], data6= [], data7= [], data8= [], data9= [], data10= [], data11 = [], time = (new Date()).getTime(), i;

for(var i = -999; i <= 0; i++) {
	data0.push([
	            time + i * 1000,
	            null
	            ]);
	data1.push([
	            time + i * 1000,
	            Math.round(Math.random() * 100)
	            ]);
	data2.push([
	            time + i * 1000,
	            Math.round(Math.random() * 100)
	            ]);
	data3.push([
	            time + i * 1000,
	            Math.round(Math.random() * 100)
	            ]);
	data4.push([
	            time + i * 1000,
	            Math.round(Math.random() * 100)
	            ]);
	data5.push([
	            time + i * 1000,
	            Math.round(Math.random() * 100)
	            ]);
	data6.push([
	            time + i * 1000,
	            Math.round(Math.random() * 100)
	            ]);
	data7.push([
	            time + i * 1000,
	            Math.round(Math.random() * 100)
	            ]);
	data8.push([
	            time + i * 1000,
	            Math.round(Math.random() * 100)
	            ]);

}

$(document).ready(function() {
	idWhere = 'container';	
	title = 'Random';

	Highcharts.setOptions({
		global : {
			useUTC : false
		}
	});

	chart = new Highcharts.StockChart({
		chart : {
			renderTo : idWhere,
			zoomType : "x",
		},

		rangeSelector: {
			buttons: [{
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
		},

		title : {
			text : title
		},
		legend: {
			enabled: true,
			align: 'right',
			layout: 'vertical',
			verticalAlign: 'middle',
		},
		exporting: {
			enabled: false
		},
		yAxis : {
			title : {
				text : 'Power'
			},
//			plotLines : [{
//			value : 0.6738,
//			id : max,
//			color : 'green',
//			dashStyle : 'shortdash',
//			width : 2,
//			label : {
//			text : 'Max'
//			}
//			}
		},
		navigator : {
			baseSeries : 1
		},
		plotOptions: {
			series : {
				cursor: 'pointer',
				point : {
					events: {
						click: function() {
							chart.xAxis[0].addPlotLine({
								value: this.x,
								color: 'red',
								width: 2,
								id: 'mark'
							});	
						}
					}
				}
			}
		},
		series : [          
		          {
		        	  name : 'Pause',
		        	  id : 'serie0',
		        	  showInLegend: true,
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]
		          },     
		          {
		        	  name : 'serie1',
		        	  id : 'serie1',
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]
		          },
		          {
		        	  name : 'serie2',
		        	  id : 'serie2',
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]

		          },
		          {
		        	  name : 'serie3',
		        	  id : 'serie3',
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]

		          },
		          {
		        	  name : 'serie4',
		        	  id : 'serie4',
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]

		          },
		          {
		        	  name : 'serie5',
		        	  id : 'serie5',
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]

		          },
		          {
		        	  name : 'serie6',
		        	  id : 'serie6',
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]

		          },
		          {
		        	  name : 'serie7',
		        	  id : 'serie7',
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]

		          },
		          {
		        	  name : 'serie8',
		        	  id : 'serie8',
		        	  type : 'line',
		        	  data : [[(new Date()).getTime() - 1000 * 1000, null]]

		          },
		          //FLAGS
		          {
		        	  id: "flags",
		        	  name: "flagflag",
		        	  type: "flags",
		        	  showInLegend: false,
		        	  data: []
		          }
		          ]
	});

	

	requestData(0);
});

function addFlag() {
	var ser = chart.get("flags");
//	var serie = chart.get("serie1");
	ser.addPoint({
		x : (new Date()).getTime() - 999 * 1000
	}); 
	$('#infos').html("Pouet"+ ser.xData);
};

function addLine() {
	chart.yAxis[0].removePlotLine('max');
	chart.yAxis[0].addPlotLine({
		value: (function() {
			var j=0;
			var x = chart.series[1].yData;
			for(i=0;i<x.length;i++) {
				if(x[i]>x[j])
					j=i;
			}

			return x[j];
		})(),
		color: 'rgb(0, 0, 0)',
		width: 3,
		id: 'max'
	});	
}

function addSerie(nom, data) {
	chart.addSeries({
		name : nom,
		id : nom,
		data : data
	});
};

//function rmSerie(nom) {
//chart.get(nom).remove();
//}

function stop() {
	chart.get('serie0').remove();
}

function addMark() {
	chart.xAxis[0].removePlotLine('mark');
	chart.xAxis[0].addPlotLine({
		value: (function() {
			var j=0;
			var x = chart.series[1].xData;
			for(i=0;i<x.length;i++) {
				if(x[i]>x[j])
					j=i;
			}

			return x[j];
		})(),
		color: 'red',
		width: 2,
		id: 'mark'
	});	
}