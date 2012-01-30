/** - Raphael Boxplot -
 * Displays boxplots using Raphael.
 * ©Guy Champollion 2011, licensed under LPRAB : http://sam.zoy.org/lprab/
 * based on Hamilton's work under "Hamilton Ulmer I-Don't-Give-A-Damn" license.
 * see the Github repository : https://github.com/hamilton/RaphaelViz
 */

/**
 * Extract datas from the html structure and
 * use the raphael.js library to display boxplots.
 */
function showBoxPlot(){
	var data = {
			'bars' : [],
			'labels' : [],
			"x-axis" : "",
			"y-axis" : ""
	};		
	data['x-axis']=document.getElementById('data').className;
	// Collecter les valeurs à la volée
	var trs=document.getElementById("data").getElementsByTagName("table")[0].getElementsByTagName("tr");
	// On ne veut pas parser les <th>
	for(var i=1;i<trs.length;i++){
		var temparray=new Array();
		var tds = trs[i].getElementsByTagName('td');
		for(var j=0;j<tds.length;j++){
			temparray.push(parseFloat(tds[j].innerHTML));
		}
		data['bars'].push(temparray);
		data['labels'].push(trs[i].className);
	}
	// The Div id is the first param, the data object the second.
	boxplot("first", data);
	
	// Cacher le div source
	document.getElementById('data').style.display='none';
}

/**
 * « can't remember where I got this function ... »
 */
function rnd_bmt() {
    var x = 0, y = 0, rds, c;

    // Get two random numbers from -1 to 1.
    // If the radius is zero or greater than 1, throw them out and pick two new ones
    // Rejection sampling throws away about 20% of the pairs.
    do {
		x = Math.random()*2-1;
		y = Math.random()*2-1;
		rds = x*x + y*y;
    }
    while (rds == 0 || rds > 1)

    // This magic is the Box-Muller Transform
    c = Math.sqrt(-2*Math.log(rds)/rds);

    // It always creates a pair of numbers. I'll return them in an array.
    // This function is quite efficient so don't be afraid to throw one away if you don't need both.
    return x*c;
}

Array.max = function( array ){
    return Math.max.apply( Math, array );
};

Array.min = function( array ){
    return Math.min.apply( Math, array );
};

function left_axis(paper) {
	// Currently only supports numeric values.

	if (paper.axes.left.type == "numeric") {

	} else {
	//
	}

	left_margin = paper.left_margin;
	max_value = paper.axes.left.max_value;
	min_value = paper.axes.left.min_value;
	top_tick = paper.axes.left.top_tick;
	bottom_tick = paper.axes.left.bottom_tick;
	y_lab = paper.axes.left.main_label;
	tick_count = paper.axes.left.tick_count;
	y_axis_x = 3*left_margin / 4;

	paper.path("M" + y_axis_x + " " + top_tick + "L" + y_axis_x + " " + bottom_tick);

	for (var i = 0; i < tick_count; i++) {
		actual_value = max_value - i * (max_value - min_value)/(tick_count-1);
		tick_position = top_tick + i * (bottom_tick - top_tick)/(tick_count-1);
		paper.path("M" + (3*y_axis_x/4) + " " + tick_position +
		"L" + y_axis_x + " " + tick_position);
		// Here's a deeply idiotic hack. Javascript is an odd duck.
		if (actual_value != 0) {
			actual_value = Math.round(actual_value*10)/10.0;
		} else {
			actual_value = "0";
		}
		paper.text((2*y_axis_x/4), tick_position, actual_value);
	}
	paper.text(y_axis_x/4, paper.top_margin + (bottom_tick - top_tick)/2, y_lab).rotate(90);
}

