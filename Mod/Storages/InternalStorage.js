var InternalStorage = function(superOperator, statement_name)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;

	this.data = {
		data: []
	};

	var obj = this;

	superOperator.ajax('data_dt/'+encodeURIComponent(statement_name),
		function(json) {
			var start_t = Date.parse(json.start_t);
			var date = new Date(start_t);

			var _addTuple = function(i)
			{
				var tuple =json.data[i];
				start_t += tuple.dt;
				tuple.time_t = new Date(start_t);
				delete tuple.dt;

				obj.addTuple(tuple);
				return tuple;
			}

			if (json.data) {
				var i = 0;
				for (; i < json.data.length; ++i)
				 	_addTuple(i);
			}

			// Autosend of bounds
			superOperator.listeners.get_bounds(null, superOperator);

			EventBus.send('time_sync', {
				start_t: obj.data.time_tMin,
				time_t: obj.data.time_tMin,
				end_t: obj.data.time_tMax
			});

			EventBus.send('get_bounds');
		});
};

InternalStorage.prototype = {

bounds: function()
{
	var r = $.extend({}, this.data);
	delete r.data;
	return r;
},

time_sync: function(start_t, end_t)
{
	var data = this.data.data;
	var filtered_data = [];
	for (var i = 0; i < data.length; ++i) {
		var t = data[i];
		if (t.time_t >= start_t && t.time_t <= end_t)
			filtered_data.push(t);
	}

	return filtered_data;
},

addTuple: function(tuple)
{
	for (var key in tuple)
	{
		var keyMin = key+'Min';
		var keyMax = key+'Max';
		if (!(keyMin in this.data) || tuple[key] < this.data[keyMin])
			this.data[keyMin] = tuple[key];

		if (!(keyMax in this.data) || tuple[key] > this.data[keyMax])
			this.data[keyMax] = tuple[key];
	}
	this.data.data.push(tuple);
}
};