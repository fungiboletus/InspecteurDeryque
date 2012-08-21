var InternalStorage = function(superOperator, statement_name)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;

	this.data = {
		data: {}
	};

	var obj = this;

	superOperator.ajax('data_dt/'+encodeURIComponent(statement_name),
		function(json) {
			var start_t = Date.parse(json.start_t);

			var _addTuple = function(i)
			{
				var tuple =json.data[i];
				start_t += tuple.dt / 1000;
				tuple.time_t = start_t;
				delete tuple.dt;

				obj.addTuple(tuple, i);
			}

			if (json.data && json.data.length > 0) {

				var nb_data = json.data.length;

				// 64 bits !
				var size = nb_data * 8;
				for (var key in json.data[0])
				{
					if (key === 'dt') key = 'time_t';
					var buffer = new ArrayBuffer(size);
					obj.data.data[key] = new Float64Array(buffer);
				}

				for (var i = 0; i < nb_data; ++i)
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

InternalStorage.prototype =
{
bounds: SuperOperator.prototype.super_bounds,
time_sync: SuperOperator.prototype.super_time_sync,

addTuple: function(tuple, i)
{
	for (var key in tuple)
	{
		var keyMin = key+'Min';
		var keyMax = key+'Max';
		if (!(keyMin in this.data) || tuple[key] < this.data[keyMin])
			this.data[keyMin] = tuple[key];

		if (!(keyMax in this.data) || tuple[key] > this.data[keyMax])
			this.data[keyMax] = tuple[key];

		this.data.data[key][i] = tuple[key];
	}
}
};