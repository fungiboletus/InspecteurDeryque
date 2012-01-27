	function initialiser() {
		var latlng = new google.maps.LatLng((46.781367900048+46.778605381016)/2, (6.6401992834884+6.6588674582321)/2);
		var options = {
			center: latlng,
			zoom: 16,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scaleControl: true
		};
		var carte = new google.maps.Map(document.getElementById("carte"), options);

		//polyligne
		var parcoursBus = [
			new google.maps.LatLng(46.781367900048, 6.6401992834884),
			new google.maps.LatLng(46.780821285011, 6.6416348016222),
			new google.maps.LatLng(46.780496546047, 6.6421830461926),
			new google.maps.LatLng(46.779835306991, 6.6426765713417),
			new google.maps.LatLng(46.777748677169, 6.6518819126808),
			new google.maps.LatLng(46.778027878803, 6.6541349682533),
			new google.maps.LatLng(46.778484884759, 6.6557324922045),
			new google.maps.LatLng(46.778752327087, 6.6573654211838),
			new google.maps.LatLng(46.778605381016, 6.6588674582321)
		];
	
		var traceParcoursBus = new google.maps.Polyline({
			path: parcoursBus,
			strokeColor: "#FF0000",
			strokeOpacity: 1.0,
			strokeWeight: 2
		});
		
		traceParcoursBus.setMap(carte);

		var marqueur = new google.maps.Marker({
			position: new google.maps.LatLng(46.781367900048, 6.6401992834884),
			map: carte
		});
		var marqueur = new google.maps.Marker({
			position: new google.maps.LatLng(46.778605381016, 6.6588674582321),
			map: carte
		});
	}

	window.onload = initialiser;
