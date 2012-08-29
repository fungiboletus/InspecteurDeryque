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
};

DStreetView.prototype =
{
listeners: {
tuples: function(detail, obj) {

	for (var statement_name in detail) {
		if (!(statement_name in obj.database)) continue;
		var data = detail[statement_name];

		var nb_data = data.time_t.length;

		if (nb_data > 0) {

			// If it's not GPS, we have nothing to do here
			if (!('lat' in  data) || !('lon' in data))
			{
				if ('heading' in data)
				{
					obj.panorama.setPov({
						heading: data.heading[0] * 180.0 / Math.PI,
						zoom:1,
						pitch:0
					});
				}
				else
				{
					EventBus.send('error', {
						status: 'Bad statement type',
						message: 'The map can only show GPS statements'});
				}
				continue;
			}

			var ll = new google.maps.LatLng(data.lat[0], data.lon[0]);
			obj.panorama.setPosition(ll);
		}
	}
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
}
}};
