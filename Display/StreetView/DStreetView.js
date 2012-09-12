/* This file is released under the CeCILL-B V1 licence.*/

var DStreetView = function(screen)
{
	var view = newDom('div');
	view.id = 'view';
	screen.appendChild(view);

	if (window['google'] == undefined || google['maps'] == undefined) return alert('Google maps library is required');

	this.panorama = new google.maps.StreetViewPanorama(view);

	EventBus.addListeners(this.listeners, this);

	this.database = {};

	// Last point
	this.last_point = null;
};

DStreetView.prototype =
{
listeners: {
values: function(detail, obj) {

	var current_point = null;
	var current_heading = null;

	for (var statement_name in detail) {
		if (!(statement_name in obj.database)) continue;
		var data = detail[statement_name];

		if ('heading' in data)
		{
			current_heading = data.heading * 180.0 / Math.PI;
		}
		else if (('lat' in  data) && ('lon' in data))
		{
			var ll = new google.maps.LatLng(data.lat, data.lon);
			obj.panorama.setPosition(ll);

			if (current_point !== null)
			{
				EventBus.send('error', {
					status: 'Too much GPS data',
					message: 'The StreetView visualization can display'
					+'only one GPS statement'});
			}

			current_point = ll;
		}
		else
		{
			EventBus.send('error', {
				status: 'Bad statement type',
				message: 'The map can only show GPS statements'});
		}
	}

	// If a heading is not present, create a bad heading with
	// the last fetched value
	if (current_heading !== null &&
		current_point !== null && obj.last_point !== null)
		current_heading = google.maps.geometry.spherical.computeHeading(
			obj.last_point, current_point);

	obj.panorama.setPov({
		heading: (current_heading === null ? 0.0 : current_heading),
		zoom:1,
		pitch:0
	});

	this.last_point = current_point;
},
add_statement: function(e, obj) {
	if (e.box_name != self.name) return;

	if (!(e.statement_name in obj.database))
			obj.database[e.statement_name] = true;
},
del_statement: function(e, obj) {
	if (e.box_name != self.name) return;

	if (e.statement_name in obj.database)
		delete obj.database[e.statement_name];

	this.have_heading = false;
	this.last_point = null;
}
}};
