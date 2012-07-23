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

	EventBus.addListener('time_sync', function(d) {
		show.data = b.time_t;
	});
};

