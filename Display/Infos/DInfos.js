var DInfos = function(screen)
{
	var shadow = newDom('div');
	shadow.className = 'shadow';
	var number = newDom('div');
	number.className = 'number';
	var show = document.createTextNode('154.27');
	number.appendChild(show);
	shadow.appendChild(number);
	screen.appendChild(shadow);

	var updateFnc = function(b) {
		show.data = b.time_t;
	};
	EventBus.addListener('time_sync', updateFnc);
	EventBus.addListener('new_tuples', function(e) {
		var l = e.data.length;
		if (l > 0)
			updateFnc(e.data[l-1]);
	});
};