function nicenum(x, round) {
	var exp;
	var f;
	var nf;
	var expdem;
	exp = Math.floor(Math.log(x) / Math.LN10);
	// exponentiation hack. Ugh.
	expdem = 1;
	for (var i=0; i < exp; i++) {
		expdem *= 10;
	}
	f = x / expdem;
		if (round) {
		if (f < 1.5) nf = 1;
		else if (f < 3) nf = 2;
		else if (f < 7) nf = 5;
		else nf = 10;
	} else {
		if (f <= 1) nf = 1;
		else if (f <= 2) nf = 2;
		else if (f <= 5) nf = 5;
		else nf = 10;
	}
	return nf * expdem;
}

function exp_left_axis(paper) {
	// Currently only supports numeric values.

	if (paper.axes.left.type == "numeric") {

	} else {
	//
	}

	left_margin = paper.left_margin;
	max_value = paper.axes.left.max_value;
	min_value = paper.axes.left.min_value;

	top_tick = paper.axes.left.top_tick;
	bottom_tick = paper.axes.left.bottom_tick;

	y_lab = paper.axes.left.main_label;
	tick_count = paper.axes.left.tick_count;
	y_axis_x = 3*left_margin / 4;

	var nfrac;
	var d;
	var graphmin, graphmax;
	var rage;

	range = nicenum(max_value - min_value, false);
	d = nicenum(range / (tick_count-1), true);
	graphmin = Math.floor(min_value / d)*d;
	graphmax = Math.ceil(max_value / d)*d;
	nfrac = Math.max(- Math.floor(Math.log(d)/Math.LN10), 0);

	paper.path("M" + y_axis_x + " " + top_tick + "L" + y_axis_x + " " + bottom_tick);


}

function pt_canvas(pt, side, paper){
	var maxval;
	var minval;
	var distance_from_margin;
	var body_value;
	var canvas_max;
	var canvas;

	maxval = paper.axes[side].max_value;
	minval = paper.axes[side].min_value;
	top_tick = paper.axes[side].top_tick;
	bottom_tick = paper.axes[side].bottom_tick;

	if (side == "left") {
		body_value = paper.body_height - paper.top_margin - paper.bottom_margin;
		canvas_max = paper.body_height - paper.top_margin;
		//this_marg = paper.top_margin;
	} else if (side == "bottom") {
		body_value = paper.body_width - paper.left_margin * 2;
		canvas_max = paper.body_width - paper.left_margin;
		//this_marg = paper.left_margin;
	}
	distance_from_margin = body_value * (pt - minval) / (maxval - minval);
	canvas = canvas_max - distance_from_margin;
	console.log(pt + " " + canvas);
	return canvas;
}

function canvas_pt(canvas, side, paper){
	var top_tick = paper.axes[side].top_tick;
	var bottom_tick = paper.axes[side].bottom_tick;
	var max_value = paper.axes[side].max_value;
	var min_value = paper.axes[side].min_value;
	var distance_from_minval;
	var value_max;
	var pt;

	var distance = (canvas - top_tick) / (bottom_tick - top_tick);
	pt = distance * (max_value - min_value) + min_value;
	return pt;
}

function axis_test(target_div) {

	var paper = canvas_setup(target_div);

	var canvas_test;
	var pt_test;

	paper.axes.left.top_tick = 90;
	paper.axes.left.bottom_tick = 350;
	paper.axes.left.max_value = 90;
	paper.axes.left.min_value = 30;
	//paper.axes.left.main_label = "frequency";
	paper.axes.left.tick_count = 5;
	left_axis(paper);

	canvas_test = pt_canvas(60, "left", paper);
	//console.log(canvas_test);
	paper.text(100, canvas_test, "got em");

	pt_test = canvas_pt(215, "left", paper);
	paper.text(200, 215, pt_test);
	paper.text(300, 215, "Success!");
}

