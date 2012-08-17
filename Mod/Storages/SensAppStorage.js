var SensAppStorage = function(superOperator, statement_name, resume)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;
	this.resume = resume;

	var additional_data = resume.additional_data;

	this.data = {
		data: []
	};

	// console.log(resume);
	var obj = this;

	$.ajax({
		url: additional_data.numerical,//+'?limit=20000',
		success: function(json){
			console.log(json);

			var time_incremment = (typeof json.bt === 'undefined') ? 0 : json.bt;

			for (var i = 0; i < json.e.length && i < 20000; ++i)
			{
				var ei = json.e[i];
				obj.addTuple({
					time_t: new Date((ei.t + time_incremment) * 1000),
					value: ei.v
				});
			}

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
	var begin = 0, end = data.length, old_m = -1, m = -1;

	do {
		m = parseInt(begin + (end-begin)/2);
		var t = data[m].time_t;
		if (old_m === m || t == start_t)
			break;
		else if (t < start_t)
			begin = m + 1;
		else
			end = m -1;
		old_m = m;
	} while (begin < end);

	var begin_filtered_data = m;

	begin = 0; end = data.length; old_m = -1; m = -1;

	do {
		m = parseInt(begin + (end-begin)/2);
		var t = data[m].time_t;
		if (old_m === m || t == end_t)
			break;
		else if (t < end_t)
			begin = m + 1;
		else
			end = m -1;
		old_m = m;
	} while (begin < end);

	var end_filtered_data = m;

	// Expecting that the native slice function is fast
	return data.slice(begin_filtered_data, end_filtered_data + 1);
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