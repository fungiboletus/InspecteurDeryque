/* This file is released under the CeCILL-B V1 licence.*/

var DTachometer = function(screen)
{

	// Graph area
	this.screen = screen;

	// Lines
	this.lines = [];
	this.needles = [];

	this.colors = ['orangered', 'dodgerblue', 'limegreen', 'yellowgreen', 'salmon', 'cyan'];

	// Creation of the structure
	var createInfo = function(id)
	{
		var info = newDom('div', 'info');
		info.id = id;
		var data_info = newDom('div');
		addText(data_info, '');
		info.appendChild(data_info);
		screen.appendChild(info);
		return info;
	};

	this.infoMin = createInfo('info_min');
	this.infoMinMedium = createInfo('info_min_medium');
	this.infoMedium = createInfo('info_medium');
	this.infoMediumMax = createInfo('info_medium_max');
	this.infoMax = createInfo('info_max');

	var n = 0;
	for (var i = -2; i < 158; i+= 1.6)
	{
		var line = newDom('div');
		var classname = i < 125 ? 'line' : 'line last';
		line.className = (n++ % 5 === 0) ? classname + ' big' : classname;
		var transform = 'rotate('+i+'deg)';
		line.style.webkitTransform = transform;
		line.style.mozTransform = transform;
		line.style.transform = transform;
		screen.appendChild(line);
		this.lines.push(line);
	}

	this.legend_area = newDom('ul', 'legend');
	this.screen.appendChild(this.legend_area);

	this.database = {};
	this.size_database = 0;
	EventBus.addListeners(this.listeners, this);
};

DTachometer.prototype.listeners = {
	bounds: function(d, obj) {
		obj.min_value = Number.MAX_VALUE;
		obj.max_value = -Number.MAX_VALUE;

		var updated = false;

		for (var local_statement in obj.database)
		{
			if (local_statement in d)
			{
				for (var type in d[local_statement])
				{
					var name = type.slice(0, -3);
					if (name !== 'time_t')
					{
						updated = true;
						var side = type.slice(-3);
						if (side === 'Max' && obj.max_value < d[local_statement][type])
							obj.max_value = d[local_statement][type];
						else if (side === 'Min' && obj.min_value > d[local_statement][type])
							obj.min_value = d[local_statement][type];
					}
				}
			}
		}

		if (!updated)
		{
			obj.min_value = 0.0;
			obj.max_value = 256.0;
		}
		else
		{
			var tic = quantizeTics(obj.max_value - obj.min_value);

			obj.min_value = Math.floor(obj.min_value / tic) * tic;
			obj.max_value = Math.ceil(obj.max_value / tic) * tic;
		}

		var medium = (obj.max_value - obj.min_value) * 0.5 + obj.min_value;
		var minMedium = (medium - obj.min_value) * 0.5 + obj.min_value;
		var mediumMax = (obj.max_value - medium) * 0.5 + medium;

		obj.infoMin.firstChild.firstChild.data = obj.min_value;
		obj.infoMinMedium.firstChild.firstChild.data = minMedium;
		obj.infoMedium.firstChild.firstChild.data = medium;
		obj.infoMediumMax.firstChild.firstChild.data = mediumMax;
		obj.infoMax.firstChild.firstChild.data = obj.max_value;

	},

	values: function(detail, obj) {
		var updated_needle = [];

		for (var statement_name in detail) {
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];

			for (var k in data)
				if (k != 'time_t')
				{
					// première valeur pour l'instant, car bon voila quoi,
					// faut faire d'autres évènements
					var id = "needle_"+(statement_name+k).hashCode();
					var box = byId(id);
					if (!box) {
						box = newDom('div', 'needle');
						box.id = id;
						box.style.webkitTransformOriginX = obj.px_height;
						box.style.width = obj.px_height;
						box.style.background = obj.colors[obj.needles.length%obj.colors.length];
						// box.appendChild(document.createTextNode(''));
						obj.screen.appendChild(box);
						obj.needles.push(box);
					}
					// If no values, min values
					var value = data[k];

					var ratio = (value - obj.min_value) / (obj.max_value - obj.min_value);

					var angle = -2 + 160 * ratio;

					var transform = 'rotate('+angle+'deg)';
					box.style.webkitTransform = transform;
					box.style.mozTransform = transform;
					box.style.transform = transform;
					updated_needle.push(box);

					var id_legend = id+'_legend';
					if (!byId(id_legend))
					{
						var legend = newDom('li');
						legend.id = id_legend;
						legend.style.color = obj.colors[(obj.needles.length-1)%obj.colors.length];
						addText(legend, statement_name + ' : ' + k);
						obj.legend_area.appendChild(legend);
					}
				}
		}

		var ni = obj.needles.length;
		for (var i = 0; i < ni; ++i)
		{
			var needle = obj.needles[i];
			if (updated_needle.indexOf(needle) === -1)
			{
				obj.needles.splice(i, 1);
				obj.screen.removeChild(needle);
				obj.legend_area.removeChild(byId(needle.id + '_legend'));
			}
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
	},
	size_change: function(e, obj)
	{
		var width = $(obj.screen).width();
		var height = $(obj.screen).height();
		var screen_height = height;

		if (height * 2 > width) height = width * 0.5;

		var px_height = height - 30 + 'px';

		var ni = obj.lines.length;
		for (var i = 0; i < ni; ++i)
			obj.lines[i].style.webkitTransformOriginX = px_height;

		var ni = obj.needles.length;
		for (var i = 0; i < ni; ++i)
		{
			obj.needles[i].style.webkitTransformOriginX = px_height;
			obj.needles[i].style.width = px_height;
		}

		obj.px_height = px_height;

		var px_height = height - 75 + 'px';
		obj.infoMin.style.webkitTransformOriginX = px_height;
		obj.infoMinMedium.style.webkitTransformOriginX = px_height;
		obj.infoMedium.style.webkitTransformOriginX = px_height;
		obj.infoMediumMax.style.webkitTransformOriginX = px_height;
		obj.infoMax.style.webkitTransformOriginX = px_height;

		var marge = (width - 2 * height + 150) / 2;
		obj.screen.style.left = marge + 'px';
		marge = (screen_height - height + 30) / 2;
		obj.screen.style.bottom = marge + 'px';
	}
};