function bottom_axis(paper) {

	//var left_margin = paper.left_margin;
	//var bar_margin = paper.bar_margin;
	// Currently only supports categorical values.

	/* This should work just like left_axis. */
	/* */
	var is_numeric = false;
	//alert(paper.axes.bottom.type);
	if (paper.axes.bottom.type == "numeric") {
		is_numeric = true;
	}

	var body_width = paper.body_width;
	var body_height = paper.body_height;
	var bottom_margin = paper.bottom_margin;
	var left_tick = paper.axes.bottom.left_tick;
	var right_tick = paper.axes.bottom.right_tick;
	var max_value = paper.axes.bottom.max_value;
	var min_value = paper.axes.bottom.min_value;
	var tick_count = paper.axes.bottom.tick_count-1;
	var labels = paper.axes.bottom.labels;
	var main_label = paper.axes.bottom.main_label;

	var x_axis_y = body_height - bottom_margin + bottom_margin/5;
	var tick_bottom = x_axis_y + bottom_margin / 5;

	var this_lab;

	paper.path("M" + left_tick + " " + x_axis_y +
	"L" + right_tick + " " + x_axis_y);

	for (var i = 0; i <= tick_count; i++) {
		x_value = left_tick + i*(right_tick - left_tick)/(tick_count);
		actual_value = min_value + i*(max_value - min_value)/(tick_count);
		paper.path("M" + x_value + " " + x_axis_y + "L" + x_value + " " + tick_bottom);
		if (is_numeric == true) {
			this_lab = Math.round(actual_value*100)/100.0;
		} else {
			this_lab = labels[i];
		}
		// Here's a deeply idiotic hack. Javascript is an odd duck.
		if (Math.actual_value != 0) {
			actual_value = Math.round(actual_value*100)/100.0;
		} else {
			actual_value = "0";
		}
		paper.text(x_value, tick_bottom + (body_height-tick_bottom)/5, this_lab);
	}
	paper.text(left_margin + (body_width - left_tick)/2, body_height - bottom_margin/5, main_label);
}

function stat_histogram(values) {
	// Computes bins for values by the Sturges rule.
	//var l2e = Math.LOG2E;

	var inner = Math.log(values.length) / Math.LN2;
	var no_bins = Math.ceil(inner + 1);
	var maxval = Array.max(values);
	var minval = Array.min(values);
	var markers = new Array(no_bins+1);
	var bins = new Array(no_bins);
	var steps = (maxval - minval)/(no_bins + 1);
	var pt;
	var i;
	var j;
	var this_marker;
	// Below: 6 markers, 5 bins.
	// __________________
	// | | | | | |

	// create bin ticks.
	for (i = 0; i < markers.length; i++) {
		markers[i] = minval + i * steps;
	}

	for (i = 0; i < bins.length; i++) {
		bins[i] = 0;
	}
	this_marker = 0;
	for (i = 0; i < values.length; i++) {
		pt = values[i];
		for (j = 0; j <= markers.length; j++) {
			if (pt >= markers[j]) {
				this_marker = j-1;
			}
		}
		if (pt == markers[0]) {
			this_marker = 0;
		}
		bins[this_marker]++;
	}
	var hist = {
		markers : markers,
		bins : bins,
	}
	return hist;
}

function canvas_setup(target_div){

	var WIDTH = 500, HEIGHT = 400, BAR_MARGIN = 20,
	BOTTOM_MARGIN = 70, TOP_MARGIN = 40, LEFT_MARGIN = 90;

	var paper = Raphael(document.getElementById(target_div), WIDTH, HEIGHT);
	paper.body_width = WIDTH;
	paper.body_height = HEIGHT;
	paper.left_margin = LEFT_MARGIN;
	paper.top_margin = TOP_MARGIN;
	paper.bottom_margin = BOTTOM_MARGIN;
	paper.axes = {};
	paper.axes.bottom = {};
	paper.axes.left = {};
	paper.axes.top = {};
	paper.axes.right = {};
	return paper;
}

