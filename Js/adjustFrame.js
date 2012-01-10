var frame = false;

var adjustSize = function()
{
	if (!frame)
	{
		frame = document.getElementById('html_view');
	};
	
	if (frame)
	{
		var height = frame.contentWindow.document.body.scrollHeight + 'px';

		if (frame.style.height != height)
		{
			frame.style.height = height;
		};

		var width = frame.contentWindow.document.body.scrollWidth + 'px';

		if (frame.style.width != width)
		{
			frame.style.width = width;
		};

		if (frame.contentWindow.document.body.style.overflowY != 'hidden')
		{
			frame.contentWindow.document.body.style.overflowY = 'hidden';
		};
		
		if (frame.contentWindow.document.body.style.overflowX != 'hidden')
		{
			frame.contentWindow.document.body.style.overflowX = 'hidden';
		};
	};
};

adjustSize();

var interval = window.setInterval(adjustSize, 200);

window.onload = function()
{
	if (!frame)
	{
		window.clearInterval(interval);
	}
	adjustSize();
};
