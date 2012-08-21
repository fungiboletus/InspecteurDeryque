var DHeatMap = function(screen)
{
	var map = newDom('div');
	map.id = 'map';
	screen.appendChild(map);

	if (window['google'] == undefined || google['maps'] == undefined) return alert('Google maps library is required');

	// If we have nothing to display, display a heart island
	this.default_location = new google.maps.LatLng(43.978487,15.383574);
	var options = {
		center: this.default_location,
		zoom:16,
		mapTypeId: google.maps.MapTypeId.SATELLITE,
		scaleControl: true
	};

	this.map = new google.maps.Map(map, options);

	EventBus.addListeners(this.listeners, this);

	this.database = {};

	this.heatmap_layer = new google.maps.visualization.HeatmapLayer(
	{
		map: this.map,
		opacity: 0.8,
		radius: 10
	});
};

DHeatMap.prototype =
{
listeners: {
tuples: function(detail, obj) {

	var bounds = new google.maps.LatLngBounds();

	var mvcarray = new google.maps.MVCArray();
	var updated = false;

		// For each new tuple
	for (var statement_name in detail) {
		if (!(statement_name in obj.database)) continue;

		var data = detail[statement_name];

		var nb_data = data.time_t.length;

		if (nb_data > 0) {

			// If it's not GPS, we have nothing to do here
			if (!('lat' in  data) || !('lon' in data))
			{
				EventBus.send('error', {
					status: 'Bad statement type',
					message: 'The map can only show GPS statements'});
				continue;
			}

			updated = true;
		}

		for (var i = 0; i < nb_data; ++i)
		{
			var ll = new google.maps.LatLng(data.lat[i], data.lon[i]);
			mvcarray.push(ll);
			bounds.extend(ll);
		}
	}

	if (updated)
	{
		obj.heatmap_layer.setData(mvcarray);
		obj.map.fitBounds(bounds);
		// obj.map.panToBounds(bounds);
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
},
size_change: function(d, obj) {
	if (obj.database.length == 0)
		obj.map.setCenter(obj.default_location);
}

}};