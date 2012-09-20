/* This file is released under the CeCILL-B V1 licence.*/

/**
 *	Video storage (a specific case)
 */
var VideoStorage = function(superOperator, statement_name, resume)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;
	this.resume = resume;

	var additional_data = resume.additional_data;

	// console.log(resume);

	// The resume is enough for sending the values
	var obj = this;

	this.start_t = additional_data.start_t;

	// TODO : this is ugly
	this.end_t = this.start_t + 90;

	// Specific event for this use (a video is very different
	// than a list of tuples with time
	EventBus.send('video', {
		statement_name: statement_name,
		location: additional_data.location,
		start_t: obj.start_t
	});

	this.load_finished = false;

	// Don't try to understand pleaase
	window.setTimeout(function(){
		this.load_finished = true;
		obj.finished_events();
	}, 1);

};

VideoStorage.prototype =
{
// This functions are present because they are called by the SuperOperator
// but they do nothing…
bounds: function() {
	return {time_tMin: this.start_t, time_tMax: this.end_t};
},
rt_clock: function() {},
// finished_event: function() {},
finished_events: function() {
	this.superOperator.listeners.get_bounds(null, this.superOperator);
	EventBus.send('time_sync', {
		start_t: this.start_t,
		end_t: this.end_t
	});
	EventBus.sendDelayed('get_bounds');
},
time_sync: function() {},
cursor: function() {}
};
