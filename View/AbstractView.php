<?php

abstract class AbstractView
{
	protected $model;
	protected $now;
	
	public function __construct($model)
	{
		$this->model = $model;
	}

	public function translateTimePeriod($period, $week = null)
	{
		switch($period)
		{
			case -1:
				return _('in the future');
			case 0:
				return _('tonight');
			case 1:
				return _('in late afternoon');
			case 2:
				return _('this afternoon');
			case 3:
				return _('around midday');
			case 4:
				return _('this morning');
			case 5:
				return _('early this morning');
			case 6:
				return _('in the night');
			case 7:
				return _('yesterday');
			case 8:
				return ($week) ? $week : _('this week');
			default:
				return _('more than a week');
		}
	}

	public function formateDate($date)
	{
		if (!is_array($date))
		{
			$date = $this->locateDate($date);
		}

		$period = $date[0];

		if ($period < 0 || $period > 8)
		{
			$format = _('%A %d %B %Y à %Hh%M');
		}
		elseif ($period >= 7)
		{
			$format = _('%A à %Hh%M');
		}
		else
		{
			$format = _('%Hh%M');
		}

		return strftime($format, $date[1]);
	}

	public function locateDate($date) {
		// It's interessant to note that it's work fine
		if (is_numeric($date)) {
			$t = intval($date);
		} else {
			$t =  strtotime($date);
		}

		// The time is cached for improve performance
		if (!isset($this->now)) {
			$this->now = time();
		}

		$n = $this->now;

		$an = getdate($n);

		// we start at midnight
		$hour_c = mktime(23, 59, 59, $an['mon'], $an['mday'], $an['year']);

		$hours = array(
			23400, // 1 From 18h30
			36000, // 2 From 14h
			45000, // 3 From 11h30
			57600, // 4 From 8h00
			68400, // 5 From 5h00
			86400, // 6 From 0h0
			172800,// 7 Yesterday
			604800,// 8 This week
			0
		);
		
		// If the date is not in the future
		if ($t <= $n)
		{
			$i;
			$hour = $hour_c;
			for ($i = 0; $t < $hour && $i < 9 ; ++$i)
			{
				$hour = $hour_c - $hours[$i];
			}

			return array($i, $t, ($i === 8) ? strftime('%A', $t) : null);
		}

		// When the date is in the future…
		return array(-1, $t, null);
	}

	public function getMimeIcone($mimetype)
	{

		$m;
		preg_match('/^([^;]*)/', $mimetype, $m);

		$file = str_replace('/', '-', $m[1]);

        if (file_exists("Img/mimes/$file.png"))
        {
                return $file;
        }

        $generics = array('image', 'audio', 'text', 'video', 'package', 'message');

        foreach($generics as $id => $generic)
		{
                if (strpos($mimetype, $generic) !== FALSE)
                {
                        return "$generic-x-generic";
                }
        }

        return 'unknown';
	}
}
?>
