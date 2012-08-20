var SensAppStorage = function(superOperator, statement_name, resume)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;
	this.resume = resume;

	var additional_data = resume.additional_data;

	// console.log(resume);
	var obj = this;

	$.ajax({
		url: additional_data.numerical,//+'?limit=20000',
		success: function(json){
			console.log(json);

			var nb_e = json.e.length;

			if (nb_e === 0)
				alert('todo');

			// 64 bits baby !
			var size = nb_e * 8;

			var time_buffer = new ArrayBuffer(size);
			var time_array = new Float64Array(time_buffer);
			var value_buffer = new ArrayBuffer(size);
			var value_array = new Float64Array(value_buffer);

			var time_incremment = (typeof json.bt === 'undefined') ? 0 : json.bt;

			var min = Number.MAX_VALUE;
			var max = -Number.MAX_VALUE;

			for (var i = 0; i < nb_e; ++i)
			{
				var ei = json.e[i];
				time_array[i] = (ei.t + time_incremment);// * 1000.0;
				value_array[i] = ei.v;
				if (ei.v > max)
					max = ei.v;
				if (ei.v < min)
					min = ei.v;
			}

			obj.data = {
				data: {
					time_t: time_array,
					value: value_array
				},

				// The data need to be sorted
				time_tMin: json.e[0].t + time_incremment,
				time_tMax: json.e[nb_e-1].t + time_incremment,

				valueMin: min,
				valueMax: max
			};

			// Autosend of bounds
			superOperator.listeners.get_bounds(null, superOperator);

			console.log(obj.data);
			EventBus.send('time_sync', {
				start_t: obj.data.time_tMin,
				time_t: obj.data.time_tMin,
				end_t: obj.data.time_tMax
			});

			EventBus.send('get_bounds');
		},
		error: function(e) {
			EventBus.send("error", {status: "SensApp "+e.status, message: e.statusText});
		}});


};

SensAppStorage.prototype = {

bounds: function()
{
	var r = $.extend({}, this.data);
	delete r.data;
	return r;
},

time_sync: function(start_t, end_t)
{
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
	return {
		time_t: data.time_t.subarray(begin_filtered_data, end_filtered_data),
		value: data.value.subarray(begin_filtered_data, end_filtered_data)
	};
}
};