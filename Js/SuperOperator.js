var SuperOperator = function() {
	this.rest_location = ROOT_PATH + "/app/RestJson/";

	EventBus.addListeners(this.listeners, this);

	this.database = {};
	this.loading_database = {};
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

super_bounds: function() {
	var r = $.extend({}, this.data);
	delete r.data;
	return r;
},

super_time_sync: function(start_t, end_t)
{
	if (typeof this.data === 'undefined' ||
		typeof this.data.data.time_t === 'undefined')
			return {};

	var data = this.data.data;

	// console.log(data, data.time_t);

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

	if (statement_name in obj.loading_database)
		return;

	obj.loading_database[statement_name] = true;

	obj.ajax('resume/'+encodeURIComponent(statement_name),
	function(json)
	{
		var storage = json.storage;

		if (typeof window[storage] !== 'undefined')
			obj.database[statement_name] = new window[storage](obj, statement_name, json);
		else
			alert('Unknow storage type : '+storage);
	});


},

del_statement: function(e, obj) {
	if (e.statement_name in obj.database)
		delete obj.database[e.statement_name];

	obj.listeners.get_bounds(null, obj);
},

get_bounds: function(d, obj) {
	var response = {};

	for (var statement_name in obj.database)
		response[statement_name] = obj.database[statement_name].bounds();

	EventBus.send('bounds', response);
},

time_sync: function(d, obj) {
	if (!d.start_t) d.start_t = new Date(0);
	if (!d.end_t) { d.end_t = new Date(); d.end_t.setDate(d.end_t.getDate()+1);}

	var response = {};

	for (var statement_name in obj.database)
		response[statement_name] =
			obj.database[statement_name].time_sync(d.start_t, d.end_t);

	// Hack with the setTimeout for send the tuples event after the time_sync event
	window.setTimeout(function(){EventBus.send('tuples', response);}, 1);
}

}};