function histogram(target_div, data) {
	d = data.values;
	y_lab = "frequency";
	x_lab = data['x-axis'];

	var paper = canvas_setup(target_div);

	var i;

	paper.hist = stat_histogram(d);
	var maxval = Array.max(paper.hist.bins);
	var minval = 0;

	paper.scale = (paper.body_height - paper.top_margin - paper.bottom_margin) / (maxval);

	paper.setter = paper.body_height - paper.bottom_margin;

	top_tick = paper.setter - (maxval - minval) * paper.scale;
	bottom_tick = paper.setter - (minval - minval) * paper.scale;
	////// AXES ///////
	// TODO: add some control flow here if user does not
	// want axes.


	paper.axes.left.top_tick = paper.setter - (maxval) * paper.scale;
	paper.axes.left.bottom_tick = paper.setter - (0) * paper.scale;
	paper.axes.left.max_value = maxval;
	paper.axes.left.min_value = minval;
	paper.axes.left.top_tick = top_tick;
	paper.axes.left.bottom_tick = bottom_tick;
	paper.axes.left.main_label = "frequency";
	paper.axes.left.tick_count = 5;
	left_axis(paper);

	var bincount = paper.hist.bins.length;
	var x_start, y_start, y_length;
	var bar_width = (paper.body_width - paper.left_margin) / (bincount);
	for (i = 0; i < bincount; i++) {
		// Calculate x and y start, in pixels.
		x_start = paper.left_margin + i * bar_width;
		y_length = Math.floor((paper.body_height - paper.bottom_margin - paper.top_margin) * (paper.hist.bins[i]/maxval));
		y_start = paper.body_height - paper.bottom_margin - y_length;

		paper.rect(x_start, y_start, bar_width, y_length).attr({
			fill:"lightgray",
			stroke:"Gray"
		});
	}

	paper.axes.bottom.max_value = Array.max(paper.hist.markers);
	paper.axes.bottom.min_value = Array.min(paper.hist.markers);
	paper.axes.bottom.left_tick = paper.left_margin;
	paper.axes.bottom.right_tick = paper.body_width - paper.left_margin/2;
	paper.axes.bottom.tick_count = 5;
	paper.axes.bottom.type = "numeric";
	paper.axes.bottom.main_label = x_lab;
	bottom_axis(paper);

}

