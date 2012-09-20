/* This file is released under the CeCILL-B V1 licence.*/

var DVideo = function(screen)
{
	var obj = this;

	// Graph area
	this.screen = screen;

	this.video = newDom('video');
	// this.video.setAttribute('controls', 'true');
	this.video.setAttribute('preload', 'auto');
	this.screen.appendChild(this.video);

	this.progressArea = newDom('div', 'progress progress-striped active');
	this.progressBar = newDom('div', 'bar');
	this.progressBar.style.width = '0%';
	this.progressArea.appendChild(this.progressBar);
	var divProgressArea = newDom('div', 'progress-area fade in');
	divProgressArea.appendChild(this.progressArea);
	this.screen.appendChild(divProgressArea);

	// Hide the progressbar when no updates
	this.progressBarHideTimeout = 0;
	var hideProgressBarCallback = function()
	{
		divProgressArea.className = 'progress-area fade';
	};

	this.canplay = false;

	this.database = {};
	EventBus.addListeners(this.listeners, this);

	var jvideo = $(this.video);
	jvideo.bind('canplay', function() {
		obj.canplay = true;
		// obj.progressArea.className = 'progress';
		jvideo.trigger('progress');
	});
	jvideo.bind('progress', function() {
		if (obj.canplay)
		{
			window.clearTimeout(obj.progressBarHideTimeout);
			divProgressArea.className = 'progress-area fade in';

			var ratio = obj.video.buffered.end(0) / obj.video.duration;

			// If the video is loaded, we doen't need to update the progression anymore
			// (progress events are sended even after the load was completed)
			if (ratio == 1.0)
			{
				jvideo.unbind('progress');
				obj.progressArea.className = 'progress progress-striped active progress-success';
				var hideTimeout = 2000;
			}
			else
				var hideTimeout = 10000;

			obj.progressBarHideTimeout =
				window.setTimeout(hideProgressBarCallback, hideTimeout);

			obj.progressBar.style.width = ratio * 100 + '%';
		}
		// console.log(
	});
};

DVideo.prototype.listeners = {
	video: function(d, obj) {
		obj.canplay = false;
		obj.time_synchro = d.start_t;
		obj.video.setAttribute('src', d.location);
	},

	cursor: function(d, obj) {
		if (obj.canplay)
		{
			var time = d.time_t - obj.time_synchro;

			var set_time = obj.video.paused;

			// If the video is playing
			if (!set_time)
			{
				// Detect if the difference between the video and the time is
				// too high
				var diff = obj.video.currentTime - time;
				if (diff > 0.5 || diff < -0.5)
					set_time = true;
			}

			if (set_time && !isNaN(time) && time >= 0.0 && time <= obj.video.duration)
			{
				obj.video.currentTime = time;
				if (time > obj.video.buffered.end(0))
					EventBus.send('pause');
			}
		}
	},

	add_statement: function(e, obj) {
		if (e.box_name != self.name) return;

		if (!(e.statement_name in obj.database))
			obj.database[e.statement_name] = true;
	},
	del_statement: function(e, obj) {
		if (e.box_name != self.name) return;

		if (e.statement_name in obj.database)
			delete obj.database[e.statement_name];
	},
	size_change: function(e, obj)
	{
		var width = $(obj.screen).width();
		var height = $(obj.screen).height();
	},
	play: function(e, obj)
	{
		if (obj.canplay && obj.video.paused)
			obj.video.play();
	},
	pause: function(e, obj)
	{
		if (obj.canplay && !obj.video.paused)
			obj.video.pause();
	},

	play_speed: function(d, obj)
	{
		obj.video.playbackRate = d.speed;
	}
};
