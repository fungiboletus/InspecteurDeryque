/* This file is released under the CeCILL-B V1 licence.*/

var DLineChart = function(screen)
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

	// Create legend
	this.legend_area = newDom('ul');
	this.legend_area.className = 'legend';
	this.screen.appendChild(this.legend_area);

	// Create time cursor
	this.time_cursor = newDom('div');
	this.time_cursor.className = 'time_cursor';
	this.screen.appendChild(this.time_cursor);

	this.database = {};
	EventBus.addListeners(this.listeners, this);

	this.clear(true);

	this.coef_x = 1.0;
	this.coef_y = 1.0;

	// If the scale have to be repainted
	this.scale_change = true;

	// this.colors = ['white', 'red', 'dodgerblue', 'limegreen', 'yellowgreen', 'orangered', 'salmon', 'cyan'];
	this.colors = ['orange', 'dodgerblue', 'limegreen', 'salmon', 'cyan'];

	// Multiscale or unique scale ?
	// with the multiscale, each statement's scale is adapted to the window
	// but values between 0 and 0.1 could looking similar to values between 1000 and 1000000
	this.multi_scale = false;

	// Print values on y axes ?
	// This is only activated when multi_scale is disabled
	this.show_x_values = true;

	// Just a line or a fill ?
	this.fill_lines = false;

	// Just points ?
	this.points = false;

	// Time value of the cursor
	this.cursor_time_t = 0;

	// Create the legend
	this.createLegend();
};

