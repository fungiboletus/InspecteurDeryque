var SensAppStorage = function(superOperator, statement_name, resume)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;
	this.resume = resume;

	var additional_data = resume.additional_data;

	this.data = {
		data: []
	};

	console.log(resume);
	var obj = this;

	$.ajax({
		url: additional_data.numerical,
		success: function(){

		},
		error: function(e) {
			EventBus.send("error", {status: "SensApp "+e.status, message: e.statusText});
		}});


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