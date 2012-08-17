var DGraphique = function(screen)
{

	// Graph area
	this.screen = screen;

	// Canvas object, for curves
	this.screenGraph = newDom('canvas');
	this.screenGraph.id = "screenGraph";
	this.canvasGraph = this.screenGraph.getContext('2d');

	// Canvas object, for axes
	this.screenAxes = newDom('canvas');
	this.screenAxes.id = "screenAxes";
	this.canvasAxes = this.screenAxes.getContext('2d');

	this.screen.appendChild(this.screenAxes);
	this.screen.appendChild(this.screenGraph);

	// Area size management
	this.manageSize();
	$(window).resize(this, this.manageSize);

	this.database = {};
	EventBus.addListeners(this.listeners, this);

	this.clear(true);

	this.coef_x = 1.0;
	this.coef_y = 1.0;

	// If the scale have to be repainted
	this.scale_change = true;
};

DGraphique.prototype =
{
// Gestion de la taille du graphe
manageSize: function(obj)
{
	var obj = obj == null ? this : obj.data;
	obj.width = $(obj.screen).width();
	obj.height = $(obj.screen).height();

	obj.screenGraph.width = obj.width;
	obj.screenGraph.height = obj.height;
	obj.screenAxes.width = obj.width;
	obj.screenAxes.height = obj.height;
},

findBounds: function(data, keyX, keyY) {
	var x_min = Number.MAX_VALUE;
	var x_max = -Number.MAX_VALUE;
	var y_min = Number.MAX_VALUE;
	var y_max = -Number.MAX_VALUE;

	for (var i = 0; i < data.length; ++i) {
		var t = data[i];
		if (t[keyX] < x_min) x_min = t[keyX];
		if (t[keyX] > x_max) x_max = t[keyX];
		if (t[keyY] < y_min) y_min = t[keyY];
		if (t[keyY] > y_max) y_max = t[keyY];
	}

	return {
		x_min: x_min,
		x_max: x_max,
		y_min: y_min,
		y_max: y_max
	};
},

paintLine: function(data, keyX, keyY, color)
{

	var c = this.canvasGraph;

	if (data.length == 0) return;
	var first_point = data[0];

	var b = this.findBounds(data, keyX, keyY);

	var size_x = b.x_max - b.x_min;
	var tmp_x = this.quantize_tics(size_x);
	if (tmp_x > this.tic_x)
		this.tic_x = tmp_x;

	var size_y = b.y_max - b.y_min;
	var tmp_y = this.quantize_tics(size_y);
	if (tmp_y > this.tic_y)
		this.tic_y = tmp_y;

	// size_x = Math.ceil(size_x / tmp_x) * tmp_x;
	size_y = Math.ceil(size_y / tmp_y) * tmp_y;
	if (size_x == 0)
	    size_x = 1.0;
	else
	    var new_coef_x = this.width / size_x;

	if (size_y == 0)
	    size_y = 1.0;
	else
	    this.coef_y = this.height / size_y;


	if (
		new_coef_x < this.coef_x ||
		Math.abs((this.coef_x - new_coef_x) /  new_coef_x) > 0.05)
		this.coef_x = this.width / size_x;


	var max_x_by_point = (this.width / data.length) * 10.0;

	c.beginPath();
	c.strokeStyle = color;
	c.lineWidth = 2.5;
	// c.shadowBlur = 3;
	// c.shadowColor = "grey";
	// c.shadowOffsetX = 1;
	// c.shadowOffsetY = 1;

	var x_i = 0;
	var	y_i = this.height - (first_point[keyY] - b.y_min) * this.coef_y;
	c.moveTo(x_i,y_i);

	// Pour chaque point à afficher
	for (var i = 0; i < data.length; ++i)
	{
		var old_x_i = x_i;
		x_i = (data[i][keyX] - b.x_min) * this.coef_x;

		var diff = x_i - old_x_i;

		y_i = this.height - (data[i][keyY] - b.y_min) * this.coef_y;

		if (diff > max_x_by_point)
			c.moveTo(x_i,y_i);
		else
			c.lineTo(x_i, y_i);
		// console.log(x_i);
	}

	c.stroke();
	c.closePath();
},

paintAxes: function(mili, paintForced)
{

	if (!paintForced &&
		this.old_tic_x === this.tic_x && this.old_tic_y === this.tic_y)
		return;

	var c = this.canvasAxes;
	c.clearRect(0,0, this.width, this.height);

	c.strokeStyle = "#505050";
	c.lineWidth = 1;

	var x_val = this.tic_x * this.coef_x;
	var y_val = this.tic_y * this.coef_y;
	var x_tic = Math.round(x_val);
	var y_tic = Math.round(y_val);

	if (x_tic <= 0.0) x_tic = 1.0;
	if (y_tic <= 0.0) y_tic = 1.0;

	// console.log(x_tic, y_tic);
	if(x_tic === 1 || y_tic == 1)
		return;

	// Vertical lines
	for(var i = 0.5; i < this.width ; i += x_tic){
		c.beginPath();
		c.moveTo(i , this.height);
		c.lineTo(i, 0);
		c.stroke();
		c.closePath();

		if (mili)
		{
			c.save();
			c.beginPath();
			c.strokeStyle = '#404040';

			var incr = (x_val / 10.0);
			for (var j = i + incr; j <= i + x_val; j += incr)
			{
				j_t = Math.round(j) + 0.5;
				c.moveTo(j_t, this.height);
				c.lineTo(j_t, 0);
			}

			c.stroke();
			c.closePath();
			c.restore();
		}

	}

	// Horizontal lines
	for(var i = 0.5; i < this.height ; i += y_tic){
		c.beginPath();
		c.moveTo(0, i);
		c.lineTo(this.width, i);
		c.stroke();
		c.closePath();

		if (mili)
		{
			c.save();
			c.beginPath();
			c.strokeStyle = '#404040';

			var incr = (y_val / 10.0);
			for (var j = i + incr; j <= i + y_val; j += incr)
			{
				j_t = Math.round(j) + 0.5;
				c.moveTo(0, j_t);
				c.lineTo(this.width, j_t);
			}

			c.stroke();
			c.closePath();
			c.restore();
		}
	}

},

clear: function(noClearCanvas) {

	this.old_tic_x = this.tic_x;
	this.old_tic_y = this.tic_y;
	this.tic_x = -1;
	this.tic_y = -1;

	// On efface toute l'ancienne zone
	if (!noClearCanvas)
		this.canvasGraph.clearRect(0,0, this.width, this.height);

},

/*
 *	Number of tics (step in graph)
 *
 *	This function is inspired by the gnuplot treatment
 *	You can find the original algorithm in quantize_normal_tics
 *	function from gnuplot axis.c file
 */
quantize_tics: function(max)
{
	var magnitude = Math.pow(10.0, Math.floor(
		Math.log(max) * 0.43429448190325 )); // log10

	var nb_decades = max / magnitude;

	var keys = [0.5, 1.0, 2.0, 5.0, 10.0, 40.0];
	var values = [0.05, 0.1, 0.2, 0.5, 1, 2];

	for (var i = 0; i < 6; ++i)
		if (nb_decades < keys[i])
			return values[i] * magnitude;

	var ret = magnitude * Math.ceil(nb_decades);

	return isNaN(ret) ? 1.0 : ret;
},

listeners: {
	/*bounds: function(d, obj) {
		var min_time = Number.MAX_VALUE;
		var max_time = -Number.MAX_VALUE;

		for (var local_statement in obj.database)
			if (local_statement in d)
			{
				if (d[local_statement].time_tMax > max_time)
					max_time = d[local_statement].time_tMax;
				if (d[local_statement].time_tMin < min_time)
					min_time = d[local_statement].time_tMin;
			}

		obj.intervalle = max_time - min_time;
	},*/

	tuples: function(detail, obj) {
		obj.clear();
		var colors = ['blue', 'purple', 'red', 'yellowgreen','white'];
		for (var statement_name in detail)
		{
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];
			if (data.length === 0) continue;

			for (var k in data[0])
				if (k != 'time_t')
					obj.paintLine(data, 'time_t', k, colors.pop());
		}

		obj.paintAxes(true, false);
	},
	add_statement: function(e, obj) {
		if (e.box_name != self.name) return;

		if (!(e.statement_name in obj.database))
			obj.database[e.statement_name] = true;
	},
	del_statement: function(e, obj) {
		if (e.box_name != self.name) return;

		if (e.statement_name in obj.database)
			delete obj.database[e.statement_name];
	}
}};
