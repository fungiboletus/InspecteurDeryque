var SuperOperator = function() {
	this.rest_location = ROOT_PATH + "/app/RestJson/";

	EventBus.addListeners(this.listeners, this);

	this.database = new Object();
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

listeners: {
get_statements_list: function(d, obj) {
	obj.ajax('reports', function(json) {
		EventBus.send("statements_list", json);
	});
},

add_statement: function(d, obj) {
	var statement_name = d.statement_name;
	// var hash = statement_name.hashCode();

	if (typeof obj.database[statement_name] !== 'undefined')
		return;

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

	EventBus.send('tuples', response);
}
}};