var SensAppStorage = function(superOperator, statement_name, resume)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;
	this.resume = resume;
	this.load_finished = false;

	var additional_data = resume.additional_data;

	// console.log(resume);
	var obj = this;

	// Create the structure of the data object
	var data = {data: {time_t: null}};

	for (var dynamic_key in additional_data)
	(function(key) {
		data.data[key] = null;
		$.ajax({
			url: additional_data[key]+'?sorted=asc',
			dataType: 'json',
			success: function(json){

				var nb_e = json.e.length;

				if (nb_e === 0)
					alert('todo');

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
					var method = (typeof json.e[0].v !== 'undefined') ? obj.extract_v :
									(typeof json.e[0].sv !== 'undefined') ? obj.extract_sv :
									(typeof json.e[0].bv !== 'undefined') ? obj.extract_bv :
									obj.extract_default;

					for (var i = 0; i < nb_e; ++i)
					{
						var ei = json.e[i];

						if (time_t_creation)
							time_array[i] = (ei.t + time_incremment);

						var value = method(ei, obj);//42.0+i;//ei[senml_key];
						value_array[i] = value;

						if (value > max)
							max = value;
						if (value < min)
							min = value;
					}
				}

				data.data[key] = value_array;
				data[key+'Min'] = min;
				data[key+'Max'] = max;

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
					obj.data = data;

					obj.load_finished = true;
					obj.finished_events();
				}

			},
			error: function(e) {
				EventBus.send("error", {
					status: e.status + ' : ' + e.statusText,
					message: 'Error when loading SensApp sensor <strong>'
					+ statement_name + ' : ' + key + '</strong><br/><span class="mono">'+this.url+'</span>'});
			}});
	})(dynamic_key);


};

SensAppStorage.prototype =
{
bounds: SuperOperator.prototype.super_bounds,
time_sync: SuperOperator.prototype.super_time_sync,
finished_events: SuperOperator.prototype.super_finished_events,

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
}
};
