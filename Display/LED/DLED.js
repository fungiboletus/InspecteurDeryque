/* This file is released under the CeCILL-B V1 licence.*/

var DLED = function(screen)
{

	// Graph area
	this.screen = screen;

	// Create interface
	this.led = newDom('div', 'led off');

	this.legend = newDom('div', 'legend');
	addText(this.legend, 'Empty');
	screen.appendChild(this.led);
	screen.appendChild(this.legend);

	this.database = {};
	EventBus.addListeners(this.listeners, this);
};

DLED.prototype.listeners = {
	size_change: function(d, obj) {
		obj.led.style.width = $(obj.led).height()+'px';
	},

	bounds: function(d, obj) {
		obj.min_value = Number.MAX_VALUE;
		obj.max_value = -Number.MAX_VALUE;

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
	},

	values: function(detail, obj) {
		var legend = '';
		var value = 0;
		var n = 0;
		for (var statement_name in detail) {
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];

			for (var k in data)
				if (k != 'time_t')
				{
					value += data[k];
					++n;
					legend += statement_name + ' : ' + k + ' — ';
				}
		}

		obj.legend.firstChild.data = legend.substr(0, legend.length-3);

		if (n > 0) value /= n;

		var ratio = (value - obj.min_value) / (obj.max_value - obj.min_value);
		if (n === 0) ratio = 0;

		// The led is on if the value is superior to the half
		obj.led.className = ratio < 0.5 ? 'led off' : 'led on';
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
};
