
function addChart(name, datas, timestamps) {

	var data = new Array();
	var i;

	for(i = 0; i < timestamps.length; i++) {
		var couple = new Array();
		couple.x = timestamps[i];
		couple.y = datas[i];
		data.push(couple);
		data.name = name;
	}
	dataCharts.push(data);
};

//GL HF with this one
/** 
 * Affiche un miniChart
 * @param {Object} where Dans quel tableau ajouter le graphe
 * @param {Object} divName Dans quel container
 * @param {Object} i Utilisé pour retrouver l'indice du tableau du pics, mais aussi sa ligne dans la grille de divs
 * @param {Object} j Sa colonne dans la grille de divs
 * @param {Object} tab
 */
var showChart = function(where, divName, i, j, section, title) {
	$('#'+ divName).append('<div id="chartBloc"><a class="close" href="#" onClick="rmChart(\''+ divName +'\', '+i+', '+ j +')">x</a><div id="'+divName+''+i+'-'+j+'" style="margin:20px;"></div></div>');
	where.push(
			new Highcharts.StockChart({
				chart : {
					renderTo : divName+''+i+'-'+j,
					height:300,
					width:150
				},
				title : {
					text : title
				},
				navigator : {
					enabled : false
				},
				rangeSelector : {
					enabled : false
				},
				series : [{
					name : 'data',
					data : computeData(i, section, title)
				}],
				exporting : {
					enabled : false
				}
			}));
}

function rmChart(choice, i, j) {
	if (choice == 'picsMin') {
		$('#picsMin'+i+'-'+j).parent().hide();
	} else if (choice == 'picsMax') {
		$('#picsMax'+i+'-'+j).parent().hide();
	} else if (choice == 'composition') {
		composition[i].destroy();
		composition.splice(i,1);
		$('#composition'+i+'-'+j).parent().hide();
	}
};
