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
	time_info.appendChild(document.createTextNode('12:45:12.054'));
	time_control.appendChild(time_info);
	var bti = newDom('button');
	bti.className = 'btn btn-mini btn-inverse';
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

	var left_pos = 50;
	var right_pos = 120;
	var slider_left = jslider.position().left;
	var slider_width = jslider.width();
	var drag_margin = 0;

	var draw = function() {
		// curseur.style.left = slider_pos - 1 + 'px';

		var left = left_pos + 'px';
		var right = slider_width - right_pos + 'px';
		if (area.style.left != left)
			area.style.left =  left;
		if (area.style.right != right)
			area.style.right = right;
	}

	draw();

	$(window).resize(function() {
		slider_left = jslider.position().left;
		var new_width = jslider.width();
		var ratio = new_width / slider_width;
		left_pos *= ratio;
		right_pos *= ratio;
		slider_width = new_width;
		draw();
	});

	var ondrag = false;
	var slider_drag = false;
	var border_left_drag = false;
	var border_right_drag = false;

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

	var manage_new_position = function() {
		var t = time_min * 1;
		var time_int = time_max - time_min;
		var start_t = t + left_pos / slider_width * time_int;
		var end_t = t + right_pos / slider_width * time_int;
		var start_t_date = new Date(start_t);
		var end_t_date = new Date(end_t);

		EventBus.send('time_sync', {
			start_t: start_t_date,
			end_t: end_t_date
		});
	};

	var drag_width = 0;
	var dragdrop = function(e) {
		if (ondrag)
		{
			var m_x = e.clientX - slider_left - drag_margin;

			if (m_x < 0) m_x = 0;
			else if (m_x > slider_width) m_x = slider_width;

			if (slider_drag)
			{
				left_pos = m_x;
				right_pos = m_x + drag_width;
				if (right_pos > slider_width) right_pos = slider_width;
			}
			else if (border_left_drag)
			{
				left_pos = m_x;
				if (left_pos > right_pos) right_pos = left_pos;
			}
			else if (border_right_drag)
			{
				right_pos = m_x;
				if (right_pos < left_pos) left_pos = right_pos;
			}
			// draw();
			manage_new_position();
		}
	};

	jslider.mousedown(function(e) {
		ondrag = true;
		if (!border_left_drag && !border_right_drag)
		{
			var m_x = e.clientX - slider_left - left_pos;
			drag_margin = m_x;
			drag_width = right_pos - left_pos;

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
		drag_margin = e.clientX - slider_left - left_pos;
		dragdrop(e);
		iframe_mask.style.display  = 'block';
	});

	$(border_right).mousedown(function(e) {
		ondrag = true; slider_drag = false; border_right_drag = true;
		drag_margin = e.clientX - slider_left - right_pos;
		dragdrop(e);
		iframe_mask.style.display  = 'block';
	});

	var play_interval = -1;
	jbti.click(function() {
		if (bti_icon.className == bti_icon_play_class)
		{
			bti_icon.className = bti_icon_pause_class;
			// var time_int = time_max - time_min;
			// var begin_t = left_pos / slider_width * time_int + time_min * 1;
			// var
			play_interval = window.setInterval(function() {
				// begin_t += 5000;
				// time_int = time_max - time_min;
				if (left_pos < slider_width && right_pos < slider_width)
				{
					left_pos += 1;
					right_pos += 1;
				}
				else
				{
					jbti.click();
				}
				// left_pos = (begin_t - time_min) * (slider_width / time_int);

				manage_new_position();
			}, 128);
		}
		else
		{
			window.clearInterval(play_interval);
			bti_icon.className = bti_icon_play_class;
		}
	});

	EventBus.addListener('time_sync', function(d, obj){
		var time_int = time_max - time_min;
		left_pos = (d.start_t - time_min) * (slider_width / time_int);
		right_pos = (d.end_t - time_min) * (slider_width / time_int);

		draw();

		var date_display = d.start_t;

		var hours = date_display.getHours().toString();
		if (hours.length < 2) hours = '0'+hours;
		var mins = date_display.getMinutes().toString();
		if (mins.length < 2) mins = '0'+mins;
		var seconds = date_display.getSeconds().toString();
		if (seconds.length < 2) seconds = '0'+seconds;
		var mili = date_display.getMilliseconds().toString();
		while(mili.length < 3) mili = '0'+mili;

		time_info.firstChild.data =
			hours+':'+ mins + ':' + seconds + '.' + mili;
	}, this);

});