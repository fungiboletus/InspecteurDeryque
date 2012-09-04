/* This file is released under the CeCILL-B V1 licence.*/

var SensAppStorage = function(superOperator, statement_name, resume)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;
	this.resume = resume;
	this.load_finished = false;

	this.additional_data = resume.additional_data;

	// console.log(resume);
	var obj = this;

	this.load_json(function(data)
	{
		obj.data = data;

		obj.load_finished = true;
		obj.finished_events();

		// Real time when the first load is finished
		EventBus.addListener('rt_clock', obj.rt_clock, obj);
	});
};

SensAppStorage.prototype =
{
bounds: SuperOperator.prototype.super_bounds,
time_sync: SuperOperator.prototype.super_time_sync,
finished_events: SuperOperator.prototype.super_finished_events,

load_json: function(end_callback, from)
{
	// Create the structure of the data object
	var data = {data: {time_t: null}};

	var obj = this;
	for (var dynamic_key in this.additional_data)
	(function(key) {
		data.data[key] = null;

		var url = obj.additional_data[key]+'?sorted=asc&limit=500';
		if (typeof from !== 'undefined') url += '&from='+from;

		$.ajax({
			url: url,
			dataType: 'json',
			success: function(json){

				var r = obj.interpret_json(json, data);

				data.data[key] = r.data;
				data[key+'Min'] = r.min;
				data[key+'Max'] = r.max;
				data.count = data.data.time_t.length;

				// If this is the last statement to be loaded
				var last = true;

				// If a null value is still present, this isn't the last statement
				for (var subkey in data.data)
					if (data.data[subkey] === null)
						last = false;

				if (last)
				{
					// Define time bounds
					data.time_tMin = data.data.time_t[0];
					data.time_tMax = data.data.time_t[data.data.time_t.length -1];

					// Call callback
					end_callback(data);
				}
			},
			error: function(e) {
				EventBus.send("error", {
					status: e.status + ' : ' + e.statusText,
					message: 'Error when loading SensApp sensor <strong>'
					+ statement_name + ' : ' + key + '</strong><br/><span class="mono">'+this.url+'</span>'});
			}});
	})(dynamic_key);

},

interpret_json: function(json, data)
{
	if (typeof json.e === 'undefined' || json.e.length === 0)
		return;

	var nb_e = json.e.length;

	// 64 bits baby !
	var size = nb_e * 8;

	// If we need to create a new time_t
	// If the new length is bigger than the previous, undefined values
	// will be get for out of range data from other parts (it's a javascript array)
	var time_t_creation = (data.data.time_t === null || data.data.time_t.byteLength < size);

	if (time_t_creation)
	{
		var time_buffer = new ArrayBuffer(size);
		var time_array = new Float64Array(time_buffer);
		data.data.time_t = time_array;
		var time_incremment = (typeof json.bt === 'undefined') ? 0 : json.bt;
	}

	var value_buffer = new ArrayBuffer(size);
	var value_array = new Float64Array(value_buffer);

	var min = Number.MAX_VALUE;
	var max = -Number.MAX_VALUE;

	if (nb_e > 0)
	{
		// v, sv or bv ?
		var method = (typeof json.e[0].v !== 'undefined') ? this.extract_v :
						(typeof json.e[0].sv !== 'undefined') ? this.extract_sv :
						(typeof json.e[0].bv !== 'undefined') ? this.extract_bv :
						this.extract_default;

		for (var i = 0; i < nb_e; ++i)
		{
			var ei = json.e[i];

			if (time_t_creation)
				time_array[i] = (ei.t + time_incremment);// * 1000.0;

			var value = method(ei, this);//42.0+i;//ei[senml_key];
			value_array[i] = value;

			if (value > max)
				max = value;
			if (value < min)
				min = value;
		}
	}

	return {
		data: value_array,
		min: min,
		max: max
	};
},

extract_v: function(e)
{
	return e.v;
},

extract_bv: function(e)
{
	return e.bv ? 1.0 : 0.0;
},

extract_default: function(e)
{
	return 0.0;
},

extract_sv: function(e, obj)
{
	// TODO prendre en compte les autres méthodes ?
	return obj.degreeToDouble(e.sv);
},

degreeToDouble: function(degree) {

	var split=degree.split(" ");

	if(typeof split[1] === 'undefined') {
		split[1]=0;
	}
	if(typeof split[2] === 'undefined') {
		split[2]=0;
	}
	if(typeof split[3] === 'undefined') {
		split[3]=0;
	}
	var doubleValue=parseFloat(split[1])+parseFloat(split[2])/60+parseFloat(split[3])/3600;
	if(split[0]==="S" || split[0]==="W") {
		doubleValue = -doubleValue;
	}
	return doubleValue;
},

rt_clock: function(d, obj)
{

	// Get the timestamp to the last data, +1 because the from argument is inclusive
	var timestamp = obj.data.time_tMax+1;

	obj.load_json(function(data)
	{
		// ugly

		obj.data = data;
		obj.finished_events();
		console.log(data);
		// // Define time bounds
		// data.time_tMin = data.data.time_t[0];
		// data.time_tMax = data.data.time_t[data.data.time_t.length -1];
		// obj.data = data;

		// obj.load_finished = true;
		// obj.finished_events();
	}, timestamp);
}

};
