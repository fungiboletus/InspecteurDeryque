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

	// Specific event for this use (a video is very different
	// than a list of tuples with time
	EventBus.send('video', {
		statement_name: statement_name,
		location: additional_data,
		start_t: 1337853600 + 10
	});


};

VideoStorage.prototype =
{
// This functions are present because they are called by the SuperOperator
// but they do nothing…
bounds: function() {},
rt_clock: function() {},
finished_event: function() {},
time_sync: function() {},
cursor: function() {}
};
