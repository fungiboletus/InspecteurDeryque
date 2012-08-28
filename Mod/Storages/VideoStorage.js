var VideoStorage = function(superOperator, statement_name, resume)
{
	this.superOperator = superOperator;
	this.statement_name = statement_name;
	this.resume = resume;

	var additional_data = resume.additional_data;

	console.log(resume);

	var obj = this;
	EventBus.send('video', {
		statement_name: statement_name,
		location: additional_data,
		start_t: +10
	});


};

VideoStorage.prototype =
{
bounds: function() {},
time_sync: function() {}
};
