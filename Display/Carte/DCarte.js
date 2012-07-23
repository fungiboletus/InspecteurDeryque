var DCarte = function(screen)
{
	if (window['google'] == undefined || google['maps'] == undefined) return alert('Google maps library is required');
	this.screen = screen;

	this.default_location = new google.maps.LatLng(43.978487,15.383574);
	var options = {
		center: this.default_location,
		zoom:16,
		mapTypeId: google.maps.MapTypeId.SATELLITE,
		scaleControl: true
	};

	this.map = new google.maps.Map(screen, options);

	EventBus.addListeners(this.listeners, this);

	this.last_point = null;
	this.last_time = null;
	this.marker = null;
};

DCarte.prototype =
{
	listeners: {
		new_tuples: function(detail, obj) {
			var data = detail.data;
			for (var i = 0; i < data.length; ++i) {

				var point = data[i];
				var ll = new google.maps.LatLng(point.lat, point.lon);

				if (obj.last_point === null)
				{

					// var pinColor = "FFFFFF";
	  		// 		var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
			  //       new google.maps.Size(21, 34),
			  //       new google.maps.Point(0,0),
			  //       new google.maps.Point(10, 34));
					// Create begin
					obj.marker = new google.maps.Marker({
						position:ll,
						// icon: pinImage,
						map: obj.map,
						title: "begin"});
				}
				else
				{
					var distance = obj.distance(obj.last_point.lat(), obj.last_point.lng(),
							 ll.lat(), ll.lng());
					var diff_t = point.time_t - obj.last_time;
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
						path: [obj.last_point, ll],
						strokeColor: "hsl("+color+", 50%, "+lum+"%)",
						strokeWeight: 3
					});
					line.setMap(obj.map);
					obj.marker.setPosition(ll);
				}
				if (!obj.map.getBounds().contains(ll))
					obj.map.setCenter(ll);
				obj.last_point = ll;
				obj.last_time = point.time_t;
			}
		},
		add_statement: function(e) {

		},
		del_statement: function(e) {

		},
		layout_change: function(d, obj) {
			obj.map.setCenter(obj.default_location);
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