DLineChart.prototype =
{

createLegend: function() {
	var buttons_area = newDom('div');
	buttons_area.className = 'btn-group';
	var obj = this;
	var btn = newDom('a');
	btn.className = 'btn btn-mini';
	btn.appendChild(document.createTextNode('Multi scale'));
	btn.onclick = function(){
		obj.multi_scale = !obj.multi_scale;
		this.className = obj.multi_scale ? 'btn btn-mini btn-inverse' : 'btn btn-mini';
		EventBus.send('get_tuples');
	};
	buttons_area.appendChild(btn);
	btn = newDom('a');
	btn.className = 'btn btn-mini';
	btn.appendChild(document.createTextNode('Fill'));
	btn.onclick = function(){
		obj.fill_lines = !obj.fill_lines;
		this.className = obj.fill_lines ? 'btn btn-mini btn-inverse' : 'btn btn-mini';
		EventBus.send('get_tuples');
	};
	buttons_area.appendChild(btn);
	btn = newDom('a');
	btn.className = 'btn btn-mini';
	btn.appendChild(document.createTextNode('Points'));
	btn.onclick = function(){
		obj.points = !obj.points;
		this.className = obj.points ? 'btn btn-mini btn-inverse' : 'btn btn-mini';
		EventBus.send('get_tuples');
	};
	buttons_area.appendChild(btn);
	this.screen.appendChild(buttons_area);
},

manageXScale: function()
{
	//	Minimal interval
	if (this.x_min === this.x_max)
	{
		--this.x_min;
		++this.x_max;
	}

	// Lot of calculations for find a good scale
	var size_x = this.x_max - this.x_min;
	var tmp_x = quantizeTics(size_x);
	if (tmp_x > this.tic_x)
		this.tic_x = tmp_x;

	if (size_x == 0)
	    size_x = 1.0;
	else
	    var new_coef_x = this.width / size_x;

	if (new_coef_x < this.coef_x ||
		Math.abs((this.coef_x - new_coef_x) /  new_coef_x) > 0.05)
		this.coef_x = this.width / size_x;
},

manageYScale: function(y_min, y_max)
{
	// The same than XScale, but for the Y axis
	var size_y = y_max - y_min;
	var tmp_y = quantizeTics(size_y);
	if (tmp_y > this.tic_y)
		this.tic_y = tmp_y;

	size_y = Math.ceil(size_y / tmp_y) * tmp_y;

	if (size_y == 0)
	    size_y = 1.0;
	else
	    this.coef_y = this.height / size_y;
},

includeLine: function(data, key)
{
	var c = this.canvasGraph;
	var data_length = data.time_t.length;

	// Nothing to do here
	if (data_length == 0) return [[], 0, 0];

	// We draw the lines in a second time, for the sampling
	var points_to_draw = [];
	var n_lines = 0;

	var current_x_pos = -1;
	var current_value = 0.0;
	var nb_current_points = 0;

	var y_min = Number.MAX_VALUE;
	var y_max = -Number.MAX_VALUE;

	// For each point
	for (var i = 0; i < data_length; ++i)
	{
		var time = data.time_t[i];
		var value = data[key][i];

		// Min max on the normal values
		if (value < y_min) y_min = value;
		if (value > y_max) y_max = value;

		// Position
		var x_pos = parseInt((time - this.x_min) * this.coef_x);

		// If the position is the same, sampling it !
		if (x_pos === current_x_pos)
		{
			current_value += value;
			++nb_current_points;
		}
		else
		{
			var sampled_value = current_value / nb_current_points;

			// Add the line at the good place, with the average value
			points_to_draw.push([current_x_pos, sampled_value]);

			// If the current point is just the next pixel
			// the average is applied
			if ((x_pos-1) === current_x_pos)
			{
				nb_current_points = nb_current_points / 3 + 1;
				current_value = current_value / 3 + value;
			}
			else
			{
				current_value = value;
				nb_current_points = 1;
			}

			// Set to the new values
			current_x_pos = x_pos;

		}
	}

	// Add the list line
	sampled_value = current_value / nb_current_points;
	points_to_draw.push([current_x_pos, sampled_value]);

	//	Minimal interval
	if (y_min === y_max)
	{
		--y_min;
		++y_max;
	}

	return [points_to_draw, y_min, y_max];
},

drawLine: function(points, color, y_min)
{
	var c = this.canvasGraph;

	c.beginPath();
	c.strokeStyle = color;
	c.lineWidth = 2.5;
	// c.shadowBlur = 3;
	// c.shadowColor = "grey";
	// c.shadowOffsetX = 1;
	// c.shadowOffsetY = 1;


	var points_length = points.length;

	// var x_i = 0;
	// var	y_i = this.height - (first_point[keyY] - b.y_min) * this.coef_y;
	// c.moveTo(x_i,y_i);

	var max_x_by_point = ( this.width / points_length) * 10.0;
	var old_x_pos = -max_x_by_point;

	for (var i = 0; i < points_length; ++i)
	{
		var x_pos = points[i][0];
		var y_pos = this.height - (points[i][1] - y_min) * this.coef_y;

		// var old_x_i = x_i;
		// x_i = points_to_draw[i][0];
		// y_i = points_to_draw[i][1];
		// var diff = x_i - old_x_i;

		if (this.points)
			c.arc(x_pos, y_pos, 3.2, 0, Math.PI*2);
		else if (x_pos - old_x_pos > max_x_by_point)
			c.moveTo(x_pos, y_pos);
		else
			c.lineTo(x_pos, y_pos);

		old_x_pos = x_pos;

		// if (diff > max_x_by_point)
			// c.moveTo(x_i,y_i);
		// else
			// c.lineTo(x_i, y_i);
	}


	if (this.fill_lines)
	{
		// var gradient = c.createLinearGradient(0,0,0, this.height);
		// gradient.addColorStop(0, color);
		// gradient.addColorStop(1, 'transparent');
		// c.fillStyle = gradient;
		c.fillStyle = color;
		if (!this.points)
		{
			c.lineTo(x_pos, this.height);
			c.lineTo(points_length > 0 ? points[0][0] : 0,this.height);
		}
		c.fill();
	}
	else
		c.stroke();

	c.closePath();
},

paintAxes: function(mili, paintForced, y_min)
{

	if (!paintForced &&
		this.old_tic_x === this.tic_x && this.old_tic_y === this.tic_y)
		return;

	var c = this.canvasAxes;
	c.clearRect(0,0, this.width, this.height);

	c.strokeStyle = "#505050";
	c.lineWidth = 1;
	c.fillStyle = 'white';
	c.font = "10px monospace";
	c.textBaseline = 'top';

	var x_val = this.tic_x * this.coef_x;
	var y_val = this.tic_y * this.coef_y;
	var x_tic = Math.round(x_val);
	var y_tic = Math.round(y_val);

	if (x_tic <= 0.0) x_tic = 1.0;
	if (y_tic <= 0.0) y_tic = 1.0;

	if(x_tic === 1 || y_tic == 1)
		return;

	// Vertical lines
	for(var i = 0.5; i < this.width + 0.5 ; i += x_tic){
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
	for(var i = 0.5; i < this.height + 0.5 ; i += y_tic){
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

	if (this.show_x_values && !this.multi_scale)
	{
		var min_y_value = Math.floor(y_min / this.tic_y) * this.tic_y;
		// console.log(min_y_value);
		for(var i = 0.5; i <= this.height+0.5 ; i += y_tic)
		{
			var x_pos = this.height - i - 11;
			if (x_pos < 0) x_pos = 0;
			if (x_pos > this.height - 10) x_pos = this.height - 10;

			var v = parseInt(
					((i-0.5) / this.coef_y + min_y_value + 0.5)/this.tic_y)
						* this.tic_y;

			var sv =v.toString();
			if (sv.length > 5)
				sv = v.toPrecision(5);
			// console.log(i, this.height, y_tic);
			c.fillText(sv, 0.5, x_pos);
		}
	}

},

paintCursor: function() {
	var x_pos = parseInt((this.cursor_time_t - this.x_min) * this.coef_x);

	if (x_pos < 0)
		x_pos = 0;
	else if (x_pos > this.width)
		xpos = this.width;

	x_pos += 'px';

	if (this.time_cursor.style.left != x_pos)
		this.time_cursor.style.left = x_pos;
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

listeners: {
	time_sync: function(d, obj)
	{
		obj.x_min = d.start_t;
		obj.x_max = d.end_t;
	},

	tuples: function(detail, obj) {
		var lines_to_draw = [];

		obj.clear();
		obj.manageXScale();

		var y_min = Number.MAX_VALUE;
		var y_max = -Number.MAX_VALUE;

		for (var statement_name in detail)
		{
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];
			for (var k in data)
				if (k != 'time_t')
				{
					var line = obj.includeLine(data, k);
					if (line[1] < y_min) y_min = line[1];
					if (line[2] > y_max) y_max = line[2];
					lines_to_draw.push(line);

					var id_legend = "legend_"+(statement_name+k).hashCode();
					if (!byId(id_legend))
					{
						var legend = newDom('li');
						legend.id = id_legend;
						legend.style.color = obj.colors[(lines_to_draw.length-1)%obj.colors.length];
						legend.appendChild(document.createTextNode(statement_name + ' : ' + k));
						obj.legend_area.appendChild(legend);
					}
				}
		}

		if (obj.multi_scale)
		{
			var min_tic_y = Number.MAX_VALUE;
			var associated_coef_y = 0.0;

			for (var i = 0; i < lines_to_draw.length; ++i)
			{
				var line = lines_to_draw[i];
				obj.manageYScale(line[1], line[2]);
				if (obj.tic_y < min_tic_y)
				{
					min_tic_y = obj.tic_y;
					associated_coef_y = obj.coef_y;
				}
				obj.drawLine(line[0], obj.colors[i%obj.colors.length], line[1]);
			}

			obj.tic_y = min_tic_y;
			obj.coef_y = associated_coef_y;
		}
		else
		{
			obj.manageYScale(y_min, y_max);
			for (var i = 0; i < lines_to_draw.length; ++i)
				obj.drawLine(lines_to_draw[i][0],
					obj.colors[i%obj.colors.length], y_min);
		}

		obj.paintAxes(true, false, y_min);
		obj.paintCursor();
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
	},

	// Gestion de la taille du graphe
	size_change: function(e, obj)
	{
		obj.width = $(obj.screen).width();
		obj.height = $(obj.screen).height();

		if (obj.screenGraph.width !== obj.width)
		{
			obj.screenGraph.width = obj.width;
			obj.screenAxes.width = obj.width;
		}

		if (obj.screenGraph.height !== obj.height)
		{
			obj.screenGraph.height = obj.height;
			obj.screenAxes.height = obj.height;
		}

		// Reset the scale
		obj.clear(true);
	},

	cursor: function(detail, obj) {
		obj.cursor_time_t = detail.time_t;
		obj.paintCursor();
	}

}};
