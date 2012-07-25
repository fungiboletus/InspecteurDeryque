var DCarte = function(screen)
{
	if (window['google'] == undefined || google['maps'] == undefined) return alert('Google maps library is required');
	this.screen = screen;

	// If we have nothing to display, display a heart island
	this.default_location = new google.maps.LatLng(43.978487,15.383574);
	var options = {
		center: this.default_location,
		zoom:16,
		mapTypeId: google.maps.MapTypeId.SATELLITE,
		scaleControl: true
	};

	this.map = new google.maps.Map(screen, options);

	EventBus.addListeners(this.listeners, this);

	this.database = new Object();
	this.markers = [];
};

DCarte.prototype =
{
	listeners: {
		new_tuples: function(detail, obj) {
			if (!(detail.statement_name in obj.database)) return;

			var base = obj.database[detail.statement_name];
			// For each new tuple
			var data = detail.data;
			for (var i = 0; i < data.length; ++i) {

				var point = data[i];
				var ll = new google.maps.LatLng(point.lat, point.lon);

				// We dont draw a line if we don't have two point or more
				if (base.last_point === null)
				{

					// var pinColor = "FFFFFF";
	  		// 		var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
			  //       new google.maps.Size(21, 34),
			  //       new google.maps.Point(0,0),
			  //       new google.maps.Point(10, 34));
					// Create begin
					base.marker = new google.maps.Marker({
						position:ll,
						// icon: pinImage,
						map: obj.map,
						title: "begin"});
				}
				else
				{
					var distance = obj.distance(base.last_point.lat(), base.last_point.lng(),
							 ll.lat(), ll.lng());
					var diff_t = point.time_t - base.last_time;
					var speed = distance/diff_t * 3600.0;

					// TODO véritable gestion des couleurs, avec légende
					var color = (280.0-speed * 20.0) % 360.0;
					if (color < 0.0) color += 360.0;
					var lum = 50;
					if (speed < 2.0) lum = 20;
					else if (speed > 20) lum = 80;
					// console.log(speed * 3.6);
					// console.log(color);
					var line = new google.maps.Polyline({
						path: [base.last_point, ll],
						strokeColor: "hsl("+color+", 50%, "+lum+"%)",
						strokeWeight: 4,
						visible: false,
						map: obj.map
					});
					line.time_t = point.time_t;
					base.lines.push(line);

					google.maps.event.addListener(line, 'click', (function(taaame){
							return function() {
								EventBus.send('tuples_selected', {statement_name: detail.statement_name,
									tuples: [taaame]});
								EventBus.send('time_sync', {time_t: taaame.time_t});
							}
						})(point));
					// line.setMap(obj.map);
					// base.marker.setPosition(ll);
				}
				base.last_point = ll;
				base.last_time = point.time_t;
			}
		},
		add_statement: function(e, obj) {
			if (e.box_name != self.name) return;

			if (!(e.statement_name in obj.database))
			{
				var new_obj = new Object();
				new_obj.last_point = null;
				new_obj.last_time = null;
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
			obj.map.setCenter(obj.default_location);
		},
		time_sync: function(d, obj) {
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
		},
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