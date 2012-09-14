/* This file is released under the CeCILL-B V1 licence.*/

/**
 *	PieChart using Javascript and CSS3.
 *
 *	Transitions, antialiasing, and wonderful colors are present.
 */
var DPieChart = function(screen)
{
	this.screen = screen;

	// Create the chart
	this.chart = newDom('div', 'chart');

	// The big pie is a specific pie when the value is superor to 50%
	this.big_pie = newDom('div', 'pie big');
	this.chart.appendChild(this.big_pie);
	screen.appendChild(this.chart);

	// The legend…
	this.legend = newDom('ul', 'legend');
	screen.appendChild(this.legend);

	// The pointer
	this.pointer = newDom('div', 'pointer');
	screen.appendChild(this.pointer);

	// Wonderful colors (like everytime)
	this.colors = ['#FFD600', 'dodgerblue', 'limegreen', 'red', '#E81BA3'];

	// Nb of pies
	this.nb_pies = 5;
	this.createPies();

	// Always the same code :-)
	this.database = {};
	EventBus.addListeners(this.listeners, this);
};

DPieChart.prototype =
{
createPies: function() {

	// The big pie is not counted
	var current_nb_pies = this.chart.childNodes.length - 1;

	// Removing the unecessary pies
	while (current_nb_pies > this.nb_pies)
	{
		this.chart.removeChild(this.chart.lastChild);
		this.legend.removeChild(this.legend.lastChild);
		--current_nb_pies;
	}

	// Create the pies if necessary
	while (current_nb_pies < this.nb_pies)
	{
		// Get the color
		var background = this.colors[(current_nb_pies)%this.colors.length];

		// The pie use 2 div
		var hold = newDom('div', 'hold');
		var pie = newDom('div', 'pie pie_'+current_nb_pies);
		pie.style.background = background;
		hold.appendChild(pie);
		this.chart.appendChild(hold);

		// Create the legend item
		var li = newDom('li');
		addText(li, 'Emtpy');
		var strong = newDom('strong');
		addText(strong, '');
		li.appendChild(strong);
		this.legend.appendChild(li);
		li.style.color = background;

		++current_nb_pies;
	}

	this.updateLegend();
},

updateLegend: function() {
	// Prepare the values
	var tic = quantizeTics(this.max_value - this.min_value);
	var v = this.min_value;

	for (var i = 0; i < this.nb_pies; ++i)
	{
		// Update the legend strong text
		var txt = '[' + Math.round(v / tic) * tic;
		v += this.interval;
		txt += ', '+ Math.round(v / tic) * tic + ((i+1) === this.nb_pies ? '] :' : '[ : ');
		this.legend.childNodes[i].firstChild.data = txt;
	}
},

applyCssRotation: function(node, angle) {
	var transform = 'rotate(' + angle *360.0 + 'deg)';

	// no comment (===> [])
	node.style.webkitTransform = transform;
	node.style.mozTransform = transform;
	node.style.transform = transform;
},

listeners: {
	// The pie chart is a big chart, with scale transformations
	// this is simpler to develop, and a antialiasing hack is also present
	size_change: function(d, obj) {

		// The chart height is fixed (by the css)
		var chart_height = 800.0;
		var height = $(obj.screen).height();

		var ratio = height / chart_height;

		// It could be very ugly to have a pie chart more large than the
		// fixed size
		if (ratio > 1.0)
			ratio = 1.0;

		// A small rotation for the design, and the scale effect
		var transform = 'rotate(10deg) scale('+ratio+','+ratio+')';
		obj.chart.style.webkitTransform = transform;
		obj.chart.style.mozTransform = transform;
		obj.chart.style.transform = transform;

		// Vertical center of the chart
		obj.chart.style.top = -(height * 0.5) / ratio + height * 0.5 + 10 + 'px';
	},
	bounds: function(d, obj) {
		obj.min_value = Number.MAX_VALUE;
		obj.max_value = -Number.MAX_VALUE;

		// Find the max and the min values
		for (var local_statement in obj.database)
			if (local_statement in d)
				for (var type in d[local_statement])
				{
					var name = type.slice(0, -3);
					if (name !== 'time_t')
					{
						var side = type.slice(-3);
						if (side === 'Max' && obj.max_value < d[local_statement][type])
							obj.max_value = d[local_statement][type];
						else if (side === 'Min' && obj.min_value > d[local_statement][type])
							obj.min_value = d[local_statement][type];
					}
				}

		// the interval is the width of a pie
		obj.interval = (obj.max_value - obj.min_value) / obj.nb_pies + obj.max_value * 0.00001;

		// update the legend in order to show the new bounds
		obj.updateLegend();
	},
	tuples: function(detail, obj) {

		// Initialize the counters
		var counters = new Array(obj.nb_pies);
		for (var i = 0; i < obj.nb_pies; ++i)
			counters[i] = 0;


		// Increment the counters in function to the selected data
		for (var statement_name in detail) {
			if (!(statement_name in obj.database)) continue;

			var data = detail[statement_name];
			var nb_data = data.time_t.length;

			for (var i = 0; i < nb_data; ++i)
				for (var k in data)
					if (k !== 'time_t')
						++counters[parseInt((data[k][i] - obj.min_value) / obj.interval)];
		}

		// In my opinion, it's most performant to increment the global counter here
		// instead of the counters increment's loop
		var global_counter = 0;
		for (var i = 0; i < obj.nb_pies; ++i)
			global_counter += counters[i];

		// Prevent divisions by zero
		if (global_counter === 0) global_counter = 1;

		// The globalratio is used to store the current angle
		var global_ratio = 0.0;

		for (var i = 0; i < obj.nb_pies; ++i)
		{
			var ratio = counters[i] / global_counter;
			var hold =	obj.chart.childNodes[i+1];

			// If the ratio is null, print nothing
			if (ratio == 0.0)
			{
				hold.style.display = 'none';
			}
			else
			{
				// The ratio could be modified in this block,
				// only for the display
				var displayed_ratio = ratio;

				if (hold.style.display !== 'bloc')
					hold.style.display = 'block';

				// Rotate the pie mask
				obj.applyCssRotation(hold, global_ratio);

				// If the ratio is superor to 50%
				// it's a particular case (just one time by execution)
				if (ratio > 0.5)
				{
					var big_pie = obj.chart.firstChild;

					// Set the correct color to the big pie
					big_pie.style.background = hold.firstChild.style.background;

					// The half is displayd, so remove this part
					displayed_ratio -= 0.5;

					// Rotate the big pie
					obj.applyCssRotation(big_pie, displayed_ratio + global_ratio - 0.5);
				}

				// Rotate the pie
				obj.applyCssRotation(hold.firstChild, displayed_ratio);
			}
			global_ratio += ratio;
			// obj.chart.childNodes[i+1].style.webkitTransform = transform;
			obj.legend.childNodes[i].lastChild.firstChild.data = Math.round(ratio * 100) + '%';
		}
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
