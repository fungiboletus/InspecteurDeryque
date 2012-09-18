/* This file is released under the CeCILL-B V1 licence.*/

var DMap = function(screen)
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
	this.lines = {};

	this.max_nb_points = 512;
};

DMap.prototype =
{
listeners: {
tuples: function(detail, obj) {

	for (var key in obj.lines)
		obj.lines[key].updated = false;

	for (var statement_name in detail) {
		if (!(statement_name in obj.database)) continue;


		var base = obj.database[statement_name];
		// For each new tuple
		var data = detail[statement_name];

		var nb_data = data.time_t.length;

		var sampling = Math.ceil(nb_data / obj.max_nb_points);

		if (nb_data > 0) {
			// var pinColor = "FFFFFF";
			// var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
		 	// new google.maps.Size(21, 34),
		 	// new google.maps.Point(0,0),
		 	// new google.maps.Point(10, 34));
			// Create begin

			// If it's not GPS, we have nothing to do here
			if (!('lat' in  data) || !('lon' in data))
			{
				EventBus.send('error', {
					status: 'Bad statement type',
					message: 'The map can only show GPS statements'});
				continue;
			}

			var ll = new google.maps.LatLng(data.lat[0], data.lon[0]);

			if (!base.marker)
			{
				base.marker = new google.maps.Marker({
					position:ll,
					// icon: pinImage,
					map: obj.map,
					title: "Cursor"});
			}
		}

		var i = nb_data % sampling - 1;
		if (i < 1) i += 2;

		for (; i < nb_data && i < 5000; i+=sampling) {
			var ll = new google.maps.LatLng(data.lat[i], data.lon[i]);
			var ll2 = new google.maps.LatLng(data.lat[i-sampling], data.lon[i-sampling]);

			var key = ll+":"+ll2;

			if (obj.lines[key]) {
				obj.lines[key].updated = true;
			}
			else
			{
				var distance = obj.distance(ll2.lat(), ll2.lng(),
						 ll.lat(), ll.lng());
				var diff_t = data.time_t[i] - data.time_t[i-sampling];
				var speed = distance/diff_t * 3.6;

				// TODO véritable gestion des couleurs, avec légende
				var color = (280.0-speed * 20.0) % 360.0;
				if (color < 0.0) color += 360.0;
				var lum = 50;
				if (speed < 2.0) lum = 20;
				else if (speed > 20) lum = 80;
				// console.log(speed * 3.6);
				// console.log(color);
				var line = new google.maps.Polyline({
					path: [ll, ll2],
					strokeColor: "hsl("+color+", 50%, "+lum+"%)",
					strokeWeight: 4,
					// visible: false,
					map: obj.map
				});
				line.updated = true;
				obj.lines[key] = line;

				google.maps.event.addListener(line, 'click', (function(time_t){
						return function() {
							EventBus.send('cursor', {time_t: time_t});
						}
					})(data.time_t[i]));
			}

			// line.setMap(obj.map);
			// base.marker.setPosition(ll);
		}
	}

	for (var key in obj.lines)
	{
		if (!obj.lines[key].updated)
		{
			obj.lines[key].setMap(null);
			delete obj.lines[key];
		}
	}
},
add_statement: function(e, obj) {
	if (e.box_name != self.name) return;

	if (!(e.statement_name in obj.database))
	{
		var new_obj = new Object();
		new_obj.marker = null;
		new_obj.lines = [];
		obj.database[e.statement_name]  = new_obj;
	}
},
del_statement: function(e, obj) {
	if (e.box_name != self.name) return;

	if (e.statement_name in obj.database)
		delete obj.database[e.statement_name];
},
size_change: function(d, obj) {
	if (obj.database.length == 0)
		obj.map.setCenter(obj.default_location);

	// The map need to fit to the bounds when the size change
	// is heavy
	EventBus.send('get_bounds');
},
bounds: function(d, obj)
{
	var updated = false;
	var bounds = new google.maps.LatLngBounds();

	for (var local_statement in obj.database)
	{
		if (local_statement in d)
		{
			var v = d[local_statement];
			// If it's about gps bounds
			if ('latMin' in v && 'latMax' in v && 'lonMin' in v && 'lonMax' in v)
			{
				updated = true;
				bounds.extend(new google.maps.LatLng(v.latMin, v.lonMax));
				bounds.extend(new google.maps.LatLng(v.latMax, v.lonMin));
			}
		}
	}

	if (updated)
		obj.map.fitBounds(bounds);

},
values: function(d, obj)
{
	var bounds = new google.maps.LatLngBounds();
	var need_to_fit = false;

	for (var statement_name in d) {
		if (!(statement_name in obj.database)) continue;

		var base = obj.database[statement_name];
		var data = d[statement_name];

		if (('lat' in  data) && ('lon' in data))
		{
			var ll = new google.maps.LatLng(data.lat, data.lon);
			base.marker.setPosition(ll);
			bounds.extend(ll);

			// Center the map if the markers is not in the map
			if (!obj.map.getBounds().contains(ll))
				need_to_fit = true;

		}
	}

	if (need_to_fit)
		obj.map.panToBounds(bounds);

},
/*time_sync: function(d, obj) {
	var time = d.time_t;
	for (var statement in obj.database)
	{
		var base = obj.database[statement];
		if (base.lines.length === 0) continue;

		var diff = Number.MAX_VALUE;
		var best_point = null;

		for (var i = 0; i < base.lines.length; ++i)
		{
			var point = base.lines[i];
			var tmp_diff = Math.abs(point.time_t - time);
			if (tmp_diff <= diff)
			{
				diff = tmp_diff;
				best_point = point;
			}

			if (d.start_t && d.end_t)
			{
				var visibility = point.time_t >= d.start_t && point.time_t <= d.end_t;
				if (point.getVisible() != visibility)
					point.setVisible(visibility);
			}
			// var opacity = 1.0;
			// if (tmp_diff > intervalle) {
			// 	if (tmp_diff < (intervalle + intervalle))
			// 		opacity = 1.0-tmp_diff/(intervalle*1.75);
			// 	else
			// 		opacity = 0.2;
			// }
			// point.setOptions({strokeOpacity: opacity});
		}

		// if (diff < intervalle)
		// {
			var ll = best_point.getPath().getAt(1);
			base.marker.setPosition(ll);
			base.marker.setVisible(true);
			if (!obj.map.getBounds().contains(ll))
				obj.map.setCenter(ll);
		// }
		// else
		// 	base.marker.setVisible(false);
	}
},*/
tuples_selected: function(d, obj) {
	if (!(d.statement_name in obj.database)) return;

	var tuples = d.tuples;

	var options = {map: obj.map};

	for (var i = tuples.length; i < obj.markers.length; ++i)
		obj.markers[i].setVisible(false);

	for (var i = obj.markers.length; i < tuples.length; ++i)
		obj.markers[i] = new google.maps.Marker(options);

	for (var i = 0; i < tuples.length; ++i)
	{
		obj.markers[i].setVisible(true);
		var ll = new google.maps.LatLng(tuples[i].lat, tuples[i].lon);
		obj.markers[i].setPosition(ll);
	}

}
},

/**
 * Distance entre deux points de coordonées géographiques.
 *
 * Il s'agit de formules adaptées à partir du logiciel libre JOSM, un
 * éditeur de cartes OpenStreetMap en Java. Cette méthode a été codée
 * dans le cadre du projet Devint pour le jeu PolyRallye, et elle se
 * retrouve ici adaptée en Javascript.
 *
 * La méthode utilisée est celle de Haversine.
 *
 * @return Distance en mètres.
 */
distance: function(latA, lonA, latB, lonB) {
	var R = 6378137.0;
	var sinHalfLat = Math.sin((latB - latA).toRadians() / 2.0);
	var sinHalfLon = Math.sin((lonB - lonA).toRadians() / 2.0);
	var d = 2.0
			* R
			* Math.asin(Math.sqrt(sinHalfLat * sinHalfLat
					+ Math.cos(latA.toRadians())
					* Math.cos(latB.toRadians()) * sinHalfLon
					* sinHalfLon));

	// For points opposite to each other on the sphere,
	// rounding errors could make the argument of asin greater than 1
	// (This should almost never happen.)
	if (isNaN(d)) {
		EventBus.send('log', "Error: NaN in DMap.distance");
		d = Math.PI * R;
	}
	return d;
}

};
