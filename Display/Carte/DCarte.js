var DCarte = function(screen)
{
	if (window['google'] == undefined || google['maps'] == undefined) return alert('Google maps library is required');
	this.screen = screen;

	var options = {
		//center: latlng,
		zoom:2,
		mapTypeId: google.maps.MapTypeId.SATELLITE,
		scaleControl: true
	};

	this.map = new google.maps.Map(screen, options);

	for (var key in this.listeners)
		window.top.addEventListener('i15e.'+key, this.listeners[key]);
};

DCarte.prototype = 
{
	listeners: {
		new_tuples: function(e) {

		},
		add_statement: function(e) {

		},
		del_statement: function(e) {

		},
		layout_change: function(e) {

		}
	}
};