/* This file is released under the CeCILL-B V1 licence.*/

var DConsole = function(area)
{
	this.console = newDom('ul');
	this.console.id = 'console';
	area.appendChild(this.console);

	var events = ['new_tuples', 'add_statement', 'del_statement',
		'size_change', 'log', 'error', 'statements_list',
		'get_statements_list', 'time_sync', 'tuples_selected',
		'get_bounds', 'bounds', 'rt_clock',
		'video', 'play_speed', 'play', 'pause',
		'cursor', 'values', 'send_selection'];

	for (var i = 0; i < events.length; ++i)
		EventBus.addListener(events[i], this.manageEvent, this);

	EventBus.addListener('tuples', this.tuples, this);

	this.max_capacity = false;
}

DConsole.prototype.manageEvent = function(detail, obj, e)
{
	var li = newDom('li');
	var type = newDom('strong');
	addText(type, e.type);
	li.appendChild(type);

	if (typeof detail !== 'undefined')
	{
		var json = newDom('span', 'json rainbow');
		li.appendChild(json);

		var text = JSON.stringify(detail, null, 2);

		var rainbow_a_fonctionne = false;
		if (text.length < 512)
		{
			try {
				Rainbow.color(text,
					'generic', function(html) {
					json.innerHTML = html;
				});
				rainbow_a_plante = true;
			} catch (e) {console.log(e);}
		}

		if (!rainbow_a_fonctionne && json.firstChild == null)
			addText(json, text);

	}

	var console = obj.console;
	console.appendChild(li);

	if (obj.max_capacity)
		console.removeChild(console.firstChild);
	else if (console.childNodes.length == 511)
		obj.max_capacity = true;

	console.scrollTop = console.scrollHeight;
};

DConsole.prototype.tuples = function(d, obj, e)
{
	var r = {};

	for (var key in d)
		r[key] = d[key].time_t.length;

	obj.manageEvent(r, obj, e)
};
