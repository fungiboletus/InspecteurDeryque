/* This file is released under the CeCILL-B V1 licence.*/

/**
 *	The SuperOperator is the «parent» of the storage operators classes.
 *
 *	It provide some usefull commun methods for storage operators,
 * and it manage the instance storage operators.
 */

var SuperOperator = function() {
	this.rest_location = URLS_DICTIONNARY.rest_json;

	EventBus.addListeners(this.listeners, this);

	// The database of statements
	this.database = {};

	// The number of uses for each statement
	this.count_database = {};

	// The bounds are used in internal
	this.current_bounds = {};

};

SuperOperator.prototype = {
// Call the RestJson API, and send the errors on the EventBus
ajax: function(path, callback) {
	$.ajax({
		url: this.rest_location+path,
		success: callback,
		error: function(e) {
			EventBus.send("error", {status: e.status, message: e.statusText});
		}});
},

// When the data loading is completed
super_finished_events: function()
{
	this.superOperator.listeners.get_bounds(null, this.superOperator);
	EventBus.send('time_sync', {
		start_t: this.data.time_tMin,
		end_t: this.data.time_tMax
	});

	EventBus.sendDelayed('get_bounds');
},

// Send the bounds of data operator
super_bounds: function() {
	if (this.data && (typeof this.data.data.time_t === 'undefined')) return false;

	var r = $.extend({}, this.data);
	delete r.data;
	return r;
},

// Send a time_selection from the data
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
	} while (begin <= end);

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

	// The good point is that subarray create a new view of the data
	// and not a new data array
	for (key in data)
		r[key] = data[key].subarray(begin_filtered_data, end_filtered_data+1);

	return r;
},

// Send the last values of the database
super_rt_clock: function(count)
{
	if (typeof this.data === 'undefined' ||
		typeof this.data.data.time_t === 'undefined')
			return false;

	var data = this.data.data;
	var data_length = data.time_t.length;

	if (count > data_length)
		count = data_length;

	return data.time_t[data_length - count];
},

listeners: {

// Send the statements list
get_statements_list: function(d, obj) {
	obj.ajax('reports', function(json) {
		EventBus.send("statements_list", json);
	});
},

// A new statement is loaded
add_statement: function(d, obj) {
	var statement_name = d.statement_name;

	// If the statement is already loaded or in load
	if (statement_name in obj.count_database)
	{
		// Increment the number of uses
		obj.count_database[statement_name] = obj.count_database[statement_name] + 1;

		// If the statement load is completed, send a new finished event
		// (else, the finished event will come too)
		if ((statement_name in obj.database) &&
			obj.database[statement_name].load_finished)
			obj.database[statement_name].finished_events();
	}
	else
	{
		// Initialize the number of uses
		obj.count_database[statement_name] = 1;

		// Get informations about the statement
		obj.ajax('resume/'+encodeURIComponent(statement_name),
		function(json)
		{
			// Which storage operator use ?
			var storage = json.storage;

			// Check if the storage operator type exist
			if (typeof window[storage] !== 'undefined')
			{
				// Create it, and store it
				var storageInstance = new window[storage](obj, statement_name, json);
				obj.database[statement_name] = storageInstance;
			}
			else
			{
				// The storage operator doesn't exist :(
				obj.count_database[statement_name] = obj.count_database[statement_name] - 1;
				EventBus.send('error', {status: 'Unknow storage type', message: storage});
			}
		});
	}
},

// A statement is no more used
del_statement: function(e, obj) {
	var statement_name = e.statement_name;

	// If the statement is in database (it could be true the most of the time)
	if (statement_name in obj.count_database)
	{
		obj.count_database[statement_name] = obj.count_database[statement_name] - 1;

		// Delete the storage operator if the last
		if ((obj.count_database[statement_name] === 0) &&
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

	// Construct the response for each statement
	var send_bounds = false;
	var time_tMin = Number.MAX_VALUE;
	var time_tMax = -Number.MAX_VALUE;
	var communMin = Number.MAX_VALUE;
	var communMax = -Number.MAX_VALUE;
	for (var statement_name in obj.database)
	{
		var bounds = obj.database[statement_name].bounds();

		// the bounds method could return false in case of error
		// or loading not completed
		if (bounds)
		{
			send_bounds = true;
			response[statement_name] = bounds;

			// Global bounds management
			if (bounds.time_tMin < time_tMin) time_tMin = bounds.time_tMin;
			if (bounds.time_tMax > time_tMax) time_tMax = bounds.time_tMax;
			for (var key in bounds)
			{
				if (key !== 'time_tMin' && key !== 'time_tMax')
				{
					var v = bounds[key];
					if (v < communMin) communMin = v;
					if (v > communMax) communMax = v;
				}
			}
		}
	}

	// It's useless to send empty bounds (many visualizations could have bugs)
	if (send_bounds)
	{
		response.__global__ = {
			time_tMin: time_tMin,
			time_tMax: time_tMax,
			communMin: communMin,
			communMax: communMax
		};
		EventBus.send('bounds', response);
		obj.current_bounds = response;
	}
},

size_change: function() {
	// Delayed sending in order to wait the size managment of other elements
	EventBus.sendDelayed('get_tuples');
},

time_sync: function(d, obj) {
	// Check the time interval, if they are not present,
	// Load the data for all the time
	if (typeof d.start_t === 'undefined' || d.start_t == null) d.start_t = 0;
	if (typeof d.end_t === 'undefined' || d.end_t == null) d.end_t = Date.now() + 3600 * 24;

	var response = {};

	var send_tuples = false;
	// Construct the response for each statement
	for (var statement_name in obj.database)
	{
		var time_sync = obj.database[statement_name].time_sync(d.start_t, d.end_t);
		if (time_sync)
		{
			send_tuples = true;
			response[statement_name] = time_sync;
		}
	}

	// Hack with the setTimeout for send the tuples event after the time_sync event
	if (send_tuples)
		EventBus.sendDelayed('tuples', response);
},

rt_clock: function(d, obj) {
	// Time interval (1 hour by default)
	var interval = (d && typeof d.interval !== 'undefined') ? d.interval : 60*60;

	var max_date = obj.current_bounds.__global__.time_tMax;
	var min_date = max_date - interval;

	EventBus.send('time_sync', {
		start_t: min_date,
		end_t: max_date
	});
},

}};