function boxplot(target_div, data) {

	d = data.bars;
	labels = data.labels;
	x_lab = data['x-axis'];
	y_lab = data['y-axis'];

	// set up iteration indices.
	var i;
	var j;

	var WIDTH = 600, HEIGHT = 400, BAR_MARGIN = 20,
	BOTTOM_MARGIN = 70, TOP_MARGIN = 40, LEFT_MARGIN = 90;
	var paper = Raphael(document.getElementById(target_div), WIDTH, HEIGHT);

	paper.axes = {};

	// Load paper with all the parameters of interest.
	paper.body_width = WIDTH;
	paper.body_height = HEIGHT;
	paper.left_margin = LEFT_MARGIN;
	paper.top_margin = TOP_MARGIN;
	paper.bottom_margin = BOTTOM_MARGIN;

	var body_width = WIDTH - LEFT_MARGIN;
	var body_height = HEIGHT - BOTTOM_MARGIN;

	var bars = d.length;
	var width_per_bar = (body_width - (bars) * BAR_MARGIN) / bars;

	paper.barplot = {};
	paper.barplot.width_per_bar = width_per_bar;
	paper.barplot.bar_margin = BAR_MARGIN;
	// 2.) Find maximums and minimums.
	var max_value = d[0][0];
	var min_value = d[0][0];

	for (i = 0; i < d.length; i++) {
		for (j = 0; j < d[i].length; j++) {
			if (d[i][j] > max_value) {
				max_value = d[i][j];
			}
			if (d[i][j] < min_value) {
				min_value = d[i][j];
			}
		}
	}
	//paper.path("M" + 0 + " " + TOP_MARGIN + "L" + 500 + " " + TOP_MARGIN);

	scale = (HEIGHT - TOP_MARGIN - BOTTOM_MARGIN) / (max_value - min_value);
	setter = HEIGHT - BOTTOM_MARGIN;
	paper.scale = scale;
	paper.setter = setter;


	////// AXES ///////
	// TODO: add some control flow here if user does not
	// want axes.

	top_tick = TOP_MARGIN;
	bottom_tick = setter - (min_value - min_value) * scale;

	paper.axes['left'] = {}
	paper.axes.left.top_tick = top_tick;
	paper.axes.left.bottom_tick = bottom_tick;
	paper.axes.left.max_value = max_value;
	paper.axes.left.min_value = min_value;
	paper.axes.left.main_label = y_lab;
	paper.axes.left.tick_count = 5;
	paper.axes.left.type = "numeric";
	paper.axes.left.labels = null;

	left_axis(paper);
	// Take care of the x axis.

	x_axis_y = body_height + BOTTOM_MARGIN/5;//body_height + BOTTOM_MARGIN / 2;
	x_axis_start = LEFT_MARGIN + width_per_bar/2;
	x_axis_end = LEFT_MARGIN + width_per_bar/2 + (labels.length-1)*(BAR_MARGIN + width_per_bar);
	paper.path("M" + x_axis_start + " " + x_axis_y +
	"L" + x_axis_end + " " + x_axis_y);

	paper.axes['bottom'] = {};
	paper.axes.bottom.bottom_y = x_axis_y;
	paper.axes.bottom.left_tick = x_axis_start;
	paper.axes.bottom.right_tick = x_axis_end;
	paper.axes.bottom.labels = labels;
	paper.axes.bottom.type = "categorical";
	paper.axes.bottom.main_label = x_lab;
	paper.axes.bottom.tick_count = labels.length;

	bottom_axis(paper);

	var uppers = paper.set();
	var lowers = paper.set();
	for (i = 0; i < d.length; i++) {

	x = LEFT_MARGIN + i * (width_per_bar + BAR_MARGIN);
	y = TOP_MARGIN;

	vector = d[i];
	max = vector[4];
	upperquart = vector[3];
	median = vector[2];
	lowerquart = vector[1];
	min = vector[0];

	max_trans = setter - (max - min_value) * scale;
	upperquart_trans = setter - (upperquart - min_value) * scale;
	median_trans = setter - (median - min_value) * scale;
	lowerquart_trans = setter - (lowerquart - min_value) * scale;
	min_trans = setter - (min - min_value) * scale;

	//paper.path("M" + 0 + " " + TOP_MARGIN + "L" + 400 + " " + TOP_MARGIN);
	//paper.path("M" + 0 + " " + (HEIGHT - BOTTOM_MARGIN) + "L" + 400 + " " + (HEIGHT - BOTTOM_MARGIN));
	//hovers.push(paper.rect(x - BAR_MARGIN/2, TOP_MARGIN, width_per_bar + BAR_MARGIN, HEIGHT - BOTTOM_MARGIN*2));

	paper.path("M" + x + " " + max_trans + "L" + (x + width_per_bar) + " " + max_trans).attr({"stroke":"Gray"});
	paper.path("M" + (x + width_per_bar/2) + " " + max_trans + "L" + (x + width_per_bar/2) + " " + upperquart_trans).attr({"stroke":"Gray"});
	uppers.push(paper.rect(x, upperquart_trans, width_per_bar, median_trans - upperquart_trans));
	lowers.push(paper.rect(x, median_trans, width_per_bar, lowerquart_trans - median_trans));
	paper.path("M" + (x + width_per_bar/2) + " " + lowerquart_trans + "L" + (x + width_per_bar/2) + " " + min_trans).attr({"stroke":"Gray"});
	paper.path("M" + x + " " + min_trans + "L" + (x + width_per_bar) + " " + min_trans).attr({"stroke":"Gray"});
	}
	//hovers.attr({
	// "fill-opacity" : .2,
	// "stroke" : "#FFF"
	// });
	uppers.attr({
	"fill": "lightgray",
	"stroke": "Gray"
	});
	lowers.attr({
	"fill": "lightgray",
	"stroke": "Gray"
	});

	// hovers.hover(
	// function (event) {
	// this.attr({"fill":"yellow"});
	// //this.attr({"fill":"yellow", "fill-opacity":.2});
	// },
	// function (event) {
	// this.attr({"fill":"white"});
	// });
	return paper;
}
