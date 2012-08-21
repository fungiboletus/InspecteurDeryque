var SuperOperator = function() {
	this.rest_location = ROOT_PATH + "/app/RestJson/";

	EventBus.addListeners(this.listeners, this);

	this.database = {};
	this.count_database = {};

};

SuperOperator.prototype = {
ajax: function(path, callback) {
	$.ajax({
		url: this.rest_location+path,
		success: callback,
		error: function(e) {
			EventBus.send("error", {status: e.status, message: e.statusText});
		}});
},

super_finished_events: function()
{
	this.superOperator.listeners.get_bounds(null, this.superOperator);
	EventBus.send('time_sync', {
		start_t: this.data.time_tMin,
		end_t: this.data.time_tMax
	});

	EventBus.sendDelayed('get_bounds');
},

super_bounds: function() {
	if (this.data && (typeof this.data.data.time_t === 'undefined')) return false;

	var r = $.extend({}, this.data);
	delete r.data;
	return r;
},

super_time_sync: function(start_t, end_t)
{
	if (typeof this.data === 'undefined' ||
		typeof this.data.data.time_t === 'undefined')
			return false;

	var data = this.data.data;

	// Wonderful dichotomical research oh yeah
	var begin = 0, end = data.time_t.length, old_m = -1, m = -1;

	do {
		m = parseInt(begin + (end-begin)/2);
		var t = data.time_t[m];
		if (old_m === m || t == start_t)
			break;
		else if (t < start_t)
			begin = m + 1;
		else
			end = m -1;
		old_m = m;
	} while (begin < end);

	var begin_filtered_data = m;

	begin = 0; end = data.time_t.length; old_m = -1; m = -1;

	do {
		m = parseInt(begin + (end-begin)/2);
		var t = data.time_t[m];
		if (old_m === m || t == end_t)
			break;
		else if (t < end_t)
			begin = m + 1;
		else
			end = m -1;
		old_m = m;
	} while (begin < end);

	var end_filtered_data = m;

	// Create a new view of the array data
	var r = {};

	for (key in data)
		r[key] = data[key].subarray(begin_filtered_data, end_filtered_data);

	return r;
},

listeners: {
get_statements_list: function(d, obj) {
	obj.ajax('reports', function(json) {
		EventBus.send("statements_list", json);
	});
},

add_statement: function(d, obj) {
	var statement_name = d.statement_name;

	if (statement_name in obj.count_database)
	{
		obj.count_database[statement_name] = obj.count_database[statement_name] + 1;

		if ((statement_name in obj.database) &&
			obj.database[statement_name].load_finished)
			obj.database[statement_name].finished_events();
	}
	else
	{

		obj.count_database[statement_name] = 0;

		obj.ajax('resume/'+encodeURIComponent(statement_name),
		function(json)
		{
			var storage = json.storage;

			if (typeof window[storage] !== 'undefined')
			{
				var storageInstance = new window[storage](obj, statement_name, json);
				obj.database[statement_name] = storageInstance;
				obj.count_database[statement_name] = obj.count_database[statement_name] + 1;
			}
			else
				alert('Unknow storage type : '+storage);
		});
	}
},

del_statement: function(e, obj) {
	var statement_name = e.statement_name;
	// Delete if the last
	if (statement_name in obj.count_database)
	{
		obj.count_database[statement_name] = obj.count_database[statement_name] - 1;

	if (
		(obj.count_database[statement_name] === 0) &&
		(statement_name in obj.database))
	{
		delete obj.database[statement_name];
		delete obj.count_database[statement_name];
	}

	}

	EventBus.sendDelayed('get_bounds');
},

get_bounds: function(d, obj) {
	var response = {};

	var send_bounds = false;
	for (var statement_name in obj.database)
	{
		var bounds = obj.database[statement_name].bounds();
		if (bounds)
		{
			send_bounds = true;
			response[statement_name] = bounds;
		}
	}

	if (send_bounds)
		EventBus.send('bounds', response);
},

time_sync: function(d, obj) {
	if (typeof d.start_t === 'undefined' || d.start_t == null) d.start_t = 0;
	if (typeof d.end_t === 'undefined' || d.end_t == null) d.end_t = (new Date()) * 1 + 3600 * 24;

	var response = {};

	var send_time_sync = false;
	for (var statement_name in obj.database)
	{
		var time_sync = obj.database[statement_name].time_sync(d.start_t, d.end_t);
		if (time_sync)
		{
			send_time_sync = true;
			response[statement_name] = time_sync;
		}
	}

	// Hack with the setTimeout for send the tuples event after the time_sync event
	if (send_time_sync)
		EventBus.sendDelayed('tuples', response);
}

}};