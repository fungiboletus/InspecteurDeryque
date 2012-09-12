/* This file is released under the CeCILL-B V1 licence.*/

/**
 *	Connector for the internal storage of InspecteurDeryque.
 */
var InternalStorage = function(superOperator, statement_name)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;
	this.load_finished = false;

	// Database
	this.data = {
		data: {}
	};

	var obj = this;

	// Load all the data
	superOperator.ajax('data_dt/'+encodeURIComponent(statement_name),
		function(json) {
			// The response send a basedate, and a diff with this date for each
			// tuple
			var start_t = Date.parse(json.start_t);

			// If the data is present…
			if (json.data && json.data.length > 0) {

				var nb_data = json.data.length;

				// 64 bits !
				var size = nb_data * 8;
				for (var key in json.data[0])
				{
					if (key === 'dt') key = 'time_t';
					// Create the arrays
					var buffer = new ArrayBuffer(size);
					obj.data.data[key] = new Float64Array(buffer);
				}

				// Store the data in it
				for (var i = 0; i < nb_data; ++i)
				{
					var tuple =json.data[i];
					// The difftime is in milisecondes
					start_t += tuple.dt / 1000;

					// Replace the difftime by the time_t
					delete tuple.dt;
					tuple.time_t = start_t;

					// Add the tuple
					obj.addTuple(tuple, i);
				}
			}

			obj.load_finished = true;
			obj.finished_events();

		});
};

InternalStorage.prototype =
{
// Heritage
bounds: SuperOperator.prototype.super_bounds,
time_sync: SuperOperator.prototype.super_time_sync,
finished_events: SuperOperator.prototype.super_finished_events,
// rt_clock: SuperOperator.prototype.super_rt_clock,
cursor: SuperOperator.prototype.super_cursor,

addTuple: function(tuple, i)
{
	// For each value in the tuple
	for (var key in tuple)
	{
		// Find the bounds for the value
		var keyMin = key+'Min';
		var keyMax = key+'Max';
		if (!(keyMin in this.data) || tuple[key] < this.data[keyMin])
			this.data[keyMin] = tuple[key];

		if (!(keyMax in this.data) || tuple[key] > this.data[keyMax])
			this.data[keyMax] = tuple[key];

		// Add the value to the database
		this.data.data[key][i] = tuple[key];
	}
}
};
