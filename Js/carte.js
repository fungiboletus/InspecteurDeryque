	function initialiser() {
		var latlng = new google.maps.LatLng((46.781367900048+46.778605381016)/2, (6.6401992834884+6.6588674582321)/2);
		//objet contenant des propriétés avec des identificateurs prédéfinis dans Google Maps permettant
		//de définir des options d'affichage de notre carte
		var options = {
			center: latlng,
			zoom: 16,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scaleControl: true
		};
		
		//constructeur de la carte qui prend en paramêtre le conteneur HTML
		//dans lequel la carte doit s'afficher et les options
		var carte = new google.maps.Map(document.getElementById("carte"), options);

		//test plyligne
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
			path: parcoursBus,//chemin du tracé
			strokeColor: "#FF0000",//couleur du tracé
			strokeOpacity: 1.0,//opacité du tracé
			strokeWeight: 2//grosseur du tracé
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
