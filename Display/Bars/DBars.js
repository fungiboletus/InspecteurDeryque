/* This file is released under the CeCILL-B V1 licence.*/

var DBars = function(screen)
{

	// Graph area
	this.screen = screen;

	// Lines
	this.lines = [];

	this.database = {};
	EventBus.addListeners(this.listeners, this);
};

DBars.prototype.listeners = {
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
				}

		}

		if (n > 0) value /= n;

		var ratio = (value - obj.min_value) / (obj.max_value - obj.min_value);
		if (n === 0) ratio = 0;

		var nb_bars = obj.lines.length;
		var nb_actived_bars = Math.ceil(ratio * nb_bars);

		for (var i = 0; i < nb_actived_bars; ++i)
			obj.lines[i].className = 'activated bar';

		for (var i = nb_actived_bars; i < nb_bars; ++i)
			obj.lines[i].className = 'bar';
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
	size_change: function(e, obj)
	{
		var width = $(obj.screen).width();
		var height = $(obj.screen).height();

		var line_width = 42;

		var nb_lines = parseInt(width / line_width);

		var old_nb_lines = obj.lines.length;
		if (nb_lines > old_nb_lines)
		{
			for (var i = old_nb_lines; i < nb_lines; ++i)
			{
				var line = newDom('div', 'bar');
				obj.lines.push(line);
				obj.screen.appendChild(line);
			}
		}
		else if (nb_lines < old_nb_lines)
			for (var i = nb_lines; i < old_nb_lines; ++i)
				obj.screen.removeChild(obj.lines.pop());

		var height_increment = height/nb_lines;

		var left = 0;

		for (var i = 0; i < nb_lines; ++i)
		{
			height -= height_increment;
			obj.lines[i].style.top = height+'px';
			obj.lines[i].style.left = left+'px';
			left += line_width;
		}

	}
};
