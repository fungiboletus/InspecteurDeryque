var DCarte = function(screen)
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
};

DCarte.prototype =
{
listeners: {
tuples: function(detail, obj) {

	for (var key in obj.lines)
		obj.lines[key].updated = false;

	var bounds = new google.maps.LatLngBounds();

	for (var statement_name in detail) {
		if (!(statement_name in obj.database)) continue;

		var base = obj.database[statement_name];
		// For each new tuple
		var data = detail[statement_name];


		if (data.length > 0) {
			// var pinColor = "FFFFFF";
			// var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
		 	// new google.maps.Size(21, 34),
		 	// new google.maps.Point(0,0),
		 	// new google.maps.Point(10, 34));
			// Create begin

			var ll = new google.maps.LatLng(data[0].lat, data[0].lon);

			if (base.marker) {
				base.marker.setPosition(ll);
			}
			else
			{
				base.marker = new google.maps.Marker({
					position:ll,
					// icon: pinImage,
					map: obj.map,
					title: "begin"});
			}

			bounds.extend(ll);
		}

		for (var i = 1; i < data.length; ++i) {

			var last_point = data[i-1];
			var point = data[i];
			var ll = new google.maps.LatLng(point.lat, point.lon);
			var ll2 = new google.maps.LatLng(last_point.lat, last_point.lon);

			var key = ll+":"+ll2;

			if (obj.lines[key]) {
				obj.lines[key].updated = true;
			}
			else
			{
				var distance = obj.distance(last_point.lat, last_point.lng,
						 point.lat, point.lng);
				var diff_t = point.time_t - last_point.time_t;
				var speed = distance/diff_t * 36.0;

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

				// google.maps.event.addListener(line, 'click', (function(taaame){
				// 		return function() {
				// 			EventBus.send('tuples_selected', {statement_name: detail.statement_name,
				// 				tuples: [taaame]});
				// 			EventBus.send('time_sync', {time_t: taaame.time_t});
				// 		}
				// 	})(point));
			}

			bounds.extend(ll);
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

	obj.map.fitBounds(bounds);
	obj.map.panToBounds(bounds);
	// obj.map.panToBounds(bounds);
	// google.maps.event.addListenerOnce(obj.map, 'idle', function() {
 //  		obj.map.fitBounds(bounds);
	// });
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
layout_change: function(d, obj) {
	if (obj.database.length == 0)
		obj.map.setCenter(obj.default_location);
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
		EventBus.send('log', "Error: NaN in DCarte.distance");
		d = Math.PI * R;
	}
	return d;
}

};