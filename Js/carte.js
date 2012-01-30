	function initialiser() {
		var tables = document.getElementById("data").getElementsByTagName("table");
		var trs,i,j;
		var timestamp = [];
		var lat = [];
		var lon = [];
		var vit = [];
		for(i=0;i<tables.length;i++){
			trs=tables[i].getElementsByTagName("tr");
			var dataintr = trs[0].getElementsByTagName("td");
			for(j=0;j<dataintr.length;j++){
				timestamp.push(parseFloat(dataintr[j].innerHTML));
			}

			dataintr = trs[1].getElementsByTagName("td");
			for(j=0;j<dataintr.length;j++){
				lat.push(parseFloat(dataintr[j].innerHTML));
			}
			
			dataintr = trs[2].getElementsByTagName("td");
			for(j=0;j<dataintr.length;j++){
				lon.push(parseFloat(dataintr[j].innerHTML));
			}
		}
		var lastlat = 0, lastlon = 0, firstlat = 0, firstlon = 0;
		if (lat.length > 0 && lon.length == lat.length)
		{
			lastlat = lat[lat.length-1];
			lastlon = lon[lon.length-1];
			firstlat = lat[0];
			firstlon = lon[0];
		}
		var latlng = new google.maps.LatLng((firstlat+lastlat)/2, (lastlon+firstlon)/2);
		var options = {
			center: latlng,
			zoom:2,
			mapTypeId: google.maps.MapTypeId.SATELLITE,
			scaleControl: true
		};
		var carte = new google.maps.Map(document.getElementById("carte"), options);
		var maxvit = 0;
		var minvit = Number.MAX_VALUE;
		for(i=1;i<lat.length;i++){
			var dist = Math.sqrt(Math.pow((lat[i]-lat[i-1]),2) + Math.pow((lon[i]-lon[i-1]),2))
			var tps = timestamp[i] - timestamp[i-1];
			if (maxvit < dist /tps)
				maxvit = dist /tps;
			if (minvit > dist /tps)
				minvit = dist /tps;
			vit.push(dist/tps);
		}
		
		//polyligne
		for(i=1;i<lat.length;i++){
			var parcours = [];
			parcours.push(new google.maps.LatLng(lat[i-1], lon[i-1]));
			parcours.push(new google.maps.LatLng(lat[i], lon[i]));
			var ratio = (vit[i-1]-minvit)/(maxvit-minvit);
			var red = Math.floor(255 * ratio);
			var blue = Math.floor(255 * (1 - ratio));
			red = red.toString(16);
			blue = blue.toString(16);
			if (red == "0")
				red = "00";
			if (blue == "0")
				blue = "00";
			var color = "#" + red.toUpperCase() + "00" + blue.toUpperCase();
			var traceparcours = new google.maps.Polyline({
				path: parcours,
				strokeColor: color,
				strokeOpacity: 1.0,
				strokeWeight: 2
			});
		
			traceparcours.setMap(carte);
		}
		var dep = new google.maps.LatLng(firstlat, firstlon);
		var arr = new google.maps.LatLng(lastlat, lastlon);
		var marqueur1 = new google.maps.Marker({
			position: dep,
			map: carte,
			title: "Départ"
		});
		var marqueur2 = new google.maps.Marker({
			position: arr,
			map: carte,
			title: "Arrivée"
		});
		var markerBounds = new google.maps.LatLngBounds();
		markerBounds.extend(dep);
		markerBounds.extend(arr);
		carte.fitBounds(markerBounds);
	}

	window.onload = initialiser;
