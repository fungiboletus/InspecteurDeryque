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
	}, undefined);
};

SensAppStorage.prototype =
{
bounds: SuperOperator.prototype.super_bounds,
time_sync: SuperOperator.prototype.super_time_sync,
finished_events: SuperOperator.prototype.super_finished_events,

load_json: function(end_callback, from, limit)
{
	// Create the structure of the data object
	var data = {data: {time_t: null}};

	var obj = this;
	for (var dynamic_key in this.additional_data)
	(function(key) {
		data.data[key] = null;

		var url = obj.additional_data[key]+'?sorted=asc';
		if (typeof from !== 'undefined') url += '&from='+from;
		if (typeof limit !== 'undefined') url += '&limit='+limit;

		obj.superOperator.ajax(
			url,
			function(json){

				var r = obj.interpret_json(json, data);

				if (r)
				{
					data.data[key] = r.data;
					var min = r.min;
					var max = r.max;
					var count = data.data.time_t.length;
				}
				else
				{
					data.data[key] = new Float64Array(new ArrayBuffer(0));
					var min = 0;
					var max = 0;
					var count = 0;
				}

				if (typeof data.count !== 'undefined')
					data.count = Math.min(count, data.count);
				else
					data.count = count;

				var keyMin = key+'Min';
				if ((typeof data[keyMin] === 'undefined') || min < data[keyMin])
					data[keyMin] = min;

				var keyMax = key+'Max';
				if ((typeof data[keyMax] === 'undefined') || max > data[keyMax])
					data[keyMax] = max;

				// If this is the last statement to be loaded
				var last = true;

				// If a null value is still present, this isn't the last statement
				for (var subkey in data.data)
					if (data.data[subkey] === null)
						last = false;

				if (last)
				{
					// Define time bounds
					if (data.count > 0)
					{
						data.time_tMin = data.data.time_t[0];
						data.time_tMax = data.data.time_t[data.count -1];
					}
					else
					{
						data.time_tMin = 0;
						data.time_tMax = 0;
					}

					// Call callback
					end_callback(data);
				}
			},
			function(e) {
				EventBus.send("error", {
					status: e.status + ' : ' + e.statusText,
					message: 'Error when loading SensApp sensor <strong>'
					+ obj.statement_name + ' : ' + key + '</strong><br/><span class="mono">'+this.url+'</span>'});
			});
	})(dynamic_key);

},

interpret_json: function(json, data)
{
	if (typeof json.e === 'undefined' || json.e.length === 0)
		return false;

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
		// the interval of the fetched data
		var interval = data.time_tMax - data.time_tMin;

		var nb_new_data = data.count;
		var nb_old_data = obj.data.count;

		// If the fetched data interval contain the requested interval (rare)
		if (nb_new_data >= nb_old_data)
		{
			console.log("super ça rentre");
			obj.data = data;
		}
		else
		{

			// epic shift and hard copy
			for (key in obj.data.data)
			{
				obj.data.data[key].set(obj.data.data[key].subarray(nb_old_data - nb_new_data));
				obj.data.data[key].set(data.data[key], obj.data.data[key].length - nb_new_data);

				// redefine bounds
				var keyMin = key+'Min';
				if (data[keyMin] < obj.data[keyMin])
					obj.data[keyMin] = data[keyMin];

				var keyMax = key+'Max';
				if (data[keyMax] < obj.data[keyMax])
					obj.data[keyMax] = data[keyMax];
			}

			// time bounds
			obj.data.time_tMin = obj.data.data.time_t[0];
			obj.data.time_tMax = obj.data.data.time_t[nb_old_data - 1];

			// like finished_events, send good events
			// EventBus.send
		}

		// ugly
		console.log(d);
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
