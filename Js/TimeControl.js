$(document).ready(function(){
	var iframe_mask = newDom('div');
	iframe_mask.id = 'iframe_mask';
	iframe_mask.style.display = 'none';
	document.body.appendChild(iframe_mask);

	var time_control = newDom('div');
	time_control.id = "time_control";
	document.body.appendChild(time_control);

	var time_info = newDom('div');
	time_info.className = 'time_info';
	var time_info_day = newDom('div');
	time_info_day.className = 'day';
	time_info_day.appendChild(document.createTextNode('10'));
	time_info.appendChild(time_info_day);
	var time_info_month_year = newDom('div');
	time_info_month_year.className = 'month year';
	time_info_month_year.appendChild(document.createTextNode('07/90'));
	time_info.appendChild(time_info_month_year);
	var time_info_time = newDom('div');
	time_info_time.className = 'time';
	time_info_time.appendChild(document.createTextNode('12:45:12.054'));
	time_info.appendChild(time_info_time);
	time_control.appendChild(time_info);
	var bti = newDom('button');
	bti.className = 'btn btn-mini btn-danger';
	var bti_icon = newDom('i');
	var bti_icon_play_class = 'icon-play icon-white';
	var bti_icon_pause_class = 'icon-pause icon-white';
	bti_icon.className = bti_icon_play_class;
	bti.appendChild(bti_icon);
	time_control.appendChild(bti);
	var jbti = $(bti);

	var zone_slider = newDom('div');
	zone_slider.className = "zone_slider";
	time_control.appendChild(zone_slider);
	var jslider = $(zone_slider);

	var area = newDom('div');
	area.className = 'area';
	zone_slider.appendChild(area);

	var border_left = newDom('div');
	border_left.className = 'border border_left';
	area.appendChild(border_left);

	var border_right = newDom('div');
	border_right.className = 'border border_right';
	area.appendChild(border_right);

	var curseur = newDom('div');
	curseur.className = 'cursor';
	zone_slider.appendChild(curseur);

	var left_width = 50;
	var right_width = 42;
	var slider_pos = 170;
	var slider_width = jslider.width();
	var drag_margin = 0;

	var draw = function() {
		curseur.style.left = slider_pos - 1 + 'px';

		var left = slider_pos - left_width;
		var width = left_width + right_width;
		if (left < 0) {
			width += left;
			left = 0;
		}
		if (left + width > slider_width) width = slider_width - left;
		left += 'px';
		width += 'px';
		if (area.style.left != left)
			area.style.left =  left;
		if (area.style.width != width)
			area.style.width = width;
	}

	draw();

	$(window).resize(function() {
		var new_width = jslider.width();
		var ratio = new_width / slider_width;
		left_width *= ratio;
		right_width *= ratio;
		slider_pos *= ratio;
		slider_width = new_width;
		draw();
	});

	var ondrag = false;
	var slider_drag = false;
	var border_left_drag = false;
	var border_right_drag = false;
	var slider_left = jslider.position().left;

	var time_min = Number.MAX_VALUE;
	var time_max = Number.MIN_VALUE;

	EventBus.addListener('bounds', function(d) {
		for (var statement_name in d) {
			if (d[statement_name].time_tMin < time_min)
				time_min = d[statement_name].time_tMin;

			if (d[statement_name].time_tMax > time_max)
				time_max = d[statement_name].time_tMax;
		}
	});
	var dragdrop = function(e) {
		if (ondrag)
		{
			var m_x = e.clientX - slider_left - drag_margin;

			if (m_x < 0) m_x = 0;
			else if (m_x > slider_width) m_x = slider_width;

			if (slider_drag)
			{
				slider_pos = m_x;
			}
			else if (border_left_drag)
			{
				left_width = slider_pos - m_x;
				if (left_width < 0) left_width = 0;
			}
			else if (border_right_drag)
			{
				right_width = m_x - slider_pos;
				if (right_width < 0) right_width = 0;
			}
			// draw();


			var time_int = time_max - time_min;
			var time_t = slider_pos / slider_width * time_int + time_min * 1;
			var start_t = time_t - left_width / slider_width * time_int;
			var end_t = time_t + right_width / slider_width * time_int;
			var time_t_date = new Date(time_t);
			var start_t_date = new Date(start_t);
			var end_t_date = new Date(end_t);
			EventBus.send('time_sync', {
				time_t: time_t_date,
				start_t: start_t_date,
				end_t: end_t_date
			});

			date_display =  border_left_drag ? start_t_date :
				border_right_drag ? end_t_date : time_t_date;

		}
	};

	jslider.mousedown(function(e) {
		ondrag = true;
		if (!border_left_drag && !border_right_drag)
		{
			var m_x = e.clientX - slider_left;
			drag_margin = m_x - slider_pos;
			if ((drag_margin < 0 && drag_margin < -left_width) ||
				(drag_margin > 0 && drag_margin > right_width)) drag_margin = 0;

			slider_drag = true;
			dragdrop(e);
		}
		iframe_mask.style.display  = 'block';
	});

	var jdoc = $(document);
	jdoc.mouseup(function() {
		ondrag = false; slider_drag = false;
		border_left_drag = false; border_right_drag = false;
		iframe_mask.style.display  = 'none';
	});

	jdoc.mousemove(dragdrop);
	document.onselectstart = function(){ return !ondrag;};

	$(border_left).mousedown(function(e) {
		ondrag = true; slider_drag = false; border_left_drag = true;
		drag_margin = e.clientX - slider_left - slider_pos + left_width;
		dragdrop(e);
		iframe_mask.style.display  = 'block';
	});

	$(border_right).mousedown(function(e) {
		ondrag = true; slider_drag = false; border_right_drag = true;
		drag_margin = e.clientX - slider_left - slider_pos - right_width;
		dragdrop(e);
		iframe_mask.style.display  = 'block';
	});

	var play_interval = -1;
	jbti.click(function() {
		if (bti_icon.className == bti_icon_play_class)
		{
			bti_icon.className = bti_icon_pause_class;
			var start_t = time_min * 1;
			play_interval = window.setInterval(function() {
				start_t += 5000;
				var date = new Date(start_t);
				EventBus.send('time_sync', {
					time_t: date,
					end_t: date,
					start_t: time_min
				});
			}, 128);
		}
		else
		{
			window.clearInterval(play_interval);
			bti_icon.className = bti_icon_play_class;
		}
	});

	EventBus.addListener('time_sync', function(d, obj){
		var time_t = d.time_t * 1;
		var time_int = time_max - time_min;
		slider_pos = (time_t - time_min) * (slider_width / time_int);

		if (d.start_t)
			left_width = (time_t - d.start_t) / time_int * slider_width;

		if (d.end_t)
			right_width = (d.end_t - time_t) / time_int * slider_width;

		draw();

		var date_display = d.time_t;
		time_info_day.firstChild.data = date_display.getDate();
		time_info_month_year.firstChild.data = date_display.getMonth()+'/'
			+date_display.getFullYear().toString().substring(2);

		var hours = date_display.getHours().toString();
		if (hours.length < 2) hours = '0'+hours;
		var mins = date_display.getMinutes().toString();
		if (mins.length < 2) mins = '0'+mins;
		var seconds = date_display.getSeconds().toString();
		if (seconds.length < 2) seconds = '0'+seconds;
		var mili = date_display.getMilliseconds().toString();
		while(mili.length < 3) mili = '0'+mili;

		time_info_time.firstChild.data =
			hours+':'+ mins + ':' + seconds + '.' + mili;
	}, this);

});