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
		url: additional_data.numerical+'?limit=20000',
		success: function(json){
			console.log(json);

			var time_incremment = (typeof json.bt === 'undefined') ? 0 : json.bt;

			for (var i = 0; i < json.e.length; ++i)